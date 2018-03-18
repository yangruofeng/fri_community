<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 11:10
 */
class loanProductListsApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Product Lists";
        $this->description = "贷款产品列表";
        $this->url = C("bank_api_url") . "/loan.product.list.php";

        $this->parameters = array();
        //$this->parameters[]= new apiParameter("token", "登陆令牌", '1acb452c5ee50c25307c5212f6a00dc8', true);
        //$this->parameters[]= new apiParameter("pageNum", "页码", 1);
        //$this->parameters[]= new apiParameter("pageSize", "每页条数", 10);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '产品详情',
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
                )

            )
        );

    }
}