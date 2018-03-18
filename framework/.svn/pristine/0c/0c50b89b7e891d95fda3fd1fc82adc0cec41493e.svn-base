<?php

class memberDepositByBankTradingClass extends clientDepositByBankTradingClass {
    public function __construct($memberId, $bankAccountId, $amount, $currency)
    {
        $member_passbook = passbookClass::getOrCreatePassbookByObjGuid(memberClass::getInstanceByID($memberId)->getSavingsGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
        parent::__construct($member_passbook, $bankAccountId, $amount, $currency);
    }
}