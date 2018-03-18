<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/9
 * Time: 17:30
 */
class memberEditAvatorApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Edit Avator";
        $this->description = "会员修改头像";
        $this->url = C("bank_api_url") . "/member.edit.profile.avator.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("avator", "头像剪切图片，文件流", '', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'member_image' => '原图',
                'member_icon' => '头像剪切图'
            )
        );

    }
}