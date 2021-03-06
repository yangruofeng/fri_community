<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 11:59
 */
class phoneCodeSendApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Phone Code Send";
        $this->description = "发送短信验证码";
        $this->url = C("bank_api_url") . "/phone.code.send.php";

        $sign = md5('country_code=86&phone=18902461905'.'6bc944bd-8886-11e7-81e6-ccb0daf5504e');

        $this->parameters = array();
        $this->parameters[]= new apiParameter("country_code", "电话国际区号", '86', true);
        $this->parameters[]= new apiParameter("phone", "电话", '18902461905', true);
        //$this->parameters[]= new apiParameter("sign", "参数签名，计算方式：md5(所有参数按字母顺序排列的k=v使用&连接后字符串 . sign_key)",$sign, true);


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