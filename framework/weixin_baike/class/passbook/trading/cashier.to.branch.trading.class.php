<?php

class cashierToBranchTradingClass extends tradingClass {
    private $branch_id;
    private $cashier_user_id;
    private $amount;
    private $currency;

    public function __construct($cashierUserId, $branchId, $amount, $currency)
    {
        $this->branch_id = $branchId;
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
            'trading_type' => "cashier_to_branch",
            'subject' => 'Cashier To Branch Manager',
            'is_outstanding' => 1  // 需要确认
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
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        // 构建detail
        // 分行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 出纳账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_cashier,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}