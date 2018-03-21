<?php

class headquarterToBankTradingClass extends tradingClass {
    private $bank_account_id;
    private $amount;
    private $currency;

    public function __construct($bankAccountId, $amount, $currency)
    {
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
            'trading_type' => "headquarter_to_bank",
            'subject' => 'Headquarter Deposit To Bank'
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
        $passbook_bank = passbookClass::getBankAccountPassbook($this->bank_account_id);
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);

        // 构建detail
        // 银行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // HIV账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}