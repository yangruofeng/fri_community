<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/10/30
 * Time: 16:00 PM
 */
class loginControl
{
    function __construct()
    {
        Tpl::setDir("login");
    }

    /**
     * 登录页面
     */
    function loginOp()
    {
        Tpl::output('login_sign', true);
        Tpl::showPage("login", "login_layout");
    }

    /**
     * 退出
     */
    function loginOutOp()
    {
        session_start();
        $user_id = $_SESSION['counter_info']['uid'];
        unset($_SESSION['counter_info']);
        unset($_SESSION['is_login']);
        session_write_close();
        // 退出记录日志
        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogout($user_id);
        $login_url = getUrl("login", "login", array(), false, ENTRY_COUNTER_SITE_URL);
        @header('Location:' . $login_url);
    }

}