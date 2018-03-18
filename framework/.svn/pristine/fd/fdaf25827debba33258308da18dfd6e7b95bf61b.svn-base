<?php

class bankToBranchTradingClass extends tradingClass {
    private $branch_id;
    private $bank_account_id;
    private $amount;
    private $currency;

    public function __construct($bankAccountId, $branchId, $amount, $currency)
    {
        $this->branch_id = $branchId;
        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
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
            'trading_type' => "bank_to_branch",
            'subject' => 'Branch Withdraw From Bank'
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
        $passbook_branch = passbookClass::getBranchPassbook($this->branch_id);
        $passbook_bank = passbookClass::getBankAccountPassbook($this->bank_account_id);

        // 构建detail
        // 分行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 银行账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}