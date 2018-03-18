<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/31
 * Time: 10:05
 */
class asiaweilulyControl extends bank_apiControl
{
    function testOp()
    {
        $ace_api = new asiaweiluyApi();
        return  $ace_api->test();
    }

    function verifyClientMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $aceAccount = trim($params['account']);
        if( !$aceAccount ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $re = asiaweiluyClass::verifyAceAccount($aceAccount);
        return $re;
    }

    function bindAceAccountStartOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone = $params['phone'];

        if( !$country_code || !$phone ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $aceAccount = '+'.trim($country_code).'-'.trim($phone);
        $chk = asiaweiluyClass::verifyAceAccount($aceAccount);
        if( !$chk->STS ){
            return $chk;
        }

        $is = $chk->DATA;
        if( !$is ){
            return new result(false,'Not ace member',null,errorCodesEnum::ACE_ACCOUNT_NOT_EXIST);
        }

        $re = asiaweiluyClass::bindAccountSendVerifyCode($aceAccount);
        return $re;
    }


}