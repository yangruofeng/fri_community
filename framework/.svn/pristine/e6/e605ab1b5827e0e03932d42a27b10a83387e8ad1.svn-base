<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/12
 * Time: 10:56
 */
class member_handlerClass
{


    /** 获得handler信息
     * @param $handler_id
     * @return mixed|null
     */
    public static function getHandlerInfoById($handler_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->getRow($handler_id);
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    /** 贷款默认的收放款账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultLoanHandler($member_id)
    {
        return self::getMemberDefaultAceHandlerInfo($member_id);
    }


    /** 获取会员默认绑定的ACE账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultAceHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }

    /** 获取会员默认绑定的储蓄账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultPassbookHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PASSBOOK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    /** 获取会员默认绑定的贷款操作账户（贷款作为一个特殊的操作账户）
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultPartnerLoanHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_LOAN,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    /** 获取会员默认绑定的现金账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultCashHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::CASH,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    /** 获取会员默认绑定的合作银行账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultBankHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    public static function getMemberBindBankList($member_id)
    {
        $m = new member_account_handlerModel();
        $list = $m->select(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( count($list) < 1 ){
            return null;
        }

        $return = array();

        foreach( $list as $bank ){
            $temp = array();
            $temp['uid'] = $bank['uid'];
            $bank_info = @json_decode($bank['handler_property'],true);

            $temp['handler_name'] = maskInfo($bank['handler_name']);
            $temp['handler_account'] = '**** **** **** '.substr($bank['handler_account'],-4);
            $temp['handler_phone'] = maskInfo($bank['handler_phone']);
            $logo_url = global_settingClass::getBankLogoByBankCode($bank_info['bank_code']);
            $temp['bank_logo'] = $logo_url;
            $temp['bank_name'] = $bank_info['bank_name'];
            $temp['bank_currency'] = $bank_info['currency'];
            $temp['bank_code'] = $bank_info['bank_code'];
            $temp['bank_detail_info'] = $bank_info;
            $return[] = $temp;
        }
        return $return;

    }

}