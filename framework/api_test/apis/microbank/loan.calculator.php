<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 13:50
 */
class loanCalculatorApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Calculator";
        $this->description = "贷款计算器";
        $this->url = C("bank_api_url") . "/loan.calculator.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("amount", "贷款金额", 50000, true);
        $this->parameters[]= new apiParameter("loan_period", "贷款周期，单位月", 24, true);
        $this->parameters[]= new apiParameter("interest", "贷款年利率，百分比，如6%，填6", 6, true);
        $this->parameters[]= new apiParameter("repayment_type", "还款方式，等额本息 annuity_scheme，等额本金 fixed_principal,一次偿还（每月计算一次利息） single_repayment,固定利息 flat_interest，先利息后本金 balloon_interest", 'annuity_scheme', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_summation' => array(
                    'payment_period' => '平均每期还款金额',
                    'total_interest' => '合计偿还利息',
                    'payment_total' => '合计偿还本息'
                ),
                'payment_schema' => array(
                    'scheme_index' => '还款计划序号',
                    'receivable_principal' => '应还本金',
                    'receivable_interest' => '应还利息',
                    'receivable_operation_fee' => '运营费',
                    'amount' => '当期应还总额',
                    'remaining_principal' => '剩余应还本金',
                )
            )
        );

    }
}