<?php

class emailCooltimeApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Email Cool Time";
        $this->description = "获取邮件发送冷却时间";
        $this->url = C("bank_api_url") . "/email.cooltime.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("email", "邮箱", 'test@test.com', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '0 不在冷却中 >0 剩余时间（秒）'
        );

    }
}