<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class member_verify_certModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_verify_cert');
    }

    /**
     * 获取role信息
     * @param $param
     * @return result
     */
    public function updateState($param)
    {
        $uid = intval($param['uid']);
        $insert = $param['insert'];
        $auditor_id = $param['auditor_id'];
        $auditor_name = $param['auditor_name'];
        $cert_name = $param['cert_name'];
        $cert_sn = $param['cert_sn'];
        $cert_addr = $param['cert_addr_detail'];
        $cert_expire_time = $param['cert_expire_time'];
        $remark = $param['remark'];

        $row = $this->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }
        if ($insert) {
            $row->cert_name = $cert_name;
            $row->cert_sn = $cert_sn;
            $row->cert_addr = $cert_addr;
            $row->cert_expire_time = $cert_expire_time;
        }
        if (isset($param['verify_state'])) {
            $row->verify_state = $param['verify_state'];
        }
        $row->auditor_id = $auditor_id;
        $row->auditor_name = $auditor_name;
        $row->auditor_time = Now();
        $row->verify_remark = $remark;
        $ret = $row->update();
        if (!$ret->STS) {
            return new result(false, 'Edit failed--' . $ret->MSG);
        }

        // 给用户发送消息
        $member_id = $row['member_id'];
        switch ($param['verify_state']) {
            case certStateEnum::PASS:
                $title = 'Certification Pass';
                $body = 'Your submitted certificate has been passed!';
                member_messageClass::sendSystemMessage($member_id, $title, $body);
                break;
            case certStateEnum::NOT_PASS :
                $title = 'Certification Un-pass';
                $body = 'Your submitted certificate authentication did not pass, please resubmit the information!';
                member_messageClass::sendSystemMessage($member_id, $title, $body);
                break;
            default:
                break;
        }
        return new result(true, 'Edit Successful');
    }

}
