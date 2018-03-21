<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/6
 * Time: 13:34
 */
// member.loan.repayment.record
class memberLoanRepaymentRecordApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Member Loan repayment record";
        $this->description = "会员贷款还款记录";
        $this->url = C("bank_api_url") . "/member.loan.repayment.record.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'contract_id' => '合同ID，如45',
                    'contract_sn' => '合同编号，如1-1000024-002-7',
                    'amount' => '金额，如990',
                    'currency' => '币种，如USD',
                    'month_time' => '月时间，如2018-02',
                    'day_time' => '日时间，如02-06 13:26',
                    'create_time' => '详细时间，如2018-02-06 13:26:57',
                )
            )
        );

    }
}