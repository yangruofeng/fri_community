<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 15:52
 */
class officerSubmitSuggestMemberCreditApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member credit suggest";
        $this->description = "客户授信推介";
        $this->url = C("bank_api_url") . "/officer.submit.suggest.member.credit.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", '', 1, true);
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("monthly_repayment_ability", "月还款能力", 500, true);
        $this->parameters[]= new apiParameter("suggest_credit", "推介信用值", 1000, true);
        $this->parameters[]= new apiParameter("remark", "备注", '');

        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array()
        );

    }
}