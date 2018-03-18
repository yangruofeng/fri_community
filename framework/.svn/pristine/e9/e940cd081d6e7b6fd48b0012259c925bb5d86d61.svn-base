<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/22
 * Time: 18:45
 */
class memberEditLoanAceAccountApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member edi bind ace";
        $this->description = "客户修改ACE账号绑定";
        $this->url = C("bank_api_url") . "/member.edit.loan.ace.account.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("account_handler_id", "原账户id", 1, true);
        $this->parameters[]= new apiParameter("name", "账户名称", 'test', true);
        $this->parameters[]= new apiParameter("account", "账户账号", '326587451', true);
        $this->parameters[]= new apiParameter("country_code", "电话国际码", '855', true);
        $this->parameters[]= new apiParameter("phone", "电话号码", '18902461925', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '绑定的账户'
        );

    }
}