<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:43
 */

class aceCollectStartApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.transfer.member2partner.start";
        $this->description = "Transfer money from signed member to his signed partner (for return loan).";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=collect_start";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("ace_account", "Member Phone Number", "+86-15928677642", true);
        $this->parameters[]= new apiParameter("amount", "金额", "100", true);
        $this->parameters[]= new apiParameter("currency", "货币", "USD", true);
        $this->parameters[]= new apiParameter("description", "交易描述", "Installment Test");

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'transfer_id' => '事务编号，finish时使用'
            )
        );
    }
}