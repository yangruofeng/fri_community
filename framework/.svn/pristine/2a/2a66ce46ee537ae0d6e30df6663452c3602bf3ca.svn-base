<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 16:30
 */
// member.asset.delete
class memberAssetDeleteApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset Delete";
        $this->description = "会员资产删除";
        $this->url = C("bank_api_url") . "/member.asset.delete.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("asset_id", "资产id",2, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}