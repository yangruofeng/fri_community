<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/15
 * Time: 17:53
 */
class memberAccountIndexApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Account sum ";
        $this->description = "账户统计";
        $this->url = C("bank_api_url") . "/member.account.index.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'credit' => array(
                    'credit' => '信用',
                    'balance' => '信用余额'
                ),
                'loan_total' => '贷款总额（本金）',
                'loan_balance' => '贷款余额（本金）',
                'loan_total_repayable' => '贷款合计欠款（本息+罚金）',
                'insurance_total' => '保险总额',
                'processing_loan_contracts' => '进行中的贷款合同数',
                'processing_insurance_contracts' => '进行中的保险合同数'
            )
        );

    }

}