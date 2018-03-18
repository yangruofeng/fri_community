<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/7
 * Time: 15:48
 */
class userControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("user");
        Language::read('user');
        Tpl::setLayout('home_layout');
    }

    /**
     * 用户修改密码
     */
    public function changePasswordOp()
    {
        Tpl::showPage("user.change_pwd");
    }

    /**
     * 修改密码
     * @param $p
     * @return result
     */
    public function apiChangePasswordOp($p)
    {
        $p['user_id'] = $this->user_id;
        if (trim($p['new_password'] != trim($p['verify_password']))) {
            return new result(false, 'Verify password error!');
        }
        $class_user = new userClass();
        $rt = $class_user->changePassword($p);
        return $rt;
    }

}