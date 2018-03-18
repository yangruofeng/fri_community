<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/7
 * Time: 10:21
 */
// member.bind.bank.delete
class memberBindBankDeleteApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member bind bank delete";
        $this->description = "会员删除绑定的银行卡";
        $this->url = C("bank_api_url") . "/member.bind.bank.delete.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("bind_id", "绑定的handler ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}