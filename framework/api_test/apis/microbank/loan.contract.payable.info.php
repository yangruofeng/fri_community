<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/11
 * Time: 16:06
 */
// loan.contract.payable.info
class loanContractPayableInfoApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Payable Info";
        $this->description = "贷款合同欠款信息";
        $this->url = C("bank_api_url") . "/loan.contract.payable.info.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_overdue_amount' => '合计逾期应还本息',
                'total_overdue_penalty' => '合计逾期应还罚金',
                'total_payable_amount' => '合计剩余应还金额',
                'next_repayment_date' => '下一期还款日期',
                'next_repayment_amount' => '下一期还款金额',
                'last_request_repayment_info' => '最后一次申请还款的信息'
            )
        );

    }
}