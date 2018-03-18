<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/9
 * Time: 13:52
 */
// co.get.all.loan.product
class coGetAllLoanProductApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan product list";
        $this->description = "贷款产品列表";
        $this->url = C("bank_api_url") . "/co.get.all.loan.product.php";

        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'list' => array(
                    array(
                        'product_id' => '产品ID',
                        'product_code' => '产品code',
                        'product_name' => '产品名称',
                    )
                )

            )
        );

    }
}