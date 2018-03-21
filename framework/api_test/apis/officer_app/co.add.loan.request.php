<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/8
 * Time: 17:01
 */
class coAddLoanRequestApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Co add loan request";
        $this->description = "CO为客户提交贷款申请";
        $this->url = C("bank_api_url") . "/co.add.loan.request.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1, true);
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("amount", "金额", 500, true);
        $this->parameters[]= new apiParameter("currency", "币种", 'USD', true);
        $this->parameters[]= new apiParameter("loan_time", "贷款时间", 6, true);
        $this->parameters[]= new apiParameter("loan_time_unit", "贷款时间单位", 'month', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '申请信息',
            )
        );

    }
}