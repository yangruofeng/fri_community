<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 16:33
 */
// credit_loan.loan.max.month
class credit_loanLoanMaxMonthApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit loan max month";
        $this->description = "获取信用贷默认方式的最大贷款月数";
        $this->url = C("bank_api_url") . "/credit_loan.loan.max.month.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("product_id", "产品id", 1, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'min_month' => '最低月数',
                'max_month' => '最高月数'
            )
        );

    }
}