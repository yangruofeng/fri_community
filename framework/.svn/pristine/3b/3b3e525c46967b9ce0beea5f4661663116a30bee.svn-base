<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class client_blackModel extends tableModelBase{
    public function __construct(){
        parent::__construct('client_black');
    }

    /**
     * 获取role信息
     * @param $uid
     * @return result
     */
    public function getBlackInfo($obj_guid){
        $info = $this->find(array('obj_guid' => $obj_guid));
        if (empty($info)) {
            return new result(false, 'Invalid Member');
        }
        return new result(true, '', $info);
    }

    public function insertBlack($param){
      $obj_guid = $param['obj_guid'];
      $auditor_id = $param['auditor_id'];
      $auditor_name = $param['auditor_name'];
      unset($param['obj_guid']);
      unset($param['auditor_id']);
      unset($param['auditor_name']);
      $insert = $this->newRow();
      $insert->obj_guid = $obj_guid;
      $insert->type = json_encode($param);
      $insert->auditor_id = $auditor_id;
      $insert->auditor_name = $auditor_name;
      $insert->update_time = Now();
      $rt = $insert->insert();
      if ($rt->STS) {
        return new result(true, 'Add successful!');
      } else {
        return new result(false, 'Add failed--' . $rt->MSG);
      }
    }

    public function updateBlack($param){
      $type = $param['type'];
      $list = $param['list'];
      $auditor_id = $param['auditor_id'];
      $auditor_name = $param['auditor_name'];
      $row = $this->getRow(array('type' => $type));
      $row->list = $list;
      $row->auditor_id = $auditor_id;
      $row->auditor_name = $auditor_name;
      $row->update_time = Now();
      $rt = $row->update();
      if ($rt->STS) {
        return new result(true, 'Update successful!');
      } else {
        return new result(false, 'Update failed--' . $rt->MSG);
      }
    }

    public function updateBlackOld($param){
      $obj_guid = $param['obj_guid'];
      $auditor_id = $param['auditor_id'];
      $auditor_name = $param['auditor_name'];
      unset($param['obj_guid']);
      unset($param['auditor_id']);
      unset($param['auditor_name']);
      $row = $this->getRow(array('obj_guid' => $obj_guid));
      $row->type = json_encode($param);
      $row->auditor_id = $auditor_id;
      $row->auditor_name = $auditor_name;
      $row->update_time = Now();
      $rt = $row->update();
      if ($rt->STS) {
        return new result(true, 'Update successful!');
      } else {
        return new result(false, 'Update failed--' . $rt->MSG);
      }
    }
}
