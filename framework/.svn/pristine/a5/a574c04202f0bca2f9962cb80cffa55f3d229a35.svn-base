<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class user
{

    /**
     * 获取user信息
     * @param $uid
     * @return result
     */
    public function getUserInfo($uid)
    {
        $uid = intval($uid);
        $m_um_role_role = M('um_user_role');
        $m_special_auth = M('um_special_auth');
        $r = new ormReader();
        $sql = 'SELECT uu.*,sd.depart_name,sd.branch_id,sb.branch_name FROM um_user uu' .
            ' LEFT JOIN site_depart sd ON uu.depart_id = sd.uid' .
            ' LEFT JOIN site_branch sb ON sd.branch_id = sb.uid' .
            ' WHERE uu.uid = ' . $uid;
        $user_info = $r->getRow($sql);
        if (empty($user_info)) {
            return new result(false, 'Invalid Id');
        }

        $role_arr = $m_um_role_role->select(array('user_id' => $uid));
        $special_auth = $m_special_auth->select(array('user_id' => $uid));
        $role_arr = array_column($role_arr, 'role_id');

        $class_role = new role();
        $back_office_auth = array();
        $counter_auth = array();
        foreach ($role_arr as $role_id) {
            $rt = $class_role->getRoleInfo($role_id);
            $back_office_auth = array_merge($back_office_auth, $rt->DATA['allow_back_office']['allow_auth']);
            $counter_auth = array_merge($counter_auth, $rt->DATA['allow_counter']['allow_auth']);
        }
        $back_office_auth = array_unique($back_office_auth);
        $counter_auth = array_unique($counter_auth);
        $allow_auth_back_office = array();
        $limit_auth_back_office = array();
        $allow_auth_counter = array();
        $limit_auth_counter = array();
        foreach ($special_auth as $auth) {
            if ($auth['auth_type'] == authTypeEnum::BACK_OFFICE) {
                if ($auth['special_type'] == 1) {
                    $allow_auth_back_office[] = $auth['auth_code'];
                }
                if ($auth['special_type'] == 2) {
                    $limit_auth_back_office[] = $auth['auth_code'];
                }
            }

            if ($auth['auth_type'] == authTypeEnum::COUNTER) {
                if ($auth['special_type'] == 1) {
                    $allow_auth_counter[] = $auth['auth_code'];
                }
                if ($auth['special_type'] == 2) {
                    $limit_auth_counter[] = $auth['auth_code'];
                }
            }
        }

        $back_office_auth = array_merge($back_office_auth, $allow_auth_back_office);
        $back_office_auth = array_unique($back_office_auth);
        $back_office_auth = array_diff($back_office_auth, $limit_auth_back_office);

        $counter_auth = array_merge($counter_auth, $allow_auth_counter);
        $counter_auth = array_unique($counter_auth);
        $counter_auth = array_diff($counter_auth, $limit_auth_counter);

        $user_info['role_arr'] = $role_arr;
        $user_info['back_office_auth'] = $back_office_auth;
        $user_info['counter_auth'] = $counter_auth;
        return new result(true, '', $user_info);
    }

    /**
     * 添加user
     * @param $param
     * @return result
     */
    public function addUser($param)
    {
        $user_code = trim($param['user_code']);
        $user_name = trim($param['user_name']);
        $password = trim($param['password']);
        $depart_id = intval($param['depart_id']);
        $role_select = $param['role_select'];
        $auth_select = $param['auth_select'];
        $auth_select_counter = $param['auth_select_counter'];
        $user_position = $param['user_position'] ?: array();
        $remark = $param['remark'];
        $user_status = intval($param['user_status']);
        $creator_id = intval($param['creator_id']);
        $creator_name = $param['creator_name'];
        if (!$user_code || !$user_name) {
            return new result(false, 'User code or name cannot be empty!');
        }
        if (!$depart_id) {
            return new result(false, 'Please select department!');
        }
        $m_um_user = M('um_user');
        $m_um_role_role = M('um_user_role');

        $chk_code = $m_um_user->getRow(array('user_code' => $user_code));
        if ($chk_code) {
            return new result(false, 'Code exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_um_user->newRow();
            $row->user_code = $user_code;
            $row->user_name = $user_name;
            $row->password = md5($password);
            $row->depart_id = $depart_id;
            $row->user_status = $user_status;
            $row->user_position = my_json_encode($user_position);
            $row->obj_guid = '';

            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $back_office_auth_role = array();
            $counter_auth_role = array();
            $class_role = new role();
            foreach ($role_select as $role) {
                $row_role = $m_um_role_role->newRow();
                $row_role->user_id = $rt_1->AUTO_ID;
                $row_role->role_id = $role;
                $rt_2 = $row_role->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed--' . $rt_2->MSG);
                }

                $rt_3 = $class_role->getRoleInfo($role);
                $back_office_auth_role = array_merge($back_office_auth_role, $rt_3->DATA['allow_back_office']['allow_auth']);
                $counter_auth_role = array_merge($counter_auth_role, $rt_3->DATA['allow_counter']['allow_auth']);
            }

            $rt_4 = $this->addUserSpecialAuth($rt_1->AUTO_ID, $back_office_auth_role, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, $rt_4->MSG);
            }
            $rt_5 = $this->addUserSpecialAuth($rt_1->AUTO_ID, $counter_auth_role, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, $rt_5->MSG);
            }

            $row->obj_guid = generateGuid($rt_1->AUTO_ID, objGuidTypeEnum::UM_USER);
            $rt_6 = $row->update();
            if (!$rt_6->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_6->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Add Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    private function addUserSpecialAuth($uid, $role_auth, $select_auth, $type)
    {
        $role_auth = array_unique($role_auth);
        $allow_auth = array_diff($select_auth, $role_auth);
        $limit_auth = array_diff($role_auth, $select_auth);
        $m_special_auth = M('um_special_auth');

        foreach ($allow_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->user_id = $uid;
            $row_special_auth->special_type = 1;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_3 = $row_special_auth->insert();
            if (!$rt_3->STS) {
                return new result(false, 'Add failed--' . $rt_3->MSG);
            }
        }

        foreach ($limit_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->user_id =$uid;
            $row_special_auth->special_type = 2;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_4 = $row_special_auth->insert();
            if (!$rt_4->STS) {
                return new result(false, 'Add failed--' . $rt_4->MSG);
            }
        }
        return new result(true);
    }

    /**
     * 编辑user
     * @param $param
     * @return result
     */
    public function editUser($param)
    {
        $uid = intval($param['uid']);
        $user_code = trim($param['user_code']);
        $user_name = trim($param['user_name']);
        $password = trim($param['password']);
        $depart_id = intval($param['depart_id']);
        $role_select = $param['role_select'];
        $auth_select = $param['auth_select'];
        $auth_select_counter = $param['auth_select_counter'];
        $user_position = $param['user_position'] ?: array();
        $remark = $param['remark'];
        $user_status = intval($param['user_status']);
        if (!$user_code || !$user_name) {
            return new result(false, 'User code or name cannot be empty!');
        }
        if (!$depart_id) {
            return new result(false, 'Please select department!');
        }
        $m_um_user = M('um_user');
        $m_um_user_role = M('um_user_role');
        $m_special_auth = M('um_special_auth');

        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $chk_code = $m_um_user->getRow(array('user_code' => $user_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Code exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->user_code = $user_code;
            $row->user_name = $user_name;
            if ($password) {
                $row->password = md5($password);
            }
            $row->depart_id = $depart_id;
            $row->user_position = my_json_encode($user_position);
            $row->user_status = $user_status;
            $row->update_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_1->MSG);
            }

            $rt_5 = $m_um_user_role->delete(array('user_id' => $uid));
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_5->MSG);
            }

            $back_office_auth_role = array();
            $counter_auth_role = array();
            $class_role = new role();
            foreach ($role_select as $role) {
                $row_role = $m_um_user_role->newRow();
                $row_role->user_id = $uid;
                $row_role->role_id = $role;
                $rt_2 = $row_role->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit failed--' . $rt_2->MSG);
                }

                $rt_3 = $class_role->getRoleInfo($role);
                $back_office_auth_role = array_merge($back_office_auth_role, $rt_3->DATA['allow_back_office']['allow_auth']);
                $counter_auth_role = array_merge($counter_auth_role, $rt_3->DATA['allow_counter']['allow_auth']);
            }

            $rt_6 = $m_special_auth->delete(array('user_id' => $uid));
            if (!$rt_6->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_6->MSG);
            }

            $rt_4 = $this->addUserSpecialAuth($uid, $back_office_auth_role, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, $rt_4->MSG);
            }
            $rt_5 = $this->addUserSpecialAuth($uid, $counter_auth_role, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, $rt_5->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 删除user
     * @param $uid
     * @return result
     */
    public function deleteUser($uid)
    {
        $m_um_user = M('um_user');
        $m_um_user_role = M('um_user_role');
        $m_special_auth = M('um_special_auth');

        $uid = intval($uid);
        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $row->delete();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_1->MSG);
            }

            $rt_2 = $m_um_user_role->delete(array('user_id' => $uid));
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_2->MSG);
            }

            $rt_3 = $m_special_auth->delete(array('user_id' => $uid));
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_3->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Delete Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 修改密码
     * @param $p
     * @return result
     */
    public function changePassword($p)
    {
        $uid = intval($p['user_id']);
        $old_password = trim($p['old_password']);
        $new_password = trim($p['new_password']);
        $m_um_user = M('um_user');
        $row = $m_um_user->getRow($uid);
        if ($row->password != md5($old_password)) {
            return new result(false, 'Old password error!');
        }

        if ($row->password == md5($new_password)) {
            return new result(false, 'The new password is the same as the old password!');
        }

        if (!preg_match("/^[a-zA-Z0-9]{6,18}$/", $new_password)) {
            return new result(false, 'The password must be 6-18 digits or letters!');
        }

        $row->password = md5($new_password);
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Change Successful! Please Login again');
        } else {
            return new result(false, 'Change failure!');
        }
    }


    /**
     * 添加event
     * @param $p
     * @return result
     */
    public function addEvent($p)
    {
        $event_code = trim($p['event_code']);
        $description = trim($p['description']);
        $min_point = round($p['min_point'], 2);
        $max_point = round($p['max_point'], 2);
        $status = intval($p['status']);
        $creator_id = $p['creator_id'];
        $creator_name = $p['creator_name'];

        if (empty($event_code) || empty($description)) {
            return new result(false, 'Param Error!');
        }

        if ($max_point <= $min_point) {
            return new result(false, 'Max point must be greater than min point!');
        }

        $m_hr_point_event = M('hr_point_event');

        $ckh_code = $m_hr_point_event->find(array('event_code' => $event_code));
        if ($ckh_code) {
            return new result(false, 'Code Repeat!');
        }

        $row = $m_hr_point_event->newRow();
        $row->event_code = $event_code;
        $row->description = $description;
        $row->min_point = $min_point;
        $row->max_point = $max_point;
        $row->status = $status;
        $row->is_system = 0;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful!');
        } else {
            return new result(false, 'Add failed!');
        }
    }

    /**
     * 修改event
     * @param $p
     * @return result
     */
    public function editEvent($p)
    {
        $uid = intval($p['uid']);
        $event_code = trim($p['event_code']);
        $description = trim($p['description']);
        $min_point = round($p['min_point'], 2);
        $max_point = round($p['max_point'], 2);
        $status = intval($p['status']);

        if (empty($event_code) || empty($description)) {
            return new result(false, 'Param Error!');
        }

        if ($max_point <= $min_point) {
            return new result(false, 'Max point must be greater than min point!');
        }

        $m_hr_point_event = M('hr_point_event');
        $ckh_code = $m_hr_point_event->find(array('event_code' => $event_code, 'uid' => array('neq', $uid)));
        if ($ckh_code) {
            return new result(false, 'Code Repeat!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_hr_point_event->getRow($uid);
            if ($row->is_system == 1) {
                $conn->rollback();
                return new result(false, 'Invalid Id!');
            }
            $row->event_code = $event_code;
            $row->description = $description;
            $row->min_point = $min_point;
            $row->max_point = $max_point;
            $row->status = $status;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed!');
            }

            if ($status == 0) {
                $r = new ormReader();
                $sql = "select hpd.uid from hr_point_depart hpd INNER JOIN hr_point_period hpp ON hpd.period_id = hpp.uid WHERE hpp.status = 0 ";
                $point_depart = $r->getRows($sql);
                if ($point_depart) {
                    $point_depart_ids = implode(',', array_column($point_depart, 'uid'));
                    $sql = "DELETE FROM hr_point_user WHERE point_event_id = $uid AND point_depart_id IN (" . $point_depart_ids . ")";
                    $rt_1 = $r->conn->execute($sql);
                    if (!$rt_1->STS) {
                        $conn->rollback();
                        return new result(false, 'Edit failed!');
                    }
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 移除事件
     * @param $uid
     * @return result
     */
    public function deleteEvent($uid)
    {
        $m_hr_point_event = M('hr_point_event');
        $row = $m_hr_point_event->getRow($uid);
        if ($row->is_system == 1) {
            return new result(false, 'Invalid Id!');
        }

        $rt = $row->delete();
        if ($rt->STS) {
            return new result(true, 'Delete successful!');
        } else {
            return new result(true, 'Delete failed!');
        }
    }

    /**
     * 增加期间
     * @param $p
     * @return result
     */
    public function addPeriod($p)
    {
        $period = trim($p['period']);
        $end_date = date('Y-m-d', strtotime($p['end_date']));
        $creator_id = $p['creator_id'];
        $creator_name = $p['creator_name'];

        $r = new ormReader();
        $sql = "SELECT MAX(end_date) end_date FROM hr_point_period";
        $prev_end_date = $r->getOne($sql);
        if ($prev_end_date) {
            $start_date = date('Y-m-d', strtotime("$prev_end_date +1 day"));
        } else {
            $start_date = date('Y-m-d', strtotime($p['start_date']));
        }

        if ($start_date > $end_date) {
            return new result(false, 'The start date should not be greater than the end date!');
        }

        $m_hr_point_period = M('hr_point_period');
        $chk = $m_hr_point_period->find(array('period' => $period));
        if ($chk) {
            return new result(false, 'Period repeat!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_hr_point_period->newRow();
            $row->period = $period;
            $row->start_date = $start_date;
            $row->end_date = $end_date;
            $row->status = 0;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(true, 'Add failed!');
            }

            $m_hr_point_depart = M('hr_point_depart');
            $sql = "SELECT * FROM site_depart";
            $depart_list = $r->getRows($sql);
            foreach ($depart_list as $depart) {
                $row_1 = $m_hr_point_depart->newRow();
                $row_1->depart_id = $depart['uid'];
                $row_1->period_id = $rt->AUTO_ID;
                $row_1->status = 0;
                $rt_1 = $row_1->insert();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(true, 'Add failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 修改期间
     * @param $p
     * @return result
     */
    public function editPeriod($p)
    {
        $uid = intval($p['uid']);
        $period = trim($p['period']);

        $r = new ormReader();
        $sql = "SELECT max(uid) max_uid FROM hr_point_period";
        $max_uid = $r->getOne($sql);
        if ($max_uid == $uid) {
            $end_date = date('Y-m-d', strtotime($p['end_date']));
        }

        $m_hr_point_period = M('hr_point_period');
        $chk = $m_hr_point_period->find(array('period' => $period, 'uid' => array('neq', $uid)));
        if ($chk) {
            return new result(false, 'Period repeat!');
        }

        $row = $m_hr_point_period->getRow($uid);

        if ($row->start_date > $end_date) {
            return new result(false, 'The start date should not be greater than the end date!');
        }

        $row->period = $period;

        if ($end_date) {
            $row->end_date = $end_date;
        }

        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(true, 'Edit failed!');
        }
    }

    /**
     * 删除区间
     */
    public function deletePeriod($uid)
    {
        $r = new ormReader();
        $sql = "SELECT max(uid) max_uid FROM hr_point_period";
        $max_uid = $r->getOne($sql);
        if ($max_uid != $uid) {
            return new result(false, 'Param Error!');
        }

        $m_hr_point_period = M('hr_point_period');
        $row = $m_hr_point_period->getRow($uid);
        if ($row['status'] == 100) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $row->delete();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed!');
            }

            $m_hr_point_depart = M('hr_point_depart');
            $rt_1 = $m_hr_point_depart->delete(array('period_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed!');
            }

            $conn->submitTransaction();
            return new result(true, 'Delete successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 开启区间
     * @param $p
     * @return result
     */
    public function activeDepartPeriod($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $row = $m_hr_point_depart->getRow($uid);
        if ($row->status != 100) {
            return new result(false, 'Param Error!');
        }
        $m_hr_point_period = M('hr_point_period');
        $point_period = $m_hr_point_period->find(array('uid' => $row['period_id']));
        if ($point_period['status'] == 100) {
            return new result(false, 'Period Closed!');
        }

        $row->status = 0;
        $row->handler_id = $handler_id;
        $row->handler_name = $handler_name;
        $row->handle_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Active Successful!');
        } else {
            return new result(false, 'Active Failed!');
        }
    }

    private function chkPeriodIsProcessing($uid)
    {
        $m_hr_point_period = M('hr_point_period');
        $hr_point_period = $m_hr_point_period->getRow($uid);
        if ($hr_point_period['start_date'] <= Now()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 计算user系统积分
     * @param $p
     * @return result
     */
    public function calculateSystemPoint($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->find(array('uid' => $uid));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_um_user = M('um_user');
        $user_list = $m_um_user->select(array('depart_id' => $point_depart['uid'], 'user_status' => 1));

        $m_hr_point_user = M('hr_point_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            foreach ($user_list as $user) {
                $system_point_arr = $this->calculateSystemPointByUser($user['uid'], $uid);
                foreach ($system_point_arr as $event_id => $point) {
                    $row = $m_hr_point_user->getRow(array('user_id' => $user['uid'], 'point_depart_id' => $uid, 'point_event_id' => $event_id));
                    if (!$row) {
                        $row = $m_hr_point_user->newRow();
                        $row->user_id = $user['uid'];
                        $row->point_depart_id = $uid;
                        $row->point_event_id = $event_id;
                        $row->point = $point;
                        $row->handler_id = $handler_id;
                        $row->handler_name = $handler_name;
                        $row->create_time = Now();
                        $rt = $row->insert();
                    } else {
                        $row->point = $point;
                        $row->update_time = Now();
                        $rt = $row->update();
                    }

                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(false, 'Calculate Failed!' . $rt->MSG);
                    }
                }
            }
            $conn->submitTransaction();
            return new result(true, 'Calculate Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * @param $user_id
     * @param $point_depart_id
     * @return array
     * todo::系统积分
     */
    private function calculateSystemPointByUser($user_id, $point_depart_id)
    {
        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->select(array('is_system' => 1, 'status' => 100));
        $point_arr = array();
        foreach ($point_event as $event) {
            $point_arr[$event['uid']] = 10;
        }
        return $point_arr;
    }

    /**
     * 自定义项评分
     * @param $p
     * @return result
     */
    public function evaluateUserPoint($p)
    {
        $depart_period_id = intval($p['depart_period']);
        $user_id = intval($p['user_id']);
        $event_id = intval($p['event_id']);
        $score = round($p['score'], 1);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);

        if ($depart_period_id <= 0 || $user_id <= 0 || $event_id <= 0 || $score < 0) {
            return new result(false, "Param Error!");
        }

        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->find(array('uid' => $depart_period_id));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->find(array('uid' => $event_id));
        if (!$point_event) {
            return new result(false, 'Invalid Event Id!');
        }

        $point = round($point_event['max_point'] * $score / 5, 2);


        $m_hr_point_user = M('hr_point_user');
        $row = $m_hr_point_user->getRow(array('user_id' => $user_id, 'point_depart_id' => $depart_period_id, 'point_event_id' => $event_id));
        if (!$row) {
            $row = $m_hr_point_user->newRow();
            $row->user_id = $user_id;
            $row->point_depart_id = $depart_period_id;
            $row->point_event_id = $event_id;
            $row->point = $point;
            $row->rate_score = $score;
            $row->handler_id = $handler_id;
            $row->handler_name = $handler_name;
            $row->create_time = Now();
            $rt = $row->insert();
        } else {
            $row->point = $point;
            $row->rate_score = $score;
            $row->update_time = Now();
            $rt = $row->update();
        }

        if (!$rt->STS) {
            return new result(false, 'Evaluate Failed!');
        }

        $r = new ormReader();
        $sql = "select SUM(point) point_total from hr_point_user WHERE user_id = $user_id AND point_depart_id = $depart_period_id";
        $point_total = $r->getOne($sql);
        return new result(true, 'Evaluate Successful!', array('point_total' => ncPriceFormat($point_total), 'point' => ncPriceFormat($point), 'score' => $score));
    }

    /**
     * 关闭
     * @param $p
     * @return result
     */
    public function closeDepartPeriod($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->getRow(array('uid' => $uid));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_um_user = M('um_user');
        $user_list = $m_um_user->select(array('depart_id' => $point_depart['uid'], 'user_status' => 1));

        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->select(array('status' => 100));

        $m_hr_point_user = M('hr_point_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            foreach ($user_list as $user) {
                foreach ($point_event as $event) {
                    $row = $m_hr_point_user->getRow(array('user_id' => $user['uid'], 'point_depart_id' => $uid, 'point_event_id' => $event['uid']));
                    if (!$row) {
                        $row = $m_hr_point_user->newRow();
                        $row->user_id = $user['uid'];
                        $row->point_depart_id = $uid;
                        $row->point_event_id = $event['uid'];
                        $row->point = 0;
                        $row->handler_id = $handler_id;
                        $row->handler_name = $handler_name;
                        $row->create_time = Now();
                        $rt = $row->insert();
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(false, 'Insert Failed!' . $rt->MSG);
                        }
                    }
                }
            }

            $point_depart->status = 100;
            $point_depart->handler_id = $handler_id;
            $point_depart->handler_name = $handler_id;
            $point_depart->handle_time = Now();
            $rt = $point_depart->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Update Failed!' . $rt->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Close Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 新添加部门增加point区间
     * @param $depart_id
     * @return ormResult|result
     */
    public function addPointDepartPeriodByDepartId($depart_id)
    {
        $r = new ormReader();
        $sql = "select uid from hr_point_period WHERE end_date > '" . Now() . "'";
        $point_period = $r->getRows($sql);

        $m_hr_point_depart = M('hr_point_depart');
        foreach ($point_period as $val) {
            $row = $m_hr_point_depart->newRow();
            $row->depart_id = $depart_id;
            $row->period_id = $val['uid'];
            $row->status = 0;
            $rt = $row->insert();
            if (!$rt->STS) {
                return $rt;
            }
        }

        return new result(true);
    }
}