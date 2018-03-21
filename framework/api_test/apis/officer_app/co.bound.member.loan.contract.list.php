<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/11
 * Time: 14:40
 */
 // co.bound.member.loan.contract.list
 class coBoundMemberLoanContractListApiDocument extends apiDocument
 {
     public function __construct()
     {
         $this->name = "CO bound member loan contract";
         $this->description = "绑定到CO的会员的贷款合同";
         $this->url = C("bank_api_url") . "/co.bound.member.loan.contract.list.php";

         $this->parameters = array();
         $this->parameters[]= new apiParameter("officer_id", "CO id", 1, true);
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
                     '@description' => '合同列表',
                 )

             )
         );

     }


 }