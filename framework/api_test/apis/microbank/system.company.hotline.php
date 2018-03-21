<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/28
 * Time: 9:55
 */

class systemCompanyHotlineApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "System Company Hotline";
        $this->description = "公司热线电话";
        $this->url = C("bank_api_url") . "/system.company.hotline.php";

        $this->parameters = array();



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '8552365478',
                '966854123'
            )
        );

    }

}