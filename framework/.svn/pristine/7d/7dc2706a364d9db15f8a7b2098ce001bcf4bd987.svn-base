<?php

class memberMessageDeleteApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Delete Messages";
        $this->description = "会员删除收到的消息";
        $this->url = C("bank_api_url") . "/member.message.delete.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("message_id_list", "消息ID列表，使用|分割多个ID", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）'
        );

    }
}