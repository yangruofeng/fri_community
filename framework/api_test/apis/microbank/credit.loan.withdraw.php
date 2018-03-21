<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/30
 * Time: 14:20
 */
class creditLoanWithdrawApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit Loan Withdraw";
        $this->description = "信用贷款体现";
        $this->url = C("bank_api_url") . "/credit_loan.withdraw.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", '会员id', 1, true);
        $this->parameters[]= new apiParameter("amount", "贷款金额", 500, true);
        $this->parameters[]= new apiParameter("loan_period", "贷款时间", 6, true);
        //$this->parameters[]= new apiParameter("loan_period_unit", "贷款时间单位，年 year 月 month 日 day", 'month', true);
        //$this->parameters[]= new apiParameter("repayment_type", "还款方式，只支持三个,等额本息 annuity_scheme,一次偿还 single_repayment，先利息后本金 balloon_interest", 'annuity_scheme', true);
        //$this->parameters[]= new apiParameter("repayment_period", "还款周期,一年一次 yearly，半年一次 semi_yearly，一季度一次 quarter，一月一次 monthly,一周一次 weekly", 'monthly', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);
        //$this->parameters[]= new apiParameter("propose", "贷款目的", '', false);
        $this->parameters[] = new apiParameter('insurance_item_id','绑定的保险项目id,多个用,隔开,如 1,5,9 ',0);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'contract_id' => '合同id',
                'loan_amount' => '贷款金额',
                'currency' => '货币类型',
                'loan_period_value' => '贷款周期',
                'loan_period_unit' => '贷款周期单位 year 年 month 月 day 日',
                'repayment_type' => '还款方式',
                'repayment_period' => '还款周期',
                'interest_rate' => '利率值',
                'interest_rate_type' => '利率类型',
                'interest_rate_unit' => '利率周期，yearly 年 monthly 月 daily 日',
                'due_date' => '还款日',
                'due_date_type' => '还款日类型，once 一次还，日期 yearly 每年（周期的月-日）  monthly 每月多少号 weekly 每周几（0-6）daily 每天',
                'admin_fee' => '管理费',
                'insurance_fee' => '保险费',
                'operation_fee' => '总计运营费',
                'total_interest' => '合计利息',
                'actual_receive_amount' => '实发款金额',
                'total_repayment' => '总计应还本金+利息',
                'lending_time' => '贷款时间',
                'contract_info' => '合同详细信息',
                'loan_product_info' => '贷款产品信息',
                'interest_info' => '贷款利率信息',
                'size_rate' => '基本利率信息',
                'special_rate' => '特殊利率信息',
                'loan_disbursement_scheme' => '放款计划',
                'loan_installment_scheme' => '还款计划',
                'bind_insurance' => '绑定的保险合同',

            )
        );

    }
}