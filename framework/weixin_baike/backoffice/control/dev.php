<?php

class devControl extends baseControl
{
    public $abc;
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("dev");
    }

    /**
     * app版本
     */
    public function appVersionOp()
    {
        Tpl::showPage("app_version");
    }

    /**
     * @param $p
     * @return array
     */
    public function getAppVersionOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM common_app_version";
        if ($search_text) {
            $sql .= " WHERE app_name LIKE '%" . $search_text . "%' OR version LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY uid DESC";
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
     * 添加新版本
     */
    public function addVersionOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $m_common_app_version = M('common_app_version');
            $rt = $m_common_app_version->addVersion($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('dev', 'appVersion', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('dev', 'addVersion', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            Tpl::showPage("app_version.add");
        }
    }

    /**
     * 功能开关
     */
    public function functionSwitchOp()
    {
        $m_core_dictionary = M('core_dictionary');
        if ($_POST['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            $param['close_reset_password'] = intval($param['close_reset_password']);
            $param['close_credit_withdraw'] = intval($param['close_credit_withdraw']);
            $param['close_register_send_credit'] = intval($param['close_register_send_credit']);
            $rt = $m_core_dictionary->updateDictionary('function_switch', my_json_encode($param));
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('dev', 'functionSwitch', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $data = $m_core_dictionary->getDictionary('function_switch');
            if ($data) {
                tpl::output('function_switch', my_json_decode($data['dict_value']));
            }
            Tpl::showPage("function.switch");
        }
    }

    /**
     * 重置密码
     */
    public function resetPasswordOp()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('function_switch');
        $data = my_json_decode($data['dict_value']);
        if ($data['close_reset_password'] == 1) {
            showMessage('Reset password closed!');
        }
        Tpl::showpage('reset.password');
    }

    /**
     * 获取重置密码列表
     * @param $p
     * @return array
     */
    public function getResetPasswordListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM client_member";
        if ($search_text) {
            $sql .= " WHERE obj_guid = '" . $search_text . "' OR display_name like '%" . $search_text . "%' OR phone_id like '" . $search_text . "' OR  login_code like '" . $search_text . "'";
        }
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

    public function apiResetPasswordOp($p)
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('function_switch');
        $data = my_json_decode($data['dict_value']);
        if ($data['close_reset_password'] == 1) {
            return new result(false, 'Reset password closed!');
        }
        $uid = intval($p['uid']);
        $new_password = trim($p['new_password']);
        $verify_password = trim($p['verify_password']);
        if ($new_password != $verify_password) {
            return new result(false, 'Verify password error');
        }

        $rt = memberClass::commonUpdateMemberPassword($uid, $new_password);
        if ($rt->STS) {
            return new result(true, 'Reset Successful!');
        } else {
            return new result(false, $rt->MSG);
        }
    }

}
