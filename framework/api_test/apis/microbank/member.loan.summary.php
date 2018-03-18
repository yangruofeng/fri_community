<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/18
 * Time: 11:15
 */
// member.loan.summary
class memberLoanSummaryApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Loan Summary";
        $this->description = "会员贷款统计";
        $this->url = C("bank_api_url") . "/member.loan.summary.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'own_loan_summary' => array(
                    '@description'=> '自己贷款的',
                    'contract_num_summary' => array(
                        'total_contracts' => '合计合同（审批通过的）',
                        'processing_contracts' => '正常进行的合同（含逾期的）',
                        'delinquent_contracts' => '延期合同',
                        'normal_processing_contracts' => '正常进行无逾期',
                        'complete_contracts' => '还款完成合同',
                        'rejected_contracts' => '被拒绝的合同',
                        'pending_approval_contracts' => '待审核的合同',
                        'write_off_contracts' => '核销合同'
                    ),
                    'contract_amount_summary' => array(
                        'total_principal' => '总共贷款本金',
                        'total_liabilities' => '合同总共应还金额',
                        'total_write_off_amount' => '核销合同的额度',
                        'total_Outstanding_Write_off_balance' => '坏账损失金额'
                    ),
                    'next_schema' => array(
                        '@description' => '没有为null',
                        'repayment_time' => '下期还款时间',
                        'repayment_amount' => '金额',
                        'currency' => '币种'

                    )
                ),
                'as_guarantee_loan_summary' => array(
                    '@description' => '作为担保人贷款的,格式同自己贷款的',
                ),
            )
        );

    }
}