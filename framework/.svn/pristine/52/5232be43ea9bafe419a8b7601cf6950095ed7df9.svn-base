<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:55
 */

class mortgageControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("mortgage");
        Language::read('mortgage');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('mortgage');

    }

    public function indexOp(){
        Tpl::showPage("coming.soon");
    }

}