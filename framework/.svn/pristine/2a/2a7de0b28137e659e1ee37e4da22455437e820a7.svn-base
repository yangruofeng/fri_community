<?php

class clientDepositByCashTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $cashier_user_id;
    private $amount;
    private $currency;

    public function __construct($clientSavingsPassbook, $cashierUserId, $amount, $currency)
    {
        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->cashier_user_id = $cashierUserId;
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
            'trading_type' => "deposit_by_cash",
            'subject' => 'Deposit'
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
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        // 构建detail
        // 客人储蓄账户 - 贷
        $detail[]=$this->createTradingDetailItem($this->client_savings_passbook,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);
        // cashier账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_cashier,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);

        return $detail;
    }
}