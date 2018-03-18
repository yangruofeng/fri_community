<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:36
 */

class aceQueryMyBalanceApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.partner.check";
        $this->description = "Check Partner Balance";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=query_my_balance";

        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'currency' => '货币',
                    'amount' => '余额'
                )
            )
        );
    }
}