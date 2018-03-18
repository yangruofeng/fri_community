<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/9
 * Time: 13:15
 */
// co.bound.loan.request.list
class coBoundLoanRequestListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "CO bound loan request";
        $this->description = "绑定到CO的贷款申请";
        $this->url = C("bank_api_url") . "/co.bound.loan.request.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "CO id", 1, true);
        $this->parameters[] = new apiParameter('state','类型 0 全部 1 待处理 2 拒绝的 3 审核通过的',0,true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);




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
                        'applicant_name' => '申请人名字',
                        'apply_amount' => '申请金额',
                        'currency' => '货币',
                        'contact_phone' => '联系电话',
                        'apply_time' => '申请时间',
                        'state' => '状态 0-100'
                    )
                )

            )
        );

    }
}