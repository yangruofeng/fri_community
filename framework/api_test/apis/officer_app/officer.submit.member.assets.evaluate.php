<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 14:59
 */
class officerSubmitMemberAssetsEvaluateApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset evaluation submit";
        $this->description = "客户资产估值确认";
        $this->url = C("bank_api_url") . "/officer.submit.member.assets.evaluate.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("id", "资产ID", 1, true);
        $this->parameters[]= new apiParameter("valuation", "估值", 500, true);
        $this->parameters[]= new apiParameter("remark", "备注", '别墅', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array()
        );

    }
}