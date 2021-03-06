<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 13:28
 */
class phoneCodeVerifyApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Phone Code Verify";
        $this->description = "短信验证码验证";
        $this->url = C("bank_api_url") . "/phone.code.verify.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("verify_id", "验证ID", '1', true);
        $this->parameters[]= new apiParameter("verify_code", "验证码", '236541', true);
        $this->parameters[] = new apiParameter('is_certificate','是否会员手机号认证，是 1，否 0',0);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}