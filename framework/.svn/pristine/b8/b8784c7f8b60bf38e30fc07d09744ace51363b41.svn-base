<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 14:24
 */
class loanContractDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Detail";
        $this->description = "贷款合同详细";
        $this->url = C("bank_api_url") . "/loan.contract.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", '合同id', 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'contract_id' => '合同id',
                'is_can_repay' => '是否可进行还款，0 不能 1 能',
                'loan_amount' => '贷款金额',
                'currency' => '货币类型',
                'loan_period_value' => '贷款周期',
                'loan_period_unit' => '贷款周期单位 year 年 month 月 day 日',
                'repayment_type' => '还款方式',
                'repayment_period' => '还款周期',
                'interest_rate' => '利率值',
                'interest_rate_type' => '利率类型',
                'interest_rate_unit' => '利率周期，yearly 年 monthly 月 daily 日',
                'due_date' => '还款日',
                'due_date_type' => '还款日类型，once 一次还，日期 yearly 每年（周期的月-日）  monthly 每月多少号 weekly 每周几（0-6）daily 每天',
                'admin_fee' => '管理费',
                'insurance_fee' => '保险费',
                'operation_fee' => '总计运营费',
                'total_interest' => '合计利息',
                'actual_receive_amount' => '实发款金额',
                'total_repayment' => '总计应还本金+利息',
                'lending_time' => '贷款时间',
                'contract_info' => '合同详细信息',
                'loan_product_info' => '贷款产品信息',
                'interest_info' => '贷款利率信息',
                'size_rate' => '基本利率信息',
                'special_rate' => '特殊利率信息',
                'loan_disbursement_scheme' =>array(
                    'des' => '放款计划',
                    'state' => 'state含义 0 新创建 10 执行中 11 失败 100 完成',
                    'disbursable_date' => '应放款时间',
                ) ,
                'loan_installment_scheme' => array(
                    'des' =>'还款计划',
                    'state' => 'state含义 0 新创建 10 执行中 11 失败 100 完成',
                    'receivable_date' => '应还款时间',
                    'penalty_start_date' => '罚款开始时间',
                    'amount' => '应还本息',
                    'actual_payment_amount' => '实际还款金额',
                    'penalty' => '应还罚金',
                    'last_repayment_time' => '上次还款时间',
                    'done_time' => '还清时间'
                ),
                'bind_insurance' => '绑定的保险合同',

            )
        );

    }
}