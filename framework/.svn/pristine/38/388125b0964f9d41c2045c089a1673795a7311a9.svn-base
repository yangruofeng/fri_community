<?php

class loanDeductTradingClass extends tradingClass {
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
            'subject' => 'Loan Deducting'
        );
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_client = passbookClass::getSavingsPassbookOfLoanAccount($this->loan_account);   // loan account对应的储蓄账户

        // 构建detail
        $currency = $this->contract_info->currency;

        // 准备业务类型
        if ($this->product_info->is_credit_loan) {
            $business_type = businessTypeEnum::CREDIT_LOAN;
        } else {
            throw new Exception("Not supported now");
        }
        // 具体收入账户的passbook，用到才创建

        // 总费用
        $total_fee = 0;
        // 年费收入 - 贷
        if ($this->scheme_info->deduct_annual_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::ANNUAL_FEE, $business_type),
                $this->scheme_info->deduct_annual_fee,
                $currency,
                accountingDirectionEnum::CREDIT);
            $total_fee += $this->scheme_info->deduct_annual_fee;
        }

        // 应收利息 - 贷
        if ($this->scheme_info->deduct_interest > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getSystemPassbook(systemAccountCodeEnum::RECEIVABLE_LOAN_INTEREST),
                $this->scheme_info->deduct_interest,
                $currency,
                accountingDirectionEnum::CREDIT);
            $total_fee += $this->scheme_info->deduct_interest;
        }

        // 管理费收入 - 贷
        if ($this->scheme_info->deduct_admin_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::ADMIN_FEE, $business_type),
                $this->scheme_info->deduct_admin_fee,
                $currency,
                accountingDirectionEnum::CREDIT);
            $total_fee += $this->scheme_info->deduct_admin_fee;
        }

        // 手续费收入 - 贷
        if ($this->scheme_info->deduct_loan_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::LOAN_FEE, $business_type),
                $this->scheme_info->deduct_loan_fee,
                $currency,
                accountingDirectionEnum::CREDIT);
            $total_fee += $this->scheme_info->deduct_loan_fee;
        }

        // 营运费每期还款收，这里不扣

        // 保险费收入 - 贷
        if ($this->scheme_info->deduct_insurance_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::INSURANCE_FEE, $business_type),
                $this->scheme_info->deduct_insurance_fee,
                $currency,
                accountingDirectionEnum::CREDIT
            );
            $total_fee += $this->scheme_info->deduct_insurance_fee;
        }

        // 活期存款 - 借
        if ($total_fee > 0) {
            $detail[] = $this->createTradingDetailItem($passbook_client, $total_fee, $currency, accountingDirectionEnum::DEBIT);
        }

        return $detail;
    }
}