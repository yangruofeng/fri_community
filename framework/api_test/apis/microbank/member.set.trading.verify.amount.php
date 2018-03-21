<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 17:45
 */
// member.set.trading.verify.amount
class memberSetTradingVerifyAmountApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Member  set trading verify amount ";
        $this->description = "设置交易密码启用金额";
        $this->url = C("bank_api_url") . "/member.set.trading.verify.amount.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("amount", "金额", 100, true);
        $this->parameters[]= new apiParameter("currency", "货币", 'USD', true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}