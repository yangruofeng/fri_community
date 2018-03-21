<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/6
 * Time: 13:41
 */
// member.bind.bank
class memberBindBankApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member bind bank";
        $this->description = "会员绑定银行卡";
        $this->url = C("bank_api_url") . "/member.bind.bank.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("bank_id", "银行编号ID", 1, true);
        $this->parameters[]= new apiParameter("account_name", "账户名称", 'test', true);
        $this->parameters[]= new apiParameter("account_no", "账户账号", '611254155488632', true);
        $this->parameters[]= new apiParameter("country_code", "银行为wing时传，国家码", '855', false);
        $this->parameters[]= new apiParameter("phone_number", "银行为wing时传，电话号码", '1236547', false);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}