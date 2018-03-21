<?php

class memberMessageListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Message List";
        $this->description = "会员收到的消息列表";
        $this->url = C("bank_api_url") . "/member.message.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_num' => '总条数',
                'total_pages' => '总页数',
                'current_page' => '当前页',
                'page_size' => '每页条数',
                'list' => array(
                    array(
                        'message_id' => '消息id',
                        'message_type' => '消息类型',
                        'sender_type' => '发送者类型',
                        'sender_id' => '发送者ID',
                        'sender_name' => '发送者名称',
                        'message_title' => '消息标题',
                        'message_body' => '消息内容',
                        'message_time' => '消息时间',
                        'message_state' => '消息状态',
                        'is_read' => '是否已读',
                        'read_time' => '阅读时间'
                    ),
                    array()
                )

            )
        );

    }
}