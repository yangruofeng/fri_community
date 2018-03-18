<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 10:32
 */
class loanCalculatorNewApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "New Loan Calculator";
        $this->description = "内部贷款计算器";
        $this->url = C("bank_api_url") . "/loan.calculator.new.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("loan_amount", "贷款金额", 1000, true);
        $this->parameters[]= new apiParameter("loan_period", "贷款周期", 6, true);
        $this->parameters[]= new apiParameter("loan_period_unit", "贷款时间单位，年 year 月 month 日 day", 'month', true);
        $this->parameters[]= new apiParameter("repayment_type", "还款方式，等额本息 annuity_scheme，等额本金 fixed_principal,一次偿还（每月计算一次利息） single_repayment,固定利息 flat_interest，先利息后本金 balloon_interest", 'annuity_scheme', true);
        $this->parameters[]= new apiParameter("repayment_period", "还款周期,一年一次 yearly，半年一次 semi_yearly，一季度一次 quarter，一月一次 monthly,一周一次 weekly，一天一次 daily", 'monthly', true);

        $str = '{"STS":true,"MSG":"success","DATA":[{"loan_amount":"1000","loan_period_value":6,"loan_period_unit":"month","repayment_type":"annuity_scheme","repayment_period":"monthly","interest_rate":"6.00","interest_rate_type":"0","interest_rate_unit":"yearly","admin_fee":"10.00","loan_fee":"10.00","arrival_amount":990,"product_info":{"product_id":"8","product_name":"Credit Loan","product_code":"cl"},"interest_info":{"uid":"29","product_id":"8","currency":"USD","loan_size_min":"100.00","loan_size_max":"1000.00","min_term_days":"100","max_term_days":"700","guarantee_type":null,"mortgage_type":null,"interest_payment":"annuity_scheme","interest_rate":"6.00","interest_rate_unit":"yearly","interest_rate_type":"0","interest_min_value":"0.00","interest_rate_period":"monthly","is_prime_rate":"0","prime_rate_id":"0","admin_fee":"10.00","admin_fee_type":"1","operation_fee":"0.50","operation_fee_unit":"yearly","operation_fee_type":"0","operation_min_value":"2.00","grace_days":"10","update_time":"2017-12-21 10:34:47"},"period_repayment_amount":171.6,"total_repayment":{"total_payment":1029.6,"total_principal":1000,"total_interest":17.6,"total_operator_fee":12,"total_period_pay":171.6},"repayment_schema":[{"scheme_index":1,"receivable_principal":164.6,"receivable_interest":5,"receivable_operation_fee":2,"remaining_principal":835.4,"amount":171.6},{"scheme_index":2,"receivable_principal":165.42,"receivable_interest":4.18,"receivable_operation_fee":2,"remaining_principal":669.98,"amount":171.6},{"scheme_index":3,"receivable_principal":166.25,"receivable_interest":3.35,"receivable_operation_fee":2,"remaining_principal":503.73,"amount":171.6},{"scheme_index":4,"receivable_principal":167.08,"receivable_interest":2.52,"receivable_operation_fee":2,"remaining_principal":336.65,"amount":171.6},{"scheme_index":5,"receivable_principal":167.92,"receivable_interest":1.68,"receivable_operation_fee":2,"remaining_principal":168.73,"amount":171.6},{"scheme_index":6,"receivable_principal":168.73,"receivable_interest":0.87,"receivable_operation_fee":2,"remaining_principal":0,"amount":171.6}]}],"CODE":200,"logger":[]}';
        $arr = json_decode($str,true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => $arr['DATA']
        );

    }
}