<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/30
 * Time: 14:20
 */
class loanContractCreateApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Create";
        $this->description = "通用产品贷款合同创建（暂时不使用此api）";
        $this->url = C("bank_api_url") . "/credit_loan.contract.create.php";

        $this->parameters = array();
       /* $this->parameters[]= new apiParameter("member_id", '会员id', 1, true);
        $this->parameters[]= new apiParameter("product_id", "贷款产品id", 1, true);
        $this->parameters[]= new apiParameter("amount", "贷款金额", 500, true);
        $this->parameters[]= new apiParameter("loan_period", "贷款周期，单位月", 6, true);
        $this->parameters[]= new apiParameter("repayment_type", "还款方式，等额本息 annuity_scheme，等额本金 fixed_principal,一次偿还（每月计算一次利息） single_repayment，固定利息 flat_interest，先利息后本金 balloon_interest", 'annuity_scheme', true);
        $this->parameters[]= new apiParameter("repayment_period", "还款周期,一年一次 yearly，半年一次 semi_yearly，一季度一次 quarter，一月一次 monthly,一周一次 weekly，一天一次 daily", 'monthly', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);
        $this->parameters[] = new apiParameter('insurance_item_id','绑定的保险项目id',0);
        $this->parameters[] = new apiParameter('insurance_amount','保险购买金额，非固定价格产品需要',0);*/


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'contract_id' => '合同id',
                'loan_amount' => '贷款金额',
                'loan_period' => '贷款周期',
                'repayment_type' => '还款方式',
                'repayment_period' => '还款周期',
                'lending_time' => '出借时间',
                'product_info' => '产品信息',
                'product_size_interest' => '产品贷款利率信息',
                'contract_detail' => '合同详细信息',
                'loan_disbursement_scheme' => '放款计划',
                'loan_installment_scheme' => '还款计划'
            )
        );

    }
}