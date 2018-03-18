<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/23
 * Time: 15:04
 */
// member.base.info.php
class memberBaseInfoApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member base info";
        $this->description = "会员基本信息";
        $this->url = C("bank_api_url") . "/member.base.info.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
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