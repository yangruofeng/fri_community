<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 11:07
 */
class officerSubmitCertLandApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member asset land cert";
        $this->description = "土地认证";
        $this->url = C("bank_api_url") . "/officer.submit.cert.asset.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "认证类型,固定值 land", 'land', true);
        $this->parameters[]= new apiParameter("property_card", "产权证照片，文件流", null, true);
        $this->parameters[]= new apiParameter("trading_record", "交易记录，文件流", null, true);
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