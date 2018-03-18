<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 11:16
 */
class creditLoanCreditCalculatorApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit calculator";
        $this->description = "信用额度计算器";
        $this->url = C("bank_api_url") . "/credit_loan.credit.calculator.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("values", "多个值用,隔开，如  1,2,16,32, 各类型的计算值 身份证 1 户口本 2 家庭关系4 工作证明8 政府员工16 汽车资产证明 32 房屋资产证明 64 
         土地 128 护照 256 居住证 512 摩托车 1024", '1,32,64', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '理论信用值，如 5000 '
        );

    }
}