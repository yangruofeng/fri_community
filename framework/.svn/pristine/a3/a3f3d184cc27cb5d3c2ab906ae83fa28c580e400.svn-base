<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 11:54
 */
class phoneCodeCdApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Phone Code Cooltime";
        $this->description = "发送短信的冷却时间";
        $this->url = C("bank_api_url") . "/phone.code.cd.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("country_code", "国家编码", '86', true);
        $this->parameters[]= new apiParameter("phone", "电话号码", '18902461905', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '0 不在冷却中 >0 剩余时间（秒）'
        );


    }
}