<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:30
 */

class aceQueryClientBalanceApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.member.check";
        $this->description = "Check Member Balance (Signed member only)";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=query_client_balance";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("ace_account", "Member Phone Number", "+86-15928677642", true);

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