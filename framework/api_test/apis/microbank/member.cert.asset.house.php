<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/5
 * Time: 13:25
 */
class memberCertAssetHouseApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset house cert";
        $this->description = "房屋认证";
        $this->url = C("bank_api_url") . "/member.cert.asset.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "认证类型,固定值 house", 'house', true);

        $this->parameters[]= new apiParameter("x_coordinate", "上传位置X坐标", '23.125487', true);
        $this->parameters[]= new apiParameter("y_coordinate", "上传位置Y坐标", '48.125487', true);
        $this->parameters[]= new apiParameter("property_card", "房产证照片，文件流", null, true);
        $this->parameters[]= new apiParameter("house_front", "房屋正面照片，文件流", null, true);
        $this->parameters[]= new apiParameter("house_front_road", "房屋门前马路照片，文件流", null, true);
        $this->parameters[]= new apiParameter("house_side_face", "房屋侧面照片，文件流", null, true);
        $this->parameters[]= new apiParameter("house_inside", "房屋内景照片，文件流,非必传", null, false);
        $this->parameters[]= new apiParameter("house_relationships_certify", "房屋关系证明，文件流,非必传", null, false);

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