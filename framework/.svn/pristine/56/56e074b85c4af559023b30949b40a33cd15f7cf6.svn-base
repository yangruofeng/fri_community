<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/30
 * Time: 18:16
 */
class creditLoanContractConfirmApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan contract confirm ";
        $this->description = "合同确认";
        $this->url = C("bank_api_url") . "/credit_loan.contract.confirm.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
            )
        );

    }
}