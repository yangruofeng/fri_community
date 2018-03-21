<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 13:09
 */
// loan.product.rate.list
class loanProductRateListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Product Rate List";
        $this->description = "贷款产品列表";
        $this->url = C("bank_api_url") . "/loan.product.rate.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("product_id", "产品id", 1,true);
        $this->parameters[]= new apiParameter("currency", "币种", 'USD');
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_num' => '总条数',
                'total_pages' => '总页数',
                'current_page' => '当前页',
                'page_size' => '每页条数',
                'list' => array(
                    array(
                        'uid' => '利率ID',
                        'product_id' => '产品id',
                        'currency' => '币种',
                        'loan_size_min' => '最低贷款金额',
                        'loan_size_max' => '最高贷款金额',
                        'repayment_type' => '还款方式',
                        'repayment_period' => '还款周期，只有分期付款才有',
                        'loan_term_time' => '可贷款时间周期',
                        'interest_rate_des_value' => '利息利率值',
                        'interest_rate_unit' => '利息利率周期',
                        'operation_fee_des_value' => '运营费利率值',
                        'operation_fee_unit' => '运营费利率周期',
                    )
                )
            )
        );

    }
}