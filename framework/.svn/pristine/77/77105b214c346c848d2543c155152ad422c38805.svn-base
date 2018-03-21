<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/10
 * Time: 13:34
 */
class memberSetFingerprintApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Set Fingerprint";
        $this->description = "会员设置指纹";
        $this->url = C("bank_api_url") . "/member.set.fingerprint.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("fingerprint", "指纹,URL编码参数", '', true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'fingerprint' => '指纹'
            )
        );

    }
}