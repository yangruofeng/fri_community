<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:41
 */

class aceDisburseFinishApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.transfer.partner2member.finish";
        $this->description = "Transfer money from partner to his signed member (for member loan)";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=disburse_finish";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("transfer_id", "Disburse Start 获得的事务ID", null, true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）'
        );
    }
}