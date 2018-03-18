<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 11:06
 */
class loanApplyAppApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "App loan apply";
        $this->description = "APP贷款申请";
        $this->url = C("bank_api_url") . "/loan.apply.app.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id，没登陆传 0", 0, true);
        $this->parameters[]= new apiParameter("amount", "贷款金额", 1000, true);
        $this->parameters[]= new apiParameter("loan_propose", "贷款目的", 'Business', true);
        $this->parameters[]= new apiParameter("loan_time", "贷款时间", 1, true);
        $this->parameters[]= new apiParameter("loan_time_unit", "贷款时间单位 year month day", 'year', true);

        $this->parameters[]= new apiParameter("name", "非登陆必传参数->贷款人名字 ", 'test');
        $this->parameters[]= new apiParameter("address", "非登陆必传参数->地址 ", 'street 208');
        $this->parameters[]= new apiParameter("country_code", "非登陆必传参数->电话国家码", '855');
        $this->parameters[]= new apiParameter("phone", "非登陆必传参数->电话号码", '85625415');
        $this->parameters[]= new apiParameter("sms_id", "非登陆必传参数->短信ID", 1);
        $this->parameters[]= new apiParameter("sms_code", "非登陆必传参数->验证码", '888888');


        $this->parameters[]= new apiParameter("mortgage", "抵押物，多个用,隔开", null);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => ''
        );

    }
}