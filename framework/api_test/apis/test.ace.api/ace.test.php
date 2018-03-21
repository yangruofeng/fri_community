<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 16:56
 */

class aceTestApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.hello";
        $this->description = "To test if the service is normal or not.";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=test";

        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => ''
        );
    }
}