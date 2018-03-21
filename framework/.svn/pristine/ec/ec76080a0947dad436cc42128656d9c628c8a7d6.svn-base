<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/25
 * Time: 17:49
 */
// loan.contract.schema.disbursement.detail
class loanContractSchemaDisbursementDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract schema disbursement detail";
        $this->description = "贷款合同放款计划放款详细";
        $this->url = C("bank_api_url") . "/loan.contract.schema.disbursement.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("schema_id", "放款计划id", 1, true);
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