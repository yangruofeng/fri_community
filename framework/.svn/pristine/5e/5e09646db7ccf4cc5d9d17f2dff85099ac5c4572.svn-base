<?php

class memberToMemberTradingClass extends clientToClientTradingClass {
    public function __construct($fromMemberId, $toMemberId, $amount, $currency)
    {
        $from_member_passbook = passbookClass::getOrCreatePassbookByObjGuid(memberClass::getInstanceByID($fromMemberId)->getSavingsGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
        $to_member_passbook = passbookClass::getOrCreatePassbookByObjGuid(memberClass::getInstanceByID($toMemberId)->getSavingsGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
        parent::__construct($from_member_passbook, $to_member_passbook, $amount, $currency);
    }
}