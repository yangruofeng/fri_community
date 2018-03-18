<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 16:24
 */
class officerGetMemberAssessmentApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member assessment";
        $this->description = "会员综合评估";
        $this->url = C("bank_api_url") . "/officer.get.member.assessment.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'asset_evaluation' => '资产估值',
                'business_profitability' => '业务盈利能力'
            )
        );

    }
}