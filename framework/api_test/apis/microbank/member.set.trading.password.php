<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/19
 * Time: 14:57
 */
// member.set.trading.password
class memberSetTradingPasswordApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Set trading password";
        $this->description = "会员设置交易密码";
        $this->url = C("bank_api_url") . "/member.set.trading.password.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("login_password", "登陆密码", '', true);
        $this->parameters[]= new apiParameter("id_no", "身份证最后4位", 'xxxx', true);
        $this->parameters[]= new apiParameter("trading_password", "交易密码", '', true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}