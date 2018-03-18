<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/9
 * Time: 17:42
 */
class memberGetMyQrcodeApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Get Qrcode";
        $this->description = "会员二维码";
        $this->url = C("bank_api_url") . "/member.get.qrcode.url.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '二维码地址'
        );

    }
}