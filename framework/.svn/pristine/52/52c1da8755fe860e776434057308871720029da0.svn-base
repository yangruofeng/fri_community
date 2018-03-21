<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/9
 * Time: 17:32
 */
// co.loan.request.approved
class coLoanRequestApprovedApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan request approved";
        $this->description = "CO贷款申请确认通过，转到BM";
        $this->url = C("bank_api_url") . "/co.loan.request.approved.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("request_id", "申请 id", 1, true);
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
                ),
                'preview_info' => array(
                    'loan_amount' => '',
                    'currency' => '',
                    'loan_time' => '',
                    'loan_time_unit' => '',
                    'repayment_type' => '',
                    'repayment_period' => '',
                    'admin_fee' => '',
                    'loan_fee' => '',
                    'disbursement_amount' => '',
                    'total_interest' => '',
                    'total_operation_fee' => '',
                    'interest_info' => '',
                    'installment_schema' => '还款计划',
                    'total_repayment_detail' => ''
                )

            )
        );


    }

}