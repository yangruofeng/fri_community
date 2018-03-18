<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 10:39
 */
class emailVerifyConfirmApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Email Verify";
        $this->description = "邮箱验证确认页面（html页面）";
        $this->url = C("bank_api_url") . "/email.verify.confirm.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("vid", "邮箱验证id", '1', true);
        $this->parameters[]= new apiParameter("vkey", "邮箱验证key", '9b7688959d7284433717d031493854db', true);

        $url = $this->url.'?vid=1&vkey=9b7688959d7284433717d031493854db';
        $this->return = array(
            'url' => "查看结果页面：<a href='{$url}' target='_blank'>$url</a>",
        );

    }
}