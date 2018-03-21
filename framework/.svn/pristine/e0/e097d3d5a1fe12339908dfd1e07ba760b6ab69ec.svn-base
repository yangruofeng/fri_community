<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/18
 * Time: 16:41
 */

class memberForgotPasswordApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Member forgot password";
        $this->description = "忘记密码";
        $this->url = C("bank_api_url") . "/member.resetpwd.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("type", "找回方式 sms 短信", 'sms', true);
        $this->parameters[]= new apiParameter("country_code", "国家码", '', true);
        $this->parameters[]= new apiParameter("phone", "手机号码", '', true);
        $this->parameters[]= new apiParameter("sms_id", "验证id", '', true);
        $this->parameters[]= new apiParameter("sms_code", "验证码", '', true);  // password
        $this->parameters[]= new apiParameter("password", "新密码", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
            )
        );

    }


}