<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/12
 * Time: 17:03
 */
class creditLoanBindInsuranceApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Bind Insurance";
        $this->description = "贷款绑定的保险产品";
        $this->url = C("bank_api_url") . "/credit_loan.bind.insurance.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("loan_product_id", "贷款产品id", 1, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '保险列表'
        );

    }
}