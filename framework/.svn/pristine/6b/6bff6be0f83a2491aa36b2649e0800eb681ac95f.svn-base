<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:19
 */

class aceVerifyClientAccountApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.member.verify";
        $this->description = "Verify if the phone register is ACE member or not.";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=verify_client_account";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("ace_account", "Member Phone Number", "+86-15928677642", true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'flag_ace_member' => '1: 是ace的会员，2： 不是'
            )
        );
    }
}