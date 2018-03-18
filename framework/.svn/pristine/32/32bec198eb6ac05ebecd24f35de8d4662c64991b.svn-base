<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/5
 * Time: 10:44
 */
//loan.product.detail
class loanProductDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Product Detail";
        $this->description = "贷款产品详细";
        $this->url = C("bank_api_url") . "/loan.product.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("product_id", "产品ID", 1, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'product_info' =>
                array(
                    'uid' => '产品ID',
                    'product_code' => '产品Code',
                    'product_name' => '产品名',
                    'product_description' => '产品描述，富文本',
                    'product_qualification' => '贷款条件，富文本',
                    'product_feature' => '产品特色，富文本',
                    'product_required' => '贷款必须资料，富文本',
                    'product_notice' => '产品公告，富文本',
                    //'state' => '状态',
                    //'create_time' => '创建时间',
                    //'creator_id' => '创建者Id',
                    //'creator_name' => '创建者Name',
                    'update_time' => '更新时间',
                    'penalty_divisor_days' => '罚款利率除以多少天',
                    'penalty_on' => '罚款基数',
                    'penalty_rate' => '罚款利率',
                    'is_multi_contract' => '允许多合同标志',
                    'is_advance_interest' => '罚款利率',
                    'is_editable_penalty' => '是否允许合同修改罚款利率',
                    'is_editable_interest' => '是否允许修改利息',
                    'is_editable_grace_days' => '是否允许修改宽限天数',
                    'product_key' => '产品系列key',
                    'is_credit_loan' => '是否信用贷',
                    'min_rate_desc' => '最低利率',
                ),
                'rate_list' => array(
                    array(
                        'uid' => '利率ID',
                        'product_id' => '产品id',
                        'currency' => '币种',
                        'loan_size_min' => '最低贷款金额',
                        'loan_size_max' => '最高贷款金额',
                        'loan_term_time' => '可贷款时间周期',
                        'repayment_type' => '还款方式',
                        'repayment_period' => '还款周期，只有分期付款才有',
                        'interest_rate_des' => '利息利率值',
                        'total_rate_des_value' => '合计利息利率值(合计interest+operation)',
                        'interest_rate_unit' => '利息利率周期',
                        'operation_fees_des' => '运营费利率值',
                        'operation_fee_unit' => '运营费利率周期',
                    )
                )

            )
        );

    }
}