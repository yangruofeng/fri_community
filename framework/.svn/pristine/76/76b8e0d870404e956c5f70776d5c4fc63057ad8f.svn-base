<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/1
 * Time: 17:09
 */
class memberCertIdApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "ID cert";
        $this->description = "身份证认证";
        $this->url = C("bank_api_url") . "/member.cert.id.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("cert_sn", "证件号码", '123456', true);
        $this->parameters[]= new apiParameter("name_en", "英文名字", 'test', true);
        $this->parameters[]= new apiParameter("name_kh", "柬文名字", 'ឈ្មោះ', true);
        $this->parameters[]= new apiParameter("hand_photo", "手持照片，文件流", '', true);
        $this->parameters[]= new apiParameter("front_photo", "正面照，文件流", '', true);
        $this->parameters[]= new apiParameter("back_photo", "背面照，文件流", '', true);
        $this->parameters[]= new apiParameter("cert_id", "如果是编辑，需要传记录id", null);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
            )
        );

    }
}