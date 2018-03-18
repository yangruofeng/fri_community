<?php

class capitalReceiveTradingClass extends tradingClass {
    private $amount;
    private $currency;

    public function __construct($amount, $currency)
    {
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
            'trading_type' => "receive_capital",
            'subject' => 'Receive Capital'
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
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);
        $passbook_capital = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CAPITAL);

        // 构建detail

        // HIV账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // capital - 贷
        $detail[]=$this->createTradingDetailItem($passbook_capital,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}