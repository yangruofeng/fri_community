<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/29
 * Time: 16:28
 */
class memberLogoutApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Logout";
        $this->description = "member退出登录";
        $this->url = C("bank_api_url") . "/member.logout.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        //$this->parameters[]= new apiParameter("client_id", "客户端id", 0);
        $this->parameters[]= new apiParameter("client_type", "客户端类型", 'android',true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}