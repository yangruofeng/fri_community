<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/11
 * Time: 17:07
 */
class memberLoanAutoDeductionAccountApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Loan Auto Account";
        $this->description = "会员贷款绑定的自动扣款账户列表";
        $this->url = C("bank_api_url") . "/member.loan.auto.deduction.account.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'uid' => '绑定的账户id',
                    'handler_account' => '账户账号'
                )

            )
        );

    }

}