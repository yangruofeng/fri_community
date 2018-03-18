<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/1
 * Time: 15:20
 */

// officer.login
class officerLoginApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Officer login";
        $this->description = "业务员登陆";
        $this->url = C("bank_api_url") . "/officer.login.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("user_code", "登陆账号", 'admin', true);
        $this->parameters[]= new apiParameter("password", "密码", '123456', true);
        $this->parameters[]= new apiParameter("client_type", "终端类型", 'android',true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'user_info' => array(
                    '@description' => '用户信息',
                ),
                'token' => '登陆令牌'
            )
        );

    }

}