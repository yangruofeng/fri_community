<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 17:02
 */
// member.family.relationship.delete

class memberFamilyRelationshipDeleteApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Family Delete";
        $this->description = "会员家庭关系移除";
        $this->url = C("bank_api_url") . "/member.family.relationship.delete.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("relation_id", "家庭关系id",2, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}