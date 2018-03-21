<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/6
 * Time: 18:01
 */
// member.mortgage.goods.list.php
class memberMortgageGoodsListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member mortgage goods List";
        $this->description = "会员抵押的物品";
        $this->url = C("bank_api_url") . "/member.mortgage.goods.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "身份令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'list' => array(
                    '@description' => '没有为null',
                    array(
                        'uid' => '资产ID',
                        'asset_type' => '资产类型',
                        'valuation' => '估值',
                        'mortgage_time' => '抵押时间',
                        'main_image' => '图片'
                    )
                )
            )
        );

    }
}