<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/16
 * Time: 17:32
 */
// loan.contract.prepayment.preview

class loanContractPrepaymentPreviewApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Loan Contract Prepayment Preview";
        $this->description = "提前还款预览";
        $this->url = C("bank_api_url") . "/loan.contract.prepayment.preview.php";

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
                'need_pay' => array(
                    '@description' => '应正常还的部分信息',
                    'total_overdue_amount' => '逾期计划总额',
                    'next_repayment_date' => '最近还款日',
                    'next_repayment_amount' => '最近应还款金额（本金+利息）',
                    'total_left_principal' => '可提前还剩余本金',
                    'total_left_periods' => '可提前还的剩余期数',
                    'total_need_pay' => array(
                        '@description' => '必还统计',
                        'total' => '合计必还总额',
                        'principal' => '合计应还本金',
                        'interest' => '合计应还利息',
                        'penalty' => '合计应还罚金'
                    ),
                ),
                'prepayment_principal' => '提前还款本金金额',
                'prepayment_fee' => '提前还款手续费',
                'total_prepayment_amount' => '合计总额',
                'left_schema' => '剩余计划'
            )
        );

    }
}