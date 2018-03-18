<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 17:38
 */
class memberCertFamilyRelationshipApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Family relationship";
        $this->description = "家庭关系证明";
        $this->url = C("bank_api_url") . "/member.cert.family.relationship.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("relation_type", "关系类型", '', true);
        $this->parameters[]= new apiParameter("relation_name", "关系人名字", '', true);
        $this->parameters[]= new apiParameter("relation_cert_type", "关系人证件类型", '', true);
        $this->parameters[]= new apiParameter("relation_cert_photo", "关系人证件照片，文件流", '', true);
        $this->parameters[]= new apiParameter("country_code", "电话国际号", '', true);
        $this->parameters[]= new apiParameter("relation_phone", "电话", '', true);
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