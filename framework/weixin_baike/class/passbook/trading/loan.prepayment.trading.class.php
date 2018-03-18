<?php

class loanPrepaymentTradingClass extends tradingClass {

    private $total_amount;
    private $principal;
    private $interest;
    private $penalty;
    private $prepayment_fee;
    private $contract_info;
    private $product_info;
    private $loan_account;
    private $currency;

    public function __construct($contract_id,$total_amount,$principal,$interest,$penalty,$prepayment_fee, $currency)
    {
        //$apply_model = new loan_prepayment_applyModel();
        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();


        $contract_info = $contract_model->getRow($contract_id);
        $product_info = $product_model->getRow($contract_info->product_id);
        $account_info = $account_model->getRow($contract_info->account_id);


        $this->total_amount = $total_amount;
        $this->principal = $principal;
        $this->interest = $interest;
        $this->penalty = $penalty;
        $this->prepayment_fee = $prepayment_fee;
        $this->contract_info = $contract_info;
        $this->product_info=$product_info;
        $this->loan_account=new loan_accountClass($account_info);
        $this->currency = $currency;
    }

    /**
     * 获取交易的主要信息
     * @return array (
     *  category
     *  trading_type
     *  subject
     *  remark
     *  is_outstanding
     * )
     */
    protected function getTradingInfo()
    {
        return array(
            'trading_type' => "loan_prepayment",
            'subject' => 'Loan Prepayment: ' . $this->product_info->product_name . " - " . $this->contract_info->contract_sn
        );
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_receivable = ($this->contract_info->loan_term_day <= 365) ?
            passbookClass::getShortLoanPassbookOfLoanAccount($this->loan_account) :
            passbookClass::getLongLoanPassbookOfLoanAccount($this->loan_account); // 一年（含）以下是短期贷款，一年以上是长期贷款
        $passbook_client = passbookClass::getSavingsPassbookOfLoanAccount($this->loan_account);   // loan account对应的储蓄账户

        // 准备业务类型
        if ($this->product_info->is_credit_loan) {
            $business_type = businessTypeEnum::CREDIT_LOAN;
        } else {
            throw new Exception("Not supported now");
        }

        // 构建detail
        $currency = $this->contract_info->currency;
        $total_amount = $this->total_amount;

        // 如果还款货币与贷款合同货币不同，需要换汇结算户参与
        if ($this->currency != $currency) {
            // 获取还款货币买入合同货币的当前设置汇率
            $exchange_rate = global_settingClass::getCurrencyRateBetween($this->currency, $currency);
            // 计算还款货币的金额
            $real_amount = round($total_amount / $exchange_rate, 2);

            // 活期存款 - 借
            $detail[] = $this->createTradingDetailItem(
                $passbook_client,
                $real_amount,
                $this->currency,
                accountingDirectionEnum::DEBIT);

            // 还款货币的换汇结算户 - 贷
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                $real_amount,
                $this->currency,
                accountingDirectionEnum::CREDIT);

            // 合同货币的换汇结算户 - 借
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                $total_amount,
                $currency,
                accountingDirectionEnum::DEBIT);
        } else {
            // 活期存款 - 借
            $detail[] = $this->createTradingDetailItem(
                $passbook_client,
                $total_amount,
                $this->currency,
                accountingDirectionEnum::DEBIT);
        }

        // 应收贷款科目 - 贷
        $detail[] = $this->createTradingDetailItem(
            $passbook_receivable,
            $this->principal,
            $currency,
            accountingDirectionEnum::CREDIT);

        // 应收利息 - 贷
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getSystemPassbook(systemAccountCodeEnum::RECEIVABLE_LOAN_INTEREST),
            $this->interest,
            $currency,
            accountingDirectionEnum::CREDIT);


        // 罚金收入 - 贷
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getIncomingPassbook(incomingTypeEnum::OVERDUE_PENALTY, $business_type),
            $this->penalty,
            $currency,
            accountingDirectionEnum::CREDIT);

        // 提前还款违约金收入 - 贷
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getIncomingPassbook(incomingTypeEnum::OVERDUE_PENALTY, $business_type),
            $this->prepayment_fee,
            $currency,
            accountingDirectionEnum::CREDIT);

        return $detail;
    }
}