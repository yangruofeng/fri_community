<?php

class memberMessageUnreadCountApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Get Member Unread Messages Count";
        $this->description = "获取会员未读消息条数";
        $this->url = C("bank_api_url") . "/member.message.unread.count.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '未读消息条数'
        );

    }
}