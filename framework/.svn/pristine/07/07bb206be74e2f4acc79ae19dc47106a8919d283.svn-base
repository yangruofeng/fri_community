<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 10:32
 */
// client.member.get.fingerprint
class clientMemberGetFingerprintApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Get client member fingerprint";
        $this->description = "获取会员指纹库";
        $this->url = C("bank_api_url") . "/client.member.get.fingerprint.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员GUID", 10000001, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'member_id' => '',
                'finger_index' => '',
                'feature_img' => '',
                'feature_data' => ''
            )
        );

    }
}