<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 14:37
 */
class officerGetMemberAssetsEvaluateApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset list ";
        $this->description = "会员资产列表";
        $this->url = C("bank_api_url") . "/officer.get.member.assets.evaluate .php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_amount' => '',
                'list' => array(
                    array(
                        'uid' => '资产ID',
                        'asset_type' => '资产类型',
                        'valuation' => '估值',
                        'remark' => '备注'
                    )
                )
            )
        );

    }
}