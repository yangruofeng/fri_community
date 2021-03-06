<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class loan_approvalModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_approval');
    }

    public function getCreditApprovalInfo($p){
      $row = $this->getRow($p);
      if ($row) {
          return new result(true, 'Edit successful!');
      } else {
          return new result(false, 'Edit failed--');
      }
    }

    public function creditGrantConfirm($param){
      $uid = intval($param['uid']);
      $before_credit = $param['before_credit'];
      $credit = $param['current_credit'];
      $repayment_ability = $param['repayment_ability'];
      $obj_guid = $param['obj_guid'];
      $operator_id = intval($param['operator_id']);
      $operator_name = $param['operator_name'];
      $state = $param['state'];
      $remark = $param['remark'];
      if (!$credit) {
        return new result(false, 'Credit cannot be empty!');
      }
      $m_loan_account = M('loan_account');
      $m_loan_credit_release = M('loan_credit_release');

      $row = $this->getRow(array('uid' => $uid));
      if (empty($row)) {
          return new result(false, 'Invalid Id!');
      }

      if($state == 1){

          $m_member = new memberModel();
          $member = $m_member->getRow(array(
              'obj_guid' => $obj_guid
          ));

          if( !$member ){
              return new result(false,'No member!');
          }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->operator_id = $operator_id;
            $row->operator_name = $operator_name;
            $row->operate_time = Now();
            $row->state = $state;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_1->MSG);
            }
            $row_release = $m_loan_credit_release->newRow();
            $row_release->obj_guid = $obj_guid;
            $row_release->before_credit = $before_credit;
            $row_release->current_credit = $credit;
            $row_release->repayment_ability = $repayment_ability;
            $row_release->remark = $remark;
            $row_release->operator_id = $operator_id;
            $row_release->operator_name = $operator_name;
            $row_release->operate_time = Now();
            $rt_2 = $row_release->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_2->MSG);
            }

            $row_account = $m_loan_account->getRow(array('obj_guid' => $obj_guid));
            $row_account->repayment_ability = $repayment_ability;
            $row_account->update_time = Now();
            $rt_3 = $row_account->update();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_3->MSG);
            }

            // 更新信用
            $valid_time = intval($param['valid_time']);
            $time = strtotime("+$valid_time year"); // todo 处理其他单位,默认年
            $expire_time = date('Y-m-d H:i:s',$time);
            $re = member_creditClass::grantCredit($member->uid,$credit,$expire_time);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }

            $conn->submitTransaction();

            // 向用户发送消息
            $title = 'Credit granting success';
            $body = 'Your credit approval has been passed,new credit is '.$credit.',new monthly repayment ability is '.$row_account->repayment_ability.'! ';
            member_messageClass::sendSystemMessage($member['uid'],$title,$body);

            return new result(true, 'Edit Successful');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

      }else{

        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        $row->operate_time = Now();
        $row->state = $state;
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            return new result(false, 'Edit failed--' . $rt_1->MSG);
        }
        return new result(true, 'Edit Successful');
      }

    }


}
