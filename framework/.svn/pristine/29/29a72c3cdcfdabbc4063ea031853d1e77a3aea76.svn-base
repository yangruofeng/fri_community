<?php

class clientToClientTradingClass extends tradingClass {
    private $from_client_savings_passbook;
    private $to_client_savings_passbook;
    private $amount;
    private $currency;

    public function __construct($fromClientSavingsPassbook, $toClientSavingsPassbook, $amount, $currency)
    {
        $this->from_client_savings_passbook = $fromClientSavingsPassbook;
        $this->to_client_savings_passbook = $toClientSavingsPassbook;
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
            'trading_type' => "transfer",
            'subject' => 'Transfer'
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

        // 构建detail
        // 转出客人储蓄账户 - 借
        $detail[]=$this->createTradingDetailItem($this->from_client_savings_passbook,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 转入客人储蓄账户 - 贷
        $detail[]=$this->createTradingDetailItem($this->to_client_savings_passbook,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}