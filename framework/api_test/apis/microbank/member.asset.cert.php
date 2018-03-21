<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 16:40
 */
class memberAssetCertApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset cert";
        $this->description = "会员资产认证";
        $this->url = C("bank_api_url") . "/member.cert.asset.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "认证类型：摩托车 motorbike 房屋 house 汽车 car 土地 land,其他参数查看具体api介绍", 'house', true);
        $this->parameters[]= new apiParameter("cert_id", "如果是编辑，需要传记录id", null);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'cert_result' => '基本信息',
                'extend_info' => '扩展信息'
            )
        );

    }
}