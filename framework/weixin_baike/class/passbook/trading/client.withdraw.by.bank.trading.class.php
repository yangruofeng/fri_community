<?php

/**
 * Class clientWithdrawByBankTradingClass
 * 客户转账到非合作银行账户
 */
class clientWithdrawByBankTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $bank_account_id;
    private $amount;
    private $currency;
    private $trading_fee;
    private $client_fee;

    /**
     * clientWithdrawByBankTradingClass constructor.
     * @param passbookClass $clientSavingsPassbook 客户储蓄账户passbook
     * @param int $bankAccountId  转出银行银行账户
     * @param float $amount 金额
     * @param string $currency 货币
     * @param float $trading_fee 交易费用，公司付出的
     * @param float $client_fee 取现手续费，客人付出的
     */
    public function __construct($clientSavingsPassbook, $bankAccountId, $amount, $currency, $trading_fee = 0.0, $client_fee = 0.0)
    {
        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->bank_account_id = $bankAccountId;
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
            'trading_type' => "withdraw_by_bank",
            'subject' => 'Withdraw'
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

        // 银行账户 - 贷
        $detail[]=$this->createTradingDetailItem(
            $passbook_bank,
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