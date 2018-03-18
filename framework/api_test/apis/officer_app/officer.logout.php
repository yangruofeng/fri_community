<?php
/**
 * Created by PhpStorm.
 * User: hh
 * Date: 2018/3/18
 * Time: 上午 11:53
 */
class officerLogoutApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Officer logout";
        $this->description = "业务员登陆退出";
        $this->url = C("bank_api_url") . "/officer.logout.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token", '', true);
        $this->parameters[]= new apiParameter("client_type", "终端类型", 'android',true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}