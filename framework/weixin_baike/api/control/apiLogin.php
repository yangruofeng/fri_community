<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/10/31
 * Time: 09:30
 */
class apiLoginControl
{
    function __construct()
    {
        Language::read('entry_index,common');
    }

    /**
     * 登录api
     * @return result
     */
    function loginOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $user_code = trim($p['user_code']);
        $user_password = trim($p['user_password']);

        if (empty($user_code)) {
            return new result(false, 'The account cannot be empty!');
        }

        if (empty($user_password)) {
            return new result(false, 'The password cannot be empty!');
        }

        $m_um_user = M('um_user');
        $user = $m_um_user->getRow(array(
            "user_code" => $user_code,
        ));

        if (empty($user)) {
            return new result(false, 'Account error!');
        }

        if ($user->user_status == 0) {
            return new result(false, 'Deactivated account!');
        }

        if (empty($user) || $user->password != md5($user_password)) {
            return new result(false, 'Password error!');
        }

        $position_arr = array(
            userPositionEnum::CREDIT_OFFICER,
            userPositionEnum::TELLER,
            userPositionEnum::CHIEF_TELLER,
            userPositionEnum::BRANCH_MANAGER,
        );

        $user_position = my_json_decode($user['user_position']);
        foreach ($user_position as $position) {
            if (in_array($position, $position_arr)) {
                return new result(false, 'No access to the system.!');
            }
        }

        $data_update = array(
            'uid' => $user->uid,
            'last_login_time' => Now(),
            'last_login_ip' => getIp()
        );
        $m_um_user->update($data_update);
        $user_arr = $user->toArray();

        setSessionVar("user_info", $user_arr);
        setSessionVar("is_login", "ok");

        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogin($user->uid, 'web');

        if ($p["remember_me"] == 1) {
            setcookie("user_code", $p["user_code"], time() + 3600 * 24 * 7);
        } else {
            setcookie("user_code", "", time() - 3600);
        }

        return new result(true, '', array('new_url' => ENTRY_DESKTOP_SITE_URL . DS . 'index.php'));
    }

    /**
     * counter登录
     * @return result
     */
    public function counterLoginOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $user_code = trim($p['user_code']);
        $user_password = trim($p['user_password']);
        if (empty($user_code)) {
            return new result(false, 'The account cannot be empty!');
        }

        if (empty($user_password)) {
            return new result(false, 'The password cannot be empty!');
        }

        $m_um_user = M('um_user');
        $user = $m_um_user->getRow(array(
            "user_code" => $user_code,
        ));

        if (empty($user)) {
            return new result(false, 'Account error!');
        }

        if ($user->is_credit_officer) {
            return new result(false, 'Not a teller!');
        }

        if ($user->user_status == 0) {
            return new result(false, 'Deactivated account!');
        }

        if (empty($user) || $user->password != md5($user_password)) {
            return new result(false, 'Password error!');
        }

        //postion判断能否登陆
        $position_arr = array(
            userPositionEnum::TELLER,
            userPositionEnum::CHIEF_TELLER,
            userPositionEnum::BRANCH_MANAGER,
        );

        $user_position = my_json_decode($user['user_position']);
        $is_access = false;
        foreach ($user_position as $position) {
            if (in_array($position, $position_arr)) {
                $is_access = true;
                break;
            }
        }
        if(!$is_access){
            return new result(false, 'No access to the system!');
        }

        $data_update = array(
            'uid' => $user->uid,
            'last_login_time' => Now(),
            'last_login_ip' => getIp()
        );
        $m_um_user->update($data_update);
        $user_arr = $user->toArray();

        setSessionVar("counter_info", $user_arr);
        setSessionVar("is_login", "ok");

        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogin($user->uid, 'counter');

        if ($p["remember_me"] == 1) {
            setcookie("user_code", $p["user_code"], time() + 3600 * 24 * 7);
        } else {
            setcookie("user_code", "", time() - 3600);
        }


        return new result(true, '', array('new_url' => ENTRY_COUNTER_SITE_URL . DS . 'index.php'));
    }

}
