<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/12
 * Time: 16:03
 */
// member.get.savings.balance
class memberGetSavingsBalanceApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member get savings balance";
        $this->description = "会员获取储蓄账户余额";
        $this->url = C("bank_api_url") . "/member.get.savings.balance.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'savings_balance' => '各币种余额'
            )

        );

    }
}