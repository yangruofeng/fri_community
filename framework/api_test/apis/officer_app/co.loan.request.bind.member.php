<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/9
 * Time: 14:21
 */
// co.loan.request.bind.member
class coLoanRequestBindMemberApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan request bind member";
        $this->description = "CO 为贷款申请绑定会员";
        $this->url = C("bank_api_url") . "/co.loan.request.bind.member.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("request_id", "申请 id", 1, true);
        $this->parameters[]= new apiParameter("member_id", "会员 id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'request_detail' => array(
                    'uid' => 'id',
                    'member_id' => '会员id,没有为null或0',
                    'applicant_name' => '申请人名字',
                    'apply_amount' => '申请金额',
                    'currency' => '货币',
                    'contact_phone' => '联系电话',
                    'apply_time' => '申请时间',
                    'state' => '状态 0-100'
                )
            )
        );

    }
}