<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 18:11
 */
// ace.verify.client.account
class aceVerifyClientAccountApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Ace verify account";
        $this->description = "验证账号是否ACE会员";
        $this->url = C("bank_api_url") . "/ace.verify.client.account.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter('account','ACE账号','+86-18902461905',true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '0 否 1 是'
        );

    }
}