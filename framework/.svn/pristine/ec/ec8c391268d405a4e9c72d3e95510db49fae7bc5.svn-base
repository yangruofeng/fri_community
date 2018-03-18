<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:58
 */

class cashControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("cash");
        Language::read('cash');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('cash_on_hand');
    }

    public function cashOnHandOp(){
        Tpl::showPage("coming.soon");
    }

    public function cashInVaultOp(){
        $this->outputSubMenu('cash_in_vault');
        Tpl::showPage("coming.soon");
    }


}
