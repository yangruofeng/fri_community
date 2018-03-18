<?php

class member_messageClass {
    public static function getReceivedMessages($member_id, $page_index, $page_size) {
        $member_id = intval($member_id);
        if( !$member_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $sql = <<<SQL
select b.*, a.is_read, a.read_time
from member_message_receiver a inner join member_message b on b.message_id = a.message_id
where a.receiver_type = 0 and a.receiver_id = '$member_id' and a.is_deleted = 0
order by b.message_time desc
SQL;
        $r = new ormReader();
        $ret = $r->getPage($sql, $page_index, $page_size);

        return new result(true, null, array(
            'total_num' => $ret->count,
            'total_pages' => $ret->pageCount,
            'current_page' => $ret->pageIndex,
            'page_size' => $ret->pageSize,
            'list' => $ret->rows
        ));
    }

    public static function getUnreadMessagesCount($member_id) {
        if( !$member_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m = new member_message_receiverModel();
        $ret = $m->field("count(*) cnt")->where(array(
            'receiver_type' => '0',
            'receiver_id' => $member_id,
            'is_deleted' => '0',
            'is_read' => '0'
        ))->find();


        return new result(true, null, intval($ret['cnt']));
        /*if ($ret) {
            return new result(true, null, $ret['cnt']);
        } else {
            // 没有数据有bug
            return new result(false, 'Select count failed', errorCodesEnum::DB_ERROR);
        }*/
    }

    protected static function deleteMemberMessageById($member_id,$id)
    {
        $m_receiver = new member_message_receiverModel();
        $message_receiver = $m_receiver->getRow(array(
            'message_id' => $id,
            'receiver_type' => 0,
            'receiver_id' => $member_id
        ));
        if( $message_receiver ){
            $message_receiver->is_deleted = 1;
            $message_receiver->delete_time = Now();
            $up = $message_receiver->update();
            if( !$up->STS ){
                return new result(false,'Delete fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',$id);
    }


    /** 多条消息 | 隔开
     * @param $messages
     * @return result
     */
    public static function deleteMessages($member_id,$messages)
    {

        $arr = explode('|',trim($messages));
        if( count($arr) > 0){
            foreach( $arr as $id ){
                if( $id ){
                    $re = self::deleteMemberMessageById($member_id,$id);
                    if( !$re->STS ){
                        return $re;
                    }
                }
            }
        }
        return new result(true,'success',null);
    }

    public static function readMessage($member_id,$msg_id)
    {

        $m_message = new member_messageModel();
        $m_receiver = new member_message_receiverModel();

        $receive_msg = $m_receiver->getRow(array(
            'receiver_id' => $member_id,
            'receiver_type' => 0,
            'message_id' => $msg_id,
            'is_deleted' => 0
        ));
        if( !$receive_msg ){
            return new result(false,'No message',null,errorCodesEnum::NO_MESSAGE);
        }

        $message = $m_message->getRow(array(
            'message_id' => $msg_id
        ));

        if( !$message ){
            return new result(false,'No message',null,errorCodesEnum::NO_MESSAGE);
        }

        $receive_msg->is_read = 1;
        $receive_msg->read_time = Now();
        $up = $receive_msg->update();
        if( !$up->STS ){
            return new result(false,'Read fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$message->toArray());

    }


    /** 发送系统消息
     * @param $receiver_id
     * @param $title
     * @param $body
     * @return result
     */
    public static function sendSystemMessage($receiver_id,$title,$body)
    {
        if( !$body ){
            return new result(false,'No content',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($receiver_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_message = new member_messageModel();
        $m_receiver = new member_message_receiverModel();
        $message = $m_message->newRow();
        $message->message_type = 1;
        $message->sender_type = 1;
        $message->sender_id = 0;
        $message->sender_name = 'system';
        $message->message_title = $title;
        $message->message_body = $body;
        $message->message_time = Now();
        $message->message_state = 0;
        $in = $message->insert();
        if( !$in->STS ){
            return new result(false,'Send fail',null,errorCodesEnum::DB_ERROR);
        }

        $receiver_msg = $m_receiver->newRow();
        $receiver_msg->message_id = $message->message_id;
        $receiver_msg->receiver_type = 0;
        $receiver_msg->receiver_id = $receiver_id;
        $receiver_msg->receiver_name = $member->display_name?:$member->login_code;
        $in = $receiver_msg->insert();
        if( !$in->STS ){
            return new result(false,'Send fail',null,errorCodesEnum::DB_ERROR);
        }

        jpushApi::Instance()->sendUserMessage($receiver_id, $message->message_id, $body);

        return new result(true,'success',array(
            'member_message' => $message,
            'member_message_receiver' => $receiver_msg
        ));

    }


}