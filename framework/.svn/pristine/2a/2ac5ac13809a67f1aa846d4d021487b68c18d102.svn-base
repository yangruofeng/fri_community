<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:18
 */

abstract class bank_apiControl{

    protected $appId;
    protected $appKey;

    public function __construct()
    {

    }


    /**
     * app使用api检查token
     * @return result
     */
    protected function checkToken($app='member')
    {
        $params = array_merge(array(),$_GET,$_POST);
        if( empty($params['token']) ){
            return new result(false,'Invalid token',null,errorCodesEnum::NO_LOGIN);
        }
        $m_member_token = new member_tokenModel();
        $token = $m_member_token->orderBy('uid desc')->getRow(array(
            'token' => $params['token']
        ));
        if( !$token ){
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }
        // 检查过期时间,超过12小时重新登录
        if( ( strtotime($token['create_time']) + 12*3600 ) < time() ){
            $token->delete();
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }

        return new result(true);
    }


    protected function checkOperator()
    {
        $params = array_merge(array(),$_GET,$_POST);
        if( empty($params['token']) ){
            return new result(false,'Invalid token',null,errorCodesEnum::NO_LOGIN);
        }
        $m_token = new um_user_tokenModel();
        $token = $m_token->orderBy('uid desc')->getRow(array(
            'token' => $params['token']
        ));
        if( !$token ){
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }
        return new result(true);
    }

    protected function checkAppSign()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $key = getConf('app_secret_key');

        if ( $this->sign($params,$key) != $params['sign'] ) {
            return new result(false, "Sign error", null, errorCodesEnum::SIGN_ERROR);
        }

        return new result(true);
    }



    /**
     * 强验证api检查签名
     * @return result
     */
    protected function checkSign()
    {

        $api_config = getConf('api_config');
        if( !$api_config ){
            return new result(false,'Api config not exist!',null,errorCodesEnum::CONFIG_ERROR);
        }
        $this->appId = $api_config['appId'];
        $this->appKey = $api_config['appKey'];

        $params = array_merge(array(),$_GET,$_POST);

        if ( $this->sign($params,$this->appKey) != $params['sign'] ) {
            return new result(false, "Sign error", null, errorCodesEnum::SIGN_ERROR);
        }

        return new result(true);


    }

    protected function sign($parameters,$key)
    {

        $parameters = array_ksort($parameters);
        $segments = array();
        foreach ($parameters as $k=>$v) {
            if ($k == "sign_type") continue;
            if ($k == "sign") continue;
            if ($k == "act") continue;
            if ($k == "op") continue;
            if ($k == "yoajax") continue;
            if ($k == "_s") continue;
            if ($v === null || $v === "") continue;
            $segments[]="$k=$v";
        }

        return md5(join("&", $segments).$key);
    }
}




