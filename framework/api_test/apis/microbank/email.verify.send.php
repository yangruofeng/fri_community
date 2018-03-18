<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 10:48
 */
class emailVerifySendApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Email Verify Send";
        $this->description = "发送验证邮件";
        $this->url = C("bank_api_url") . "/email.verify.send.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("email", "邮箱地址", '328705107@qq.com', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}