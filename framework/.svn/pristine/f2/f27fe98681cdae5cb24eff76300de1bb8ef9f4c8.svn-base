<?php

class memberMessageReadApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Read Messages";
        $this->description = "会员读取消息";
        $this->url = C("bank_api_url") . "/member.message.read.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("message_id", "消息ID", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '消息详细属性',
                'message_id' => '消息id',
                'message_type' => '消息类型',
                'sender_type' => '发送者类型',
                'sender_id' => '发送者ID',
                'sender_name' => '发送者名称',
                'message_title' => '消息标题',
                'message_body' => '消息内容',
                'message_time' => '消息时间'
            )
        );

    }
}