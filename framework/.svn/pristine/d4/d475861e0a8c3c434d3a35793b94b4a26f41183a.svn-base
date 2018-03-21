<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/9
 * Time: 15:54
 */
// co.loan.request.bind.product
class coLoanRequestBindProductApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan request bind product";
        $this->description = "CO 为贷款申请绑定产品";
        $this->url = C("bank_api_url") . "/co.loan.request.bind.product.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("request_id", "申请 id", 1, true);
        $this->parameters[]= new apiParameter("product_id", "产品id", 1, true);
        $this->parameters[]= new apiParameter("repayment_type", "还款方式", 'annuity_scheme', true);
        $this->parameters[]= new apiParameter("repayment_period", "还款周期，还款方式不是分期还款可不传", 'monthly');
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
                'interest_info' => '利率信息，没有匹配的为null'
            )
        );

    }
}