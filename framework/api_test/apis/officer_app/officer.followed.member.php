<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 16:52
 */
// officer.followed.member
class officerFollowedMemberApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Officer follow member";
        $this->description = "业务员跟进的会员";
        $this->url = C("bank_api_url") . "/officer.followed.member.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '会员列表，没有为null',
            )
        );

    }
}