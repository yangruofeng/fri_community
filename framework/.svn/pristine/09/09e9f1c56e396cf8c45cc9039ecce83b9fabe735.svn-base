<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 17:55
 */
class creditLoanPreviewApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit Loan Preview";
        $this->description = "贷款预览";
        $this->url = C("bank_api_url") . "/credit_loan.preview.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("product_id", "贷款产品id", 1, true);
        $this->parameters[]= new apiParameter("amount", "贷款金额", 500, true);
        $this->parameters[]= new apiParameter("loan_period", "贷款周期，单位月", 6, true);
        $this->parameters[]= new apiParameter("repayment_type", "还款方式，等额本息 annuity_scheme，等额本金 fixed_principal,一次偿还（每月计算一次利息） single_repayment，固定利息 flat_interest，先利息后本金 balloon_interest", 'annuity_scheme', true);
        $this->parameters[]= new apiParameter("repayment_period", "还款周期,一年一次 yearly，半年一次 semi_yearly，一季度一次 quarter，一月一次 monthly,一周一次 weekly，一天一次 daily", 'monthly', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'product_info' => '产品信息',
                'loan_fee' => '贷款手续费',
                'arrival_amount' => '实际到手贷款金额',
                'interest_rate' => '利率值',
                'interest_rate_type' => '利率类型 0 百分比 1 固定金额',
                'period_repayment_amount' => '平均每期还款金额',
                'total_repayment'=> '还款合计',
                'repayment_schema' => array(
                    array(
                        'scheme_index' => '还款计划序号',
                        'receivable_principal' => '应还本金',
                        'receivable_interest' => '应还利息',
                        'receivable_operation_fee' => '运营费',
                        'amount' => '当期应还总额',
                        'remaining_principal' => '剩余应还本金',
                    )
                )
            )
        );

    }
}