<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/31
 * Time: 10:29
 */
// ace.bind.account.start
class aceBindAccountStartApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Ace bind account start";
        $this->description = "开始绑定ACE账号（发送验证码）";
        $this->url = C("bank_api_url") . "/ace.bind.account.start.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter('account','ACE账号','+86-18902461905',true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'verify_id' => '短信验证ID',
                'phone_id' => '发送电话'
            )
        );

    }
}