<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 14:29
 */
class officerGetMemberResidencePlaceApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Get member residence place";
        $this->description = "获取会员居住地址";
        $this->url = C("bank_api_url") . "/officer.get.member.residence.place.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[] = new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'address_info' => array(
                    '@description' => '没有为null',
                    'uid' => '地址ID',
                    'coord_x' => '经度',
                    'coord_y' => '纬度',
                    'full_text' => '全地址',
                    'create_time' => ''
                )

            )
        );

    }

}