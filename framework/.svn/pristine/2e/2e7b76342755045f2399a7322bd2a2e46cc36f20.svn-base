<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 17:50
 */
class asiaweiluyClass
{
    function __construct()
    {
    }


    /** 验证是否ACE会员,如 +86-18902461905
     * @param $accountAce
     * @return result
     */
    public static function verifyAceAccount($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->verifyClientAccount($accountAce);
        if( !$re->STS ){
            return new result(false,'Api error',null,errorCodesEnum::API_FAILED);
        }
        $data = $re->DATA;
        $is_member = 0;
        if( $data['flag_ace_member'] == 1 ){
            $is_member = 1;
        }
        return new result(true,'success',$is_member);
    }


    /** 绑定账号发送验证码  如 +86-18902461905
     * @param $accountAce
     * @return result
     */
    public static function bindAccountSendVerifyCode($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->bindStart($accountAce);
        if( !$re->STS ){
            return new result(false,'Api error',null,errorCodesEnum::API_FAILED);
        }
        $data = $re->DATA;
        return new result(true,'success',array(
            'verify_id' => $data['sign_id'],
            'phone_id' => $accountAce
        ));
    }


    /** 绑定账号验证验证码
     * @param $verify_id
     * @param $code
     * @return result
     */
    public static function bindAccountCheckVerifyCode($verify_id,$code)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->bindFinish($verify_id,$code);
        if( !$re->STS ){
            return new result(false,'Api error',null,errorCodesEnum::API_FAILED);
        }
        $ok = 0;
        $data = $re->DATA;
        if( $data['flag_success'] == 1 ){
            $ok = 1;
        }
        return new result(true,'success',$ok);
    }


}