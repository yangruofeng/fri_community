<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/16
 * Time: 16:25
 */

// loan.contract.prepayment.apply
class loanContractPrepaymentApplyApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Prepayment Apply";
        $this->description = "贷款合同欠款信息";
        $this->url = C("bank_api_url") . "/loan.contract.prepayment.apply.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("prepayment_type", "提前还款类型 0 部分还款 1 全额还款 2 固定期数", 0, true);
        $this->parameters[]= new apiParameter("repay_period", "固定期数参数，偿还期数 ", 2);
        $this->parameters[]= new apiParameter("amount", "部分还款参数，本金金额", '100');
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '申请信息'
            )
        );

    }
}