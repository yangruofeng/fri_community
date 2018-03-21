<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 12:33
 */
class memberInsuranceContractListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Insurance Contract List";
        $this->description = "会员保险合同列表";
        $this->url = C("bank_api_url") . "/member.insurance.contract.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "合同类型 1 全部合同 2 进行中的合同 3 待审核 4 过期合同 ", 1, true);
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
                        'currency' => '货币',
                        'price' => '保险价格',
                        'start_insured_amount' => '初保金额',
                        'tax_fee' => '税费',
                        'start_date' => '开始时间',
                        'end_date' => '结束时间，null 为永久有效',
                        'state' => '合同状态',
                    ),
                    array()
                )

            )
        );

    }


}