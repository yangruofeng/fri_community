<?php

class loanDisburseTradingClass extends tradingClass {
    private $scheme_info;
    private $contract_info;
    private $product_info;
    private $loan_account;

    public function __construct($schemeInfo)
    {
        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();

        $contract_info = $contract_model->getRow($schemeInfo->contract_id);
        $product_info = $product_model->getRow($contract_info->product_id);
        $account_info = $account_model->getRow($contract_info->account_id);

        $this->scheme_info=$schemeInfo;
        $this->contract_info=$contract_info;
        $this->product_info=$product_info;
        $this->loan_account=new loan_accountClass($account_info);
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
            'trading_type' => "disburse_loan",
            'subject' => 'Loan Disbursement: ' . $this->product_info->product_name . " - " . $this->contract_info->contract_sn
        );
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array
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

        // 应收贷款科目 - 借
        $detail[] = $this->createTradingDetailItem($passbook_receivable, $this->scheme_info->principal, $currency, accountingDirectionEnum::DEBIT);
        // 活期存款 - 贷
        $detail[] = $this->createTradingDetailItem($passbook_client, $this->scheme_info->principal, $currency, accountingDirectionEnum::CREDIT);

        // 应收利息 - 借
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getSystemPassbook(systemAccountCodeEnum::RECEIVABLE_LOAN_INTEREST),
            $this->contract_info->receivable_interest,
            $currency,
            accountingDirectionEnum::DEBIT
        );

        // 利息收入 - 贷
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getIncomingPassbook(incomingTypeEnum::INTEREST, $business_type),
            $this->contract_info->receivable_interest,
            $currency,
            accountingDirectionEnum::CREDIT
        );

        return $detail;
    }
}