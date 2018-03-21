<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/25
 * Time: 10:15
 */

class phoneIsRegisteredApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Phone Is Registered";
        $this->description = "验证手机号是否被注册";
        $this->url = C("bank_api_url") . "/phone.is.registered.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("country_code", "电话国际区号", '86', true);
        $this->parameters[]= new apiParameter("phone", "电话", '18902461905', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'is_registered' => '1 是 0 否',
            )
        );

    }

}