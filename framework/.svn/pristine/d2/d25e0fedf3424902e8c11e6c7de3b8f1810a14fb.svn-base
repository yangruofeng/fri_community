<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 13:19
 */
class systemCountryCodeApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "System country code";
        $this->description = "系统国家编码";
        $this->url = C("bank_api_url") . "/system.country.code.php";

        $this->parameters = array();



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
               '855','86','66','84'
            )
        );

    }


}