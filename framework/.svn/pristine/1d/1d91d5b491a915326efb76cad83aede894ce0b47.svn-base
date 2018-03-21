<?php

class memberWithdrawByBankTradingClass extends clientWithdrawByBankTradingClass {
    /**
     * memberWithdrawByBankTradingClass constructor.
     * @param int $memberId 会员ID
     * @param int $bankAccountId  转出银行银行账户
     * @param float $amount 金额
     * @param string $currency 货币
     * @param float $trading_fee 交易费用，公司付出的
     * @param float $client_fee 取现手续费，客人付出的
     */
    public function __construct($memberId, $bankAccountId, $amount, $currency, $trading_fee = 0.0, $client_fee = 0.0)
    {
        $member_passbook = passbookClass::getOrCreatePassbookByObjGuid(memberClass::getInstanceByID($memberId)->getSavingsGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
        parent::__construct($member_passbook, $bankAccountId, $amount, $currency, $trading_fee, $client_fee);
    }
}