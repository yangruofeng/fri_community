<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 10:27
 */
// client.member.set.fingerprint
class clientMemberSetFingerprintApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Set client member fingerprint";
        $this->description = "设置会员指纹库";
        $this->url = C("bank_api_url") . "/client.member.set.fingerprint.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员GUID", 10000001, true);
        $this->parameters[]= new apiParameter("finger_index", "序号", 1, true);
        $this->parameters[]= new apiParameter("feature_img", "指纹图片", 'test', true);
        $this->parameters[]= new apiParameter("feature_data", "指纹特征数据", 'test', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '保险列表'
        );

    }
}