<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:54
 */

class aceCollectFinishApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.transfer.member2partner.finish";
        $this->description = "Finish transfering money from signed member to his signed partner (for return loan).";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=collect_finish";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("transfer_id", "Disburse Start 获得的事务ID", null, true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）'
        );
    }
}