<?php

class operatorControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Operator");
        Tpl::setDir("operator");
        $this->getProcessingTask();
    }

    /**
     * 新创建client
     */
    public function newClientOp()
    {
        Tpl::showPage('new_client');
    }

    /**
     * 获取新创建client列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        $sql = "SELECT cm.*,uu.user_name operator_name FROM client_member cm LEFT JOIN um_user uu ON cm.operator_id = uu.uid WHERE cm.operate_state = " . $verify_state;
        if (trim($p['search_text'])) {
            $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY cm.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "current_user" => $this->user_id,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "verify_state" => $verify_state
        );
    }

    /**
     * 检查新注册Client
     */
    public function checkNewClientOp()
    {
        $uid = intval($_GET['uid']);
        $m_client_member = M('client_member');
        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);
        $is_handle_task = $m_um_user_operator_task->isHandleTask($uid, operateTypeEnum::NEW_CLIENT);
        if (!$is_handle_task) {
            showMessage('You can\'t deal with new task before finish the suspended task.');
        }

        $client_info = $m_client_member->getRow(array('uid' => $uid));

        if (!$client_info) {
            showMessage('Invalid Id!');
        }

        if (in_array($client_info->operate_state, array(newMemberCheckStateEnum::CLOSE, newMemberCheckStateEnum::ALLOT, newMemberCheckStateEnum::PASS))) {
            showMessage('The new user has been checked!');
        }

        if ($client_info->operate_state == newMemberCheckStateEnum::LOCKED && $client_info->operator_id != $this->user_id) {
            showMessage('Other operator have been reprocessed!');
        }

        if ($client_info->operate_state == newMemberCheckStateEnum::CREATE) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $client_info->operate_state = newMemberCheckStateEnum::LOCKED;
            $client_info->operator_id = $this->user_id;
            $client_info->update_time = Now();
            $rt_1 = $client_info->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                showMessage($rt_1->MSG);
            }

            $rt_2 = $m_um_user_operator_task->insertTask($uid, operateTypeEnum::NEW_CLIENT);
            if (!$rt_2->STS) {
                $conn->rollback();
                showMessage($rt_2->MSG);
            }
            $conn->submitTransaction();
            $this->getProcessingTask();
        }

        if ($client_info['member_grade']) {
            $m_member_grade = M('member_grade');
            $member_grade = $m_member_grade->find(array('uid' => $client_info['member_grade']));
            $client_info['grade_code'] = $member_grade['grade_code'];
        }
        Tpl::output('client_info', $client_info);

        $m_site_branch = M('site_branch');
        $branch_list = $m_site_branch->orderBy('uid DESC')->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('new_client.check');
    }

    /**
     * 获取co列表
     * @param $p
     * @return array
     */
    public function getCoListOp($p)
    {
        $search_text = trim($p['search_text']);
        $branch_id = intval($p['branch_id']);

        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name,sb.branch_name,sd.depart_name FROM um_user uu INNER JOIN um_user_position up ON uu.uid = up.user_id INNER JOIN site_depart sd ON uu.depart_id = sd.uid INNER JOIN site_branch sb ON sb.uid = sd.branch_id WHERE uu.user_status = 1 AND sb.status = 1 AND up.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        if ($search_text) {
            $sql .= " AND uu.user_name like '%" . $search_text . "'";
        }
        if ($branch_id) {
            $sql .= " AND sb.uid = " . $branch_id;
        }
        $sql .= " ORDER BY sb.uid asc, sd.uid ASC";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $co_ids = array_column($rows, 'uid');
            $co_id_str = "(" . implode(',', $co_ids) . ")";
            $sql = "SELECT co_id,count(*) member_num FROM client_member WHERE co_id IN $co_id_str GROUP BY co_id";
            $member_num = $r->getRows($sql);
            $member_num = resetArrayKey($member_num, 'co_id');

            $sql = "SELECT co_id,count(*) apply_num FROM loan_apply WHERE co_id IN $co_id_str AND (state = " . loanApplyStateEnum::ALLOT_CO . " OR state = " . loanApplyStateEnum::ALLOT_CO . ") GROUP BY co_id";
            $apply_num = $r->getRows($sql);
            $apply_num = resetArrayKey($apply_num, 'co_id');

            foreach ($rows as $key => $row) {
                $row['member_num'] = intval($member_num[$row['uid']]['member_num']);
                $row['apply_num'] = intval($apply_num[$row['uid']]['apply_num']);
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 处理结果
     * @param $p
     * @return result
     */
    public function submitCheckClientOp($p)
    {
        $member_id = intval($p['uid']);
        $verify_state = trim($p['verify_state']);
        $credit_officer_id = intval($p['credit_officer_id']);
        $remark = trim($p['remark']);
        $m_client_member = M('client_member');
        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);

        $client_info = $m_client_member->getRow(array('uid' => $member_id));
        if ($client_info->operate_state != newMemberCheckStateEnum::LOCKED) {
            return new result(false, 'Invalid Id!');
        }

        if ($client_info->operator_id != $this->user_id) {
            return new result(false, 'Param Error!');
        }

        $chk = $m_um_user_operator_task->checkCurrentTask($member_id, operateTypeEnum::NEW_CLIENT);
        if (!$chk) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        if ($verify_state == 'abandon') {//取消任务
            $client_info->operator_id = 0;
            $client_info->operate_state = newMemberCheckStateEnum::CREATE;
            $client_info->operate_remark = '';
            $client_info->update_time = Now();
            $rt_1 = $client_info->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

            $task_state = 0;
            $msg = 'Abandon Successful!';
        } elseif ($verify_state == 'allot') {
            $m_um_user = M('um_user');
            $co_info = $m_um_user->find(array('uid' => $credit_officer_id));

            $client_info->operate_state = newMemberCheckStateEnum::ALLOT;
            $client_info->operate_remark = $remark;
            $client_info->co_id = $credit_officer_id;
            $client_info->co_name = $co_info['user_name'];
            $client_info->update_time = Now();
            $rt_1 = $client_info->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }
            $task_state = 100;

        } else {
            $client_info->operate_state = $verify_state == 'pass' ? newMemberCheckStateEnum::PASS : newMemberCheckStateEnum::CLOSE;
            $client_info->operate_remark = $remark;
            if ($verify_state == newMemberCheckStateEnum::CLOSE) {
                $client_info->member_state = memberStateEnum::CANCEL;
            }
            $client_info->update_time = Now();
            $rt_1 = $client_info->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }
            $task_state = 100;
        }

        $rt_2 = $m_um_user_operator_task->updateTaskState($member_id, operateTypeEnum::NEW_CLIENT, $task_state);
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, $rt_2->MSG);
        }

        $conn->submitTransaction();
        return new result(true, $msg ?: 'Handle Successful!');
    }

    /**
     * 贷款申请
     */
    public function requestLoanOp()
    {
        Tpl::showPage('request_loan');
    }

    /**
     * 获取申请列表
     * @param $p
     * @return array
     */
    public function getRequestLoanListOp($p)
    {
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        $sql = "SELECT la.* FROM loan_apply la WHERE 1 = 1";
        if ($verify_state == 2) {
            $sql .= " AND la.state >= " . $verify_state;
        } else {
            $sql .= " AND la.state = " . $verify_state;
        }
        if (trim($p['search_text'])) {
            $sql .= " AND la.applicant_name like '%" . trim($p['search_text']) . "%' OR la.contact_phone '" . trim($p['search_text']) . "'";
        }
        $sql .= " ORDER BY la.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $apply_source = (new loanApplySourceEnum)->Dictionary();
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "current_user" => $this->user_id,
            "verify_state" => $verify_state,
            "apply_source" => $apply_source,
        );
    }

    /**
     * 贷款申请审核
     */
    public function operateRequestLoanOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_apply = M('loan_apply');

        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);
        $is_handle_task = $m_um_user_operator_task->isHandleTask($uid, operateTypeEnum::REQUEST_LOAN);
        if (!$is_handle_task) {
            showMessage('You can\'t deal with new task before finish the suspended task.');
        }

        $row = $m_loan_apply->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if ($row->state > loanApplyStateEnum::CREATE) {
            showMessage('The request has been audited!');
        }

        if ($row->state == loanApplyStateEnum::LOCKED && $row->operator_id != $this->user_id) {
            showMessage('Other operator have been reprocessed!');
        }

        if ($row->state == loanApplyStateEnum::CREATE) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $row->state = loanApplyStateEnum::LOCKED;
            $row->operator_id = $this->user_id;
            $row->operator_name = $this->user_name;
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                showMessage($rt_1->MSG);
            }

            $rt_2 = $m_um_user_operator_task->insertTask($uid, operateTypeEnum::REQUEST_LOAN);
            if (!$rt_2->STS) {
                $conn->rollback();
                showMessage($rt_2->MSG);
            }
            $conn->submitTransaction();
            $this->getProcessingTask();
        }

        Tpl::output('apply_info', $row);

        $apply_source = (new loanApplySourceEnum)->Dictionary();
        Tpl::output('apply_source', $apply_source);

        $m_site_branch = M('site_branch');
        $branch_list = $m_site_branch->orderBy('uid DESC')->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showpage('request_loan.audit');
    }

    /**
     * 贷款申请处理
     * @param $p
     * @return result
     */
    public function submitRequestLoanOp($p)
    {
        $uid = intval($p['uid']);
        $verify_state = trim($p['verify_state']);
        $remark = trim($p['remark']);
        $credit_officer_id = intval($p['credit_officer_id']);

        $m_loan_apply = M('loan_apply');
        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);

        $loan_apply = $m_loan_apply->getRow(array('uid' => $uid));
        if (!$loan_apply) {
            return new result(false, 'Invalid Id!');
        }

        if ($loan_apply->state != loanApplyStateEnum::LOCKED) {
            return new result(false, 'Invalid Id!');
        }

        if ($loan_apply->operator_id != $this->user_id) {
            return new result(false, 'Param Error!');
        }

        $chk = $m_um_user_operator_task->checkCurrentTask($uid, operateTypeEnum::REQUEST_LOAN);
        if (!$chk) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        if ($verify_state == 'abandon') {
            $loan_apply->operator_id = 0;
            $loan_apply->operator_name = '';
            $loan_apply->operator_remark = '';
            $loan_apply->state = loanApplyStateEnum::CREATE;
            $loan_apply->update_time = Now();
            $rt_1 = $loan_apply->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

            $task_state = 0;
            $msg = 'Abandon Successful!';
        } elseif ($verify_state == 'allot') {
            $m_um_user = M('um_user');
            $co_info = $m_um_user->find(array('uid' => $credit_officer_id));

            $loan_apply->state = loanApplyStateEnum::ALLOT_CO;
            $loan_apply->operator_remark = $remark;
            $loan_apply->credit_officer_id = $credit_officer_id;
            $loan_apply->credit_officer_name = $co_info['user_name'];
            $loan_apply->update_time = Now();
            $rt_1 = $loan_apply->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }
            $task_state = 100;
        } else {
            $loan_apply->state = loanApplyStateEnum::OPERATOR_REJECT;
            $loan_apply->operator_remark = $remark;
            $loan_apply->update_time = Now();
            $rt_1 = $loan_apply->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }
            $task_state = 100;
        }

        $rt_2 = $m_um_user_operator_task->updateTaskState($uid, operateTypeEnum::REQUEST_LOAN, $task_state);
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, $rt_2->MSG);
        }

        $conn->submitTransaction();
        return new result(true, $msg ?: 'Handle Successful!');
    }

    /**
     * 添加贷款申请
     */
    public function addRequestLoanOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_loan_apply = M('loan_apply');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;

            $rt = $m_loan_apply->addApply($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('operator', 'requestLoan', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('operator', 'addRequestLoan', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type'));
            Tpl::output('mortgage_type', $define_arr['mortgage_type']);

            $apply_source = (new loanApplySourceEnum)->Dictionary();
            Tpl::output('request_source', $apply_source);
            Tpl::showpage('request_loan.add');
        }
    }

    /**
     * Certification File
     */
    public function certificationFileOp()
    {
        $type = trim($_GET['type']) ?: certificationTypeEnum::ID;
        Tpl::output('type', $type);
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('title', $certification_type[$type]);
        Tpl::showPage("certification");
    }

    public function getCertificationListOp($p)
    {
        $r = new ormReader();
        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where 1=1  ";
        $sql1 .= " and verify.cert_type = " . intval($p['cert_type']);
        $sql1 .= " and verify.verify_state = " . intval($p['verify_state']);

        if (trim($p['member_name'])) {
            $sql1 .= " and (member.login_code like '%" . $p['member_name'] . "%')";
        }
        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;
        $list = array();

        foreach ($rows as $row) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $row['uid'] . "'";
            $images = $r->getRows($sql);
            $row['cert_images'] = $images;
            $list[] = $row;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "cur_uid" => $this->user_id,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 审核资料详情
     * @throws Exception
     */
    public function certificationDetailOp()
    {
        $uid = intval($_GET['uid']);

        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);
        $is_handle_task = $m_um_user_operator_task->isHandleTask($uid, operateTypeEnum::CERTIFICATION_FILE);
        if (!$is_handle_task) {
            showMessage('You can\'t deal with new task before finish the suspended task.');
        }

        $sample_images = global_settingClass::getCertSampleImage();
        Tpl::output('cert_sample_images', $sample_images);

        $r = new ormReader();
        $m_member_verify_cert = M('member_verify_cert');
        $row = $m_member_verify_cert->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if ($row->verify_state > certStateEnum::CREATE) {
            showMessage('The request has been audited!');
        }

        if ($row->verify_state == certStateEnum::LOCK && $row->auditor_id != $this->user_id) {
            showMessage('Other operator have been reprocessed!');
        }

        $data = $row->toArray();

        $ID = $m_member_verify_cert->getRow(array('member_id' => $data['member_id'], 'cert_type' => certificationTypeEnum::ID, 'verify_state' => 10));
        if ($data['cert_type'] == certificationTypeEnum::FAIMILYBOOK && !$ID) {
            showMessage('Please verify the identity card information', getUrl('operator', 'certification', array('cert_type' => certificationTypeEnum::FAIMILYBOOK), false, BACK_OFFICE_SITE_URL));
        }

        $sql = "select * from member_verify_cert where uid != " . $data['uid'] . " and member_id = " . $data['member_id'] . " and cert_type = " . $data['cert_type'] . " order by uid desc";
        $history = $r->getRows($sql);
        foreach ($history as $k => $v) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $v['uid'] . "'";
            $images = $r->getRows($sql);
            $v['cert_images'] = $images;
            $history[$k] = $v;
        }

        if ($row['verify_state'] == certStateEnum::CREATE) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $row->verify_state = certStateEnum::LOCK;
            $row->auditor_id = $this->user_id;
            $row->auditor_name = $this->user_name;
            $row->auditor_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                showMessage($rt_1->MSG);
            }

            $rt_2 = $m_um_user_operator_task->insertTask($uid, operateTypeEnum::CERTIFICATION_FILE);
            if (!$rt_2->STS) {
                $conn->rollback();
                showMessage($rt_2->MSG);
            }
            $conn->submitTransaction();
            $this->getProcessingTask();
        }

        $sql = "select verify.*,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where verify.uid = " . $uid;
        $info = $r->getRow($sql);

        $sql = "select * from member_verify_cert_image where cert_id=" . $uid;
        $images = $r->getRows($sql);
        $info['cert_images'] = $images;

        Tpl::output('info', $info);
        Tpl::output('history', $history);
        if ($ID) {
            Tpl::output('IDInfo', $ID->toArray());
        }

        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('certification_type', $certification_type);
        Tpl::output('title', $certification_type[$row->cert_type]);
        switch ($row->cert_type) {
            case certificationTypeEnum::MOTORBIKE :
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::FAIMILYBOOK :
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
                Tpl::showPage("certification.detail");
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $m_work = new member_workModel();
                $extend_info = $m_work->getRow(array(
                    'cert_id' => $row->uid
                ));
                Tpl::output('extend_info', $extend_info);
                Tpl::showPage('certification.work.detail');
                break;
            default:
                showMessage('Not supported type');
        }
    }

    /**
     * 资料认证
     * @return result
     * @throws Exception
     */
    public function certificationConfirmOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $obj_validate = new Validate();
        $obj_validate->deliverparam = $p;
        $error = $obj_validate->validate();
        if ($error != '') {
            showMessage($error, '', 'html', 'error');
        }

        $uid = intval($p['uid']);
        $m_member_verify_cert = M('member_verify_cert');
        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);

        $row = $m_member_verify_cert->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if ($row->verify_state != certStateEnum::LOCK) {
            showMessage('Invalid Id!');
        }

        if ($row->auditor_id != $this->user_id) {
            showMessage('Param Error!');
        }

        $chk = $m_um_user_operator_task->checkCurrentTask($uid, operateTypeEnum::CERTIFICATION_FILE);
        if (!$chk) {
            showMessage('Param Error!');
        }

        $cert_type = $row->cert_type;

        $conn = ormYo::Conn();
        $conn->startTransaction();

        $rt_2 = $m_um_user_operator_task->updateTaskState($uid, operateTypeEnum::CERTIFICATION_FILE, 100);
        if (!$rt_2->STS) {
            $conn->rollback();
            showMessage($rt_2->MSG);
        }

        //存认证log
        $m_member_cert_log = new member_cert_logModel();
        $rt_3 = $m_member_cert_log->insertCertLog($uid, 1);
        if (!$rt_3->STS) {
            $conn->rollback();
            showMessage($rt_3->MSG);
        }

        switch ($cert_type) {
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
            case certificationTypeEnum::MOTORBIKE:
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                $p['insert'] = false;
                $ret = $m_member_verify_cert->updateState($p);
                if (!$ret->STS) {
                    $conn->rollback();
                    showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                }

                if ($p['verify_state'] == certStateEnum::PASS) {
                    $asset_state = assetStateEnum::CERTIFIED;
                } else {
                    $asset_state = assetStateEnum::INVALID;
                }

                // 更新资产认证状态
                $m_asset = new member_assetsModel();
                $asset = $m_asset->getRow(array('cert_id' => $row['uid']));
                if ($asset) {
                    $asset->asset_state = $asset_state;
                    $asset->update_time = Now();
                    $up = $asset->update();
                    if (!$up->STS) {
                        $conn->rollback();
                        showMessage('Update asset state fail', getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                    }
                }
                $message = 'Successful';
                $conn->submitTransaction();
                break;
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::FAIMILYBOOK :
                // 更新状态
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                if ($cert_type == certificationTypeEnum::ID) {
                    $p['insert'] = true;
                    $ret = $m_member_verify_cert->updateState($p);
                    if (!$ret->STS) {
                        $conn->rollback();
                        showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                    }
                    if ($p['verify_state'] == certStateEnum::PASS) {
                        // 修改会员表身份证信息
                        $m_member = new memberModel();
                        $member = $m_member->getRow($row->member_id);
                        if (!$member) {
                            $conn->rollback();
                            showMessage('Error member', getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                        }
                        $id_en_name_json = json_encode(array('family_name' => $p['en_family_name'], 'given_name' => $p['en_given_name']));
                        $id_kh_name_json = json_encode(array('family_name' => $p['kh_family_name'], 'given_name' => $p['kh_given_name']));
                        $member->initials = strtoupper(substr($p['en_family_name'], 0, 1));
                        $member->display_name = $p['en_family_name'] . ' ' . $p['en_given_name'];
                        $member->kh_display_name = $p['kh_family_name'] . ' ' . $p['kh_given_name'];
                        $member->id_sn = $row['cert_sn'];
                        $member->id_type = $p['id_type'];
                        $member->nationality = $p['nationality'];
                        $member->id_en_name_json = $id_en_name_json;
                        $member->id_kh_name_json = $id_kh_name_json;
                        $member->id_address1 = $p['id_address1'];
                        $member->id_address2 = $p['id_address2'];
                        $member->id_address3 = $p['id_address3'];
                        $member->id_address4 = $p['id_address4'];
                        $member->id_expire_time = $p['cert_expire_time'];
                        $up = $member->update();
                        if (!$up->STS) {
                            $conn->rollback();
                            showMessage('Update member ID sn fail', getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                        }
                    }
                } else {
                    $p['insert'] = false;
                    $ret = $m_member_verify_cert->updateState($p);
                    if (!$ret->STS) {
                        $conn->rollback();
                        showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                    }
                }
                $message = 'Successful';
                $conn->submitTransaction();
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $p['insert'] = false;
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                $ret = $m_member_verify_cert->updateState($p);
                if (!$ret->STS) {
                    $conn->rollback();
                    showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                }

                $m_work = new member_workModel();
                $extend_info = $m_work->getRow(array(
                    'cert_id' => $row->uid
                ));

                if ($p['verify_state'] == certStateEnum::PASS) {
                    $work_state = workStateStateEnum::VALID;
                } else {
                    $work_state = workStateStateEnum::INVALID;
                }
                if ($extend_info) {
                    $extend_info->state = $work_state;
                    $up = $extend_info->update();
                    if (!$up->STS) {
                        $conn->rollback();
                        showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                    }
                }

                // 如果通过
                if ($p['verify_state'] == certStateEnum::PASS) {
                    // 如果是政府员工，更新member表
                    if ($extend_info->is_government) {
                        $m_member = new memberModel();
                        $member = $m_member->getRow($extend_info->member_id);
                        if ($member) {
                            $member->is_government = 1;
                            $up = $member->update();
                            if (!$up->STS) {
                                $conn->rollback();
                                showMessage($ret->MSG, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
                            }
                        }
                    }
                }

                $message = 'Successful';
                $conn->submitTransaction();
                break;
            default:
                $message = 'Not supported type';
                $conn->rollback();
        }

        showMessage($message, getUrl('operator', 'certificationFile', array('cert_type' => $cert_type), false, BACK_OFFICE_SITE_URL));
    }

    /**
     * 取消任务
     * @param $p
     * @return result
     */
    public function abandonCertificationFileOp($p)
    {
        $uid = intval($p['uid']);
        $m_member_verify_cert = M('member_verify_cert');
        $m_um_user_operator_task = new um_user_operator_taskModel($this->user_id);

        $row = $m_member_verify_cert->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        if ($row->verify_state != certStateEnum::LOCK) {
            return new result(false, 'Invalid Id!');
        }

        if ($row->auditor_id != $this->user_id) {
            return new result(false, 'Param Error!');
        }

        $chk = $m_um_user_operator_task->checkCurrentTask($uid, operateTypeEnum::CERTIFICATION_FILE);
        if (!$chk) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        $row->auditor_id = 0;
        $row->auditor_name = '';
        $row->verify_remark = '';
        $row->verify_state = certStateEnum::CREATE;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, $rt_1->MSG);
        }

        $rt_2 = $m_um_user_operator_task->updateTaskState($uid, operateTypeEnum::CERTIFICATION_FILE, 0);
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, $rt_2->MSG);
        }

        $conn->submitTransaction();
        return new result(true, 'Abandon Successful');
    }

    public function grantCreditOp()
    {
        Tpl::showPage('grant.credit');
    }

    public function getGrandCreditListOp($p)
    {
        $r = new ormReader();
        if (trim($p['type'] == 'all')) {
            $sql = "SELECT cm.uid uid FROM client_member cm WHERE cm.member_state != 0";
            if (trim($p['search_text'])) {
                $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%'";
            }
            $sql .= " ORDER BY cm.uid DESC";
        } else {
            $sql = "SELECT cm.uid uid,max(mcl.create_time) last_cert_time FROM client_member cm INNER JOIN member_verify_cert mvc ON cm.uid = mvc.member_id INNER JOIN member_cert_log mcl ON mvc.uid = mcl.cert_id WHERE cm.member_state != 0 AND cm.member_state != 20 AND mcl.state = 0";
            if (trim($p['search_text'])) {
                $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%'";
            }
            $sql .= " GROUP BY cm.uid ORDER BY last_cert_time DESC";
        }

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $member_ids = array_column($rows, 'uid');
            $member_id_str = "(" . implode(',', $member_ids) . ")";
            $sql = "SELECT client.*,loan.uid load_uid,loan.allow_multi_contract,c.credit,c.credit_balance FROM client_member client LEFT JOIN loan_account loan ON loan.obj_guid = client.obj_guid LEFT JOIN member_credit c ON c.member_id=client.uid WHERE client.uid IN $member_id_str ORDER BY client.uid DESC";
            $member_list = $r->getRows($sql);
            $member_list = resetArrayKey($member_list, 'uid');
            foreach ($rows as $key => $row) {
                $row = array_merge(array(), $row, $member_list[$row['uid']]);
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 修改信用额度
     * @throws Exception
     */
    public function editCreditOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $m_member = new memberModel();
        $m_core_dictionary = M('core_dictionary');
        $member = $m_member->getRow(array(
            'obj_guid' => intval($p['obj_guid'])
        ));
        if (!$member) {
            showMessage('No member');
        }
        $setting = $m_core_dictionary->getDictionary('global_settings');
        $setting = my_json_decode($setting['dict_value']);
        if ($p['credit'] > $setting['operator_credit_maximum']) {
            showMessage('Credit limit.');
        }
        $m_loan_account = new loan_accountModel();
        $rt = $m_loan_account->getCreditInfo(intval($p['obj_guid']));
        $data = $rt->DATA;

        $m_credit = new member_creditModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member['uid']
        ));

        $credit_reference = credit_loanClass::getCreditLevelList();
        $cert_lang = enum_langClass::getCertificationTypeEnumLang();
        foreach ($credit_reference as $k => $v) {
            $item = $v;
            $cert_list = $item['cert_list'];
            foreach ($cert_list as $key => $value) {
                $cert_list[$key] = $cert_lang[$value];
            }
            $item['cert_list'] = $cert_list;
            $credit_reference[$k] = $item;
        }
        Tpl::output('credit_reference_value', $credit_reference);

        $sql = "SELECT loan.*,client.uid as member_id,client.display_name,client.alias_name,client.phone_id,client.email FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.obj_guid = '" . intval($p['obj_guid']) . "'";
        $info = $r->getRow($sql);

        if ($p['form_submit'] == 'ok') {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $p['before_credit'] = $member_credit['credit'];
            $rt = $m_loan_account->editCredit($p);
            if (!$rt->STS) {
                unset($p['form_submit']);
                $conn->rollback();
                showMessage($rt->MSG, getUrl('operator', 'editCredit', $p, false, BACK_OFFICE_SITE_URL));

            }

            $sql = "SELECT mcl.uid FROM member_cert_log mcl INNER JOIN member_verify_cert mvc ON mvc.uid = mcl.cert_id WHERE mcl.state = 0 AND mvc.member_id = " . $info['member_id'];
            $log_id_list = $r->getRows($sql);
            if ($log_id_list) {
                $log_ids = array_column($log_id_list, 'uid');
                $log_id_str = '(' . implode(',', $log_ids) . ')';
                $sql = "UPDATE member_cert_log SET state = 100,update_time = '" . Now() . "' WHERE uid IN $log_id_str";
                $rt_1 = $r->conn->execute($sql);
                if (!$rt_1->STS) {
                    unset($p['form_submit']);
                    $conn->rollback();
                    showMessage($rt_1->MSG, getUrl('operator', 'editCredit', $p, false, BACK_OFFICE_SITE_URL));
                }
            }

            $conn->submitTransaction();
            showMessage($rt->MSG, getUrl('operator', 'grantCredit', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            $m_loan_approval = M('loan_approval');
            $approvaling = $m_loan_approval->getRow(array('obj_guid' => intval($p['obj_guid']), 'state' => 0));//申请中
            if ($approvaling) {
                Tpl::output('approval_info', $approvaling);
            }

            $member_id = $member['uid'];
            $re = memberClass::getMemberSimpleCertResult($member_id);
            if (!$re->STS) {
                showMessage('Error: ' . $re->MSG);
            }
            $verifys = $re->DATA;

            $verify_field = enum_langClass::getCertificationTypeEnumLang();
            Tpl::output("verify_field", $verify_field);

            //获取co advice
            $m_member_credit_suggest = M('member_credit_suggest');
            $credit_suggest = $m_member_credit_suggest->orderBy('uid DESC')->find(array('member_id' => $info['member_id']));
            Tpl::output("credit_suggest", $credit_suggest);

            $sql = "SELECT SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $info['member_id'] . " AND asset_state = 100";
            $assets_valuation = $r->getOne($sql);
            Tpl::output("assets_valuation", $assets_valuation);

            $sql = "SELECT asset_type,SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $info['member_id'] . " AND asset_state = 100 GROUP BY asset_type ORDER BY asset_type ASC";
            $assets_valuation_type = $r->getRows($sql);
            Tpl::output("assets_valuation_type", $assets_valuation_type);

            Tpl::output('info', $info);
            Tpl::output("verifys", $verifys);
            Tpl::output('credit_info', $member_credit);
            Tpl::output('loan_info', $data);
            Tpl::showPage("grant.credit.edit");
        }
    }

    /**
     * 获取client
     * @param $p
     * @return result
     */
    public function getClientInfoOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

//        if ($client_info->member_state == memberStateEnum::LOCKING || $client_info->member_state == memberStateEnum::CANCEL) {
//            return new result(false, 'This account is not available！');
//        }

        $client_info['member_state'] = L('client_member_state_' . $client_info['member_state']);//使用这个状态值
        return new result(true, '', $client_info);
    }

    /**
     * 挂失页面
     */
    public function requestLockOp()
    {
        Tpl::showPage("request.lock");
    }

    /**
     * 挂失操作
     * @param $p
     * @return result
     */
    public function lockMemberOp($p)
    {

        $uid = intval($p['uid']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'No eligible clients!');
        }

        if ($row->member_state == memberStateEnum::LOCKING || $row->member_state == memberStateEnum::CANCEL) {
            return new result(false, 'This account is not available！');
        }

        $row->member_state = memberStateEnum::LOCKING;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Lock successfully');
        } else {
            return new result(false, 'Lock failure');
        }
    }

    /**
     * 挂失列表
     * @param $p
     * @return result
     */

    public function lockListOp()
    {
        $r = new ormReader();
        $sql = "SELECT * FROM client_member WHERE member_state=20 ORDER BY update_time DESC ";
        $data = $r->getRows($sql);
        $data->member_state = 1;
        Tpl::output('data', $data);
        Tpl::showPage('lock.list');
    }


    public function complaintAdviceOp()
    {
        Tpl::showPage('complaint.advice');
    }

    /**
     * 增加投诉或建议
     */
    public function addComplaintAdviceOp()
    {
        Tpl::showPage('complaint.advice.add');
    }


    /**
     * 获取投诉建议列表
     */
    public function getComplaintAdviceListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM complaint_advice ";
        if ($search_text) {
            $sql .= " WHERE complaint_advice.type like '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY create_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 新增投诉或建议
     */
    public function saveComplaintAdviceOp()
    {
        $type = $_POST['type'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $contact_name = $_POST['contact_name'];
        $country_code = $_POST['country_code'];
        $phone_number = $_POST['phone_number'];
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];
        $create_time = Now();
        $state = complaintAdviceEnum::CREATE;

        $arr = array(
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'contact_name' => $contact_name,
            'contact_phone' => $contact_phone,
            'create_time' => $create_time,
            'state' => $state
        );
        $m_complaint_advice = M("complaint_advice");
        $r = $m_complaint_advice->insert($arr);
        if ($r->STS) {
            showMessage('Add successfully', getUrl('operator', 'complaintAdvice', array(0), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage('Add failure');
        }
    }

    /**
     *投诉建议详情
     */
    public function detailsOp()
    {
        $uid = $_GET['uid'];
        $r = new ormReader();
        $sql = 'SELECT * FROM complaint_advice WHERE uid=' . $uid;
        $data = $r->getRow($sql);
        Tpl::output('data', $data);
        Tpl::showPage('complaint.advice.details');
    }
}
