<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:33
 */

class aceBindStartApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.member.sign.start";
        $this->description = "Member sign apply. ACE will send SMS to verify the application. \nThe phone number should belong to some member which is NOT the signed-member of the partner.";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=bind_start";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("ace_account", "Member Phone Number", "+86-15928677642", true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'sign_id' => '绑定事务编号，Finish时使用'
            )
        );
    }
}