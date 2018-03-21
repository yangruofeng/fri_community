<?php

class branchInitTradingClass extends tradingClass {
    private $branch_id;
    private $amount;
    private $currency;

    public function __construct($branchId, $amount, $currency)
    {
        $this->branch_id = $branchId;
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
            'trading_type' => "init_branch",
            'subject' => 'Init Branch'
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
        $passbook_init = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_INIT);

        // 构建detail
        // 分行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 系统账户init - 贷
        $detail[]=$this->createTradingDetailItem($passbook_init,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}