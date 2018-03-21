<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 17:31
 */

class aceBindFinishApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Ace API - ace.member.sign.finish";
        $this->description = "Member sign finish. ACE will verify the verification code, if correct, sign success";
        $this->url = C("bank_api_url") . "/test.ace.api.php?op=bind_finish";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("sign_id", "Bind Start 获得的事务ID", null, true);
        $this->parameters[]= new apiParameter("verify_code", "验证码", null, true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'flag_success' => "1: 绑定成功，2：失败"
            )
        );
    }
}