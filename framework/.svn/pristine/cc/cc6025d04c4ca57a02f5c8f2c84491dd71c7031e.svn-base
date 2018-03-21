<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/18
 * Time: 17:34
 */
class memberAceDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member ace info";
        $this->description = "客户绑定ACE账号信息";
        $this->url = C("bank_api_url") . "/member.ace.account.info.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '绑定的账户'
        );

    }
}