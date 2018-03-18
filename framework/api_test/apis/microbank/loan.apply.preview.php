<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/5
 * Time: 13:14
 */
// loan.apply.preview
class loanApplyPreviewApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan apply preview";
        $this->description = "贷款申请预览";
        $this->url = C("bank_api_url") . "/loan.apply.preview.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("amount", "贷款金额", 1000, true);
        $this->parameters[]= new apiParameter("loan_time", "贷款时间,单位是月", 1, true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_interest_rate' => '合计利率信息',
                'total_repayment_amount' => '合计还款总额'
            )
        );

    }
}