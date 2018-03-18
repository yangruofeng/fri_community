<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/19
 * Time: 9:28
 */
class memberLoanApplyListApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Member Loan Apply List";
        $this->description = "会员贷款申请列表";
        $this->url = C("bank_api_url") . "/member.loan.apply.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        //$this->parameters[]= new apiParameter("type", "类型 1 全部 2 执行中 3 待审核 4 逾期合同 ", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);



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
                        'uid' => 'id',
                        'member_id' => '会员id',
                        'product_id' => '产品id',
                        'product_name' => '产品名称',
                        'applicant_name' => '申请人',
                        'apply_amount' => '申请金额',
                        'currency' => '货币',
                        'contact_phone' => '联系电话',
                        'loan_propose' => '贷款目的',
                        'apply_time' => '申请时间',
                        'state' => '处理状态 0新建  10 处理中 20 处理完成'
                    )
                )

            )
        );

    }

}