<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 17:27
 */
// member.is.set.trading.password
class memberIsSetTradingPasswordApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member is set trading password ";
        $this->description = "会员是否设置交易密码";
        $this->url = C("bank_api_url") . "/member.is.set.trading.password.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'is_set' => '0 否 1 是',
                'verify_amount' => '启用密码金额',
                'currency' => '金额货币'
            )
        );

    }
}