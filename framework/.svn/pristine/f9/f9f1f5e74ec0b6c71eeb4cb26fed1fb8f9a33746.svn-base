<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/19
 * Time: 18:29
 */

// loan.contract.get.prepayment.detail
class loanContractGetPrepaymentDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract prepayment detail";
        $this->description = "贷款合同提前还款信息";
        $this->url = C("bank_api_url") . "/loan.contract.get.prepayment.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
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
                'last_prepayment_request' => array(
                    '@description' => '上次申请结果，没有为null',
                    'uid' => '请求id',
                    'contract_id' => '合同id',
                    'amount' => '申请应还总金额',
                    'currency' => '币种',
                    'prepayment_type' => '提前还款方式',
                    'repay_period' => '申请偿还期数',
                    'principal_amount' => '申请提前还本金',
                    'fee_amount' => '申请提前还本金手续费',
                    'apply_time' => '申请时间',
                    'state' => '状态 0 新建 10 审核中 11 审核拒绝 20 审核通过 30 已付款 40 钱到账，未处理合同 100 合同提前还款处理完成 101 合同提前还款处理失败 '
                ),
                'prepayment_payment_record' => array(
                    '@description' => '还款申请记录',
                    array(
                        'amount' => '金额',
                        'currency' => '币种',
                        'create_time' => '时间',
                        'state' => '处理状态,0 新加 20 处理中 21 收钱失败 30 已收钱 100 处理成功',
                    )
                )

            )
        );

    }
}