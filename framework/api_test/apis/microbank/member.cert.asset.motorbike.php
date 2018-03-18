<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/5
 * Time: 13:14
 */

class memberCertAssetMotorbikeApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset motorbike cert";
        $this->description = "摩托车认证";
        $this->url = C("bank_api_url") . "/member.cert.asset.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "认证类型,固定值 motorbike", 'motorbike', true);
        $this->parameters[]= new apiParameter("motorbike_photo", "摩托车照片，文件流", null, true);
        $this->parameters[]= new apiParameter("certificate_front", "摩托车证件正面照，文件流", null, true);
        $this->parameters[]= new apiParameter("certificate_back", "摩托车证件背面照，文件流", null, true);
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