<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/22
 * Time: 17:03
 */

// loan.contract.schema.repayment.detail


class loanContractSchemaRepaymentDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract schema repayment detail";
        $this->description = "贷款合同计划还款详细";
        $this->url = C("bank_api_url") . "/loan.contract.schema.repayment.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("schema_id", "计划id", 1, true);
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