<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/31
 * Time: 11:13
 */

// member.query.credit
class memberQueryCreditApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member query credit";
        $this->description = "会员信用信息";
        $this->url = C("bank_api_url") . "/member.query.credit.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'credit' => '信用值',
                'balance' => '可用信用',
                'evaluate_time' => '最后授信时间',
            )
        );

    }
}