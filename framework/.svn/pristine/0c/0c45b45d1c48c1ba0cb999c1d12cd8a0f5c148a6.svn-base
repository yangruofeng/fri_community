<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */
class entry_indexControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("book_layout");
        Tpl::setDir("layout");
        Tpl::output("html_title", "Counter Office");
        Tpl::output("user_info", $this->user_info);
        Tpl::output("menu_items", $this->getResetMenu());
    }

    public function indexOp()
    {
        $r = new ormReader();
        $sql = "SELECT sd.depart_name,sb.branch_name FROM site_depart sd INNER JOIN site_branch sb ON sd.branch_id = sb.uid WHERE sd.uid = " . intval($this->user_info['depart_id']);
        $department_info = $r->getRow($sql);
        Tpl::output('department_info', $department_info);
        Tpl::showPage("null_layout");
    }
}