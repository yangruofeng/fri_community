<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/16
 * Time: 16:09
 */
// loan.contract.get.pay.off.detail

class loanContractGetPayOffDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Pay Off Detail";
        $this->description = "合同提前还款全额应还详细";
        $this->url = C("bank_api_url") . "/loan.contract.get.pay.off.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                /*'contract_info' => '合同信息',
                'pay_off_total' => array(
                    'pay_off_principal' => '应还本金',
                    'pay_off_interest' => '应还利息',
                    'pay_off_penalty' => '应还罚金',
                    'pay_off_commission' => '提前还款手续费',
                    'pay_off_total_amount' => '合计应还'
                )*/
                'total_prepayment_amount' => '合计提前偿还总额',
                'currency' => '应还利息',
                'total_paid_principal' => '合计本金',
                'total_paid_interest' => '合计利息',
                'total_paid_penalty' => '合计罚金',
                'total_paid_commission' => '合计手续费',

            )
        );

    }
}