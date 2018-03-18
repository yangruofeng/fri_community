<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */
class indexControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("book_layout");
        Tpl::setDir("layout");
        Tpl::output("html_title", "Back Office");
        Tpl::output("user_info", $this->user_info);
        Tpl::output("is_operator", in_array(userPositionEnum::OPERATOR, $this->user_position));
        Tpl::output("menu_items", $this->getResetMenu());
        if (in_array(userPositionEnum::OPERATOR, $this->user_position)) {
            Tpl::output('system_title', 'Operator');
        }
    }

    public function indexOp()
    {
        if (in_array(userPositionEnum::OPERATOR, $this->user_position)) {
            $rt = $this->getTaskNumOp();
            Tpl::output("task_num", $rt->DATA);
        }
        Tpl::showPage("null_layout");
    }
}