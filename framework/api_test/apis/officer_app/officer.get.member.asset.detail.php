<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 14:31
 */
class officerGetMemberAssetDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member one asset detail";
        $this->description = "会员某项资产明细";
        $this->url = C("bank_api_url") . "/officer.get.member.asset.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("asset_id", "资产ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'asset_detail' => array(
                    'valuation' => '估值',
                    'remark' => '备注'
                )
            )
        );

    }
}