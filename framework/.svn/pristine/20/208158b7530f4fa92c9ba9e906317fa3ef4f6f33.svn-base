<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 11:25
 */
class memberLoanContractListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Loan Contract List";
        $this->description = "会员贷款合同列表";
        $this->url = C("bank_api_url") . "/member.loan.contract.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("loan_type", "贷款类型 0 自己的贷款 1 担保的贷款 ", 0, true);
        $this->parameters[]= new apiParameter("type", "合同类型 1 全部合同 2 执行中的合同 3 待审核 4 逾期合同 5 完成的合同（还款完成） 6 正常执行无逾期的 20 信用贷全部合同", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_num' => '总条数',
                'total_pages' => '总页数',
                'current_page' => '当前页',
                'page_size' => '每页条数',
                'list' => array(
                    array(
                        'uid' => '合同id',
                        'contract_sn' => '合同编号',
                        'apply_amount' => '申请金额',
                        'currency' => '货币',
                        'start_date' => '开始时间',
                        'end_date' => '结束时间',
                        'state' => '合同状态',
                        'left_period' => '剩余未还期数',
                        'left_principal' => '剩余未还本金',
                    ),
                    array()
                )

            )
        );

    }
}