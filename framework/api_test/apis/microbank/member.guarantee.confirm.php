<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/19
 * Time: 14:39
 */

//member.guarantee.confirm

class memberGuaranteeConfirmApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member  guarantee confirm  ";
        $this->description = "担保关系确认";
        $this->url = C("bank_api_url") . "/member.guarantee.confirm.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("uid", "关系ID", 1, true);
        $this->parameters[]= new apiParameter("state", "确认结果， 0 拒绝 1 接受", 0, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null

        );

    }
}