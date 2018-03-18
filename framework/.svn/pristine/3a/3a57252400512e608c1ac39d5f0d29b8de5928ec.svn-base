<?php

/**
 * Class clientWithdrawByPartnerTradingClass
 * 客人储蓄账户转账到合作银行或机构
 */
class clientWithdrawByPartnerTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $partner_info;
    private $amount;
    private $currency;
    private $trading_fee;
    private $client_fee;

    public function __construct($clientSavingsPassbook, $partnerId, $amount, $currency, $trading_fee = 0.0, $client_fee = 0.0)
    {
        $partner_model = new partnerModel();
        $partner_info = $partner_model->getRow($partnerId);

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->partner_info = $partner_info;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->trading_fee = $trading_fee;
        $this->client_fee = $client_fee;
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
            'trading_type' => "withdraw_by_partner",
            'subject' => 'Withdraw by ' . $this->partner_info->partner_name
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
        $passbook_partner = passbookClass::getPartnerPassbook($this->partner_info->uid);

        // 构建detail
        // 客人储蓄账户 - 借
        $detail[]=$this->createTradingDetailItem(
            $this->client_savings_passbook,
            $this->amount + $this->client_fee,
            $this->currency,
            accountingDirectionEnum::DEBIT);

        // 手续费收入 - 贷
        if ($this->client_fee > 0) {
            $detail[]=$this->createTradingDetailItem(
                passbookClass::getSystemPassbook(systemAccountCodeEnum::FEE_INCOMING),
                $this->client_fee,
                $this->currency,
                accountingDirectionEnum::CREDIT
            );
        }

        // partner结算账户 - 贷
        $detail[]=$this->createTradingDetailItem(
            $passbook_partner,
            $this->amount + $this->trading_fee,
            $this->currency,
            accountingDirectionEnum::CREDIT);

        // 财务费用 - 借
        if ($this->trading_fee > 0) {
            $detail[]=$this->createTradingDetailItem(
                passbookClass::getSystemPassbook(systemAccountCodeEnum::FINANCIAL_EXPENSES),
                $this->trading_fee,
                $this->currency,
                accountingDirectionEnum::DEBIT
            );
        }

        return $detail;
    }
}