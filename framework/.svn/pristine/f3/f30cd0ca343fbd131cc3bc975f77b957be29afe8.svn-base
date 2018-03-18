<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 14:12
 */
// credit.loan.index.page
class creditLoanIndexPageApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit Loan Index";
        $this->description = "信用贷主页";
        $this->url = C("bank_api_url") . "/credit.loan.index.page.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'product_id' => '信用贷产品id',
                'credit_info' => array(
                    'is_active' => '是否激活（可用） 0 未激活 1 激活',
                    'credit' => '信用值',
                    'balance' => '信用余额',
                    'evaluate_time' => '最后授信时间，没有为null',
                    'credit_process' => array(
                        '@description' => '激活详情',
                        'fingerprint' => array(
                            '@description' => '指纹',
                            'is_check' => '是否需要录入指纹 1 是 0否',
                            'is_complete' => '是否完成 1 完成 0 未完成',
                        ),
                        'authorized_contract' => array(
                            '@description' => '授权合同',
                            'is_check' => '是否需要签订授权合同 1 是 0否',
                            'is_complete' => '是否完成 1 完成 0 未完成',
                        )
                    )
                ),
                'monthly_min_rate' => '最低月利率',
                'next_repayment_schema' => array(
                    '@description' => '下期应还款计划,没有为null',
                    'uid' => '计划ID',
                    'contract_id' => '合同ID',
                    'next_repayment_amount' => '应还款金额',
                    'currency' => '币种',
                )
//                'rate_list' => array(
//
//                    'total_num' => '总条数',
//                    'total_pages' => '总页数',
//                    'current_page' => '当前页',
//                    'page_size' => '每页条数',
//                    'list' => array(
//                        array(
//                            'uid' => '利率ID',
//                            'product_id' => '产品id',
//                            'currency' => '币种',
//                            'loan_size_min' => '最低贷款金额',
//                            'loan_size_max' => '最高贷款金额',
//                            'repayment_type' => '还款方式',
//                            'repayment_period' => '还款周期，只有分期付款才有',
//                            'loan_term_time' => '可贷款时间周期',
//                            'interest_rate_des_value' => '利息利率值',
//                            'interest_rate_unit' => '利息利率周期',
//                            'operation_fee_des_value' => '运营费利率值',
//                            'operation_fee_unit' => '运营费利率周期',
//                        )
//                    )
//                )
            )
        );

    }
}