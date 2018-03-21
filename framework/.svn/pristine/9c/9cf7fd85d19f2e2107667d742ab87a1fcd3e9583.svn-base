<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 14:41
 */
class memberAddAddressApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Add address";
        $this->description = "会员添加地址";
        $this->url = C("bank_api_url") . "/member.add.address.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[] = new apiParameter("id1", "一级地址ID", 1, true);
        $this->parameters[] = new apiParameter("id2", "二级地址ID", 52, true);
        $this->parameters[] = new apiParameter("id3", "三级地址ID", 99, true);
        $this->parameters[] = new apiParameter("id4", "四级地址ID", 125, true);
        $this->parameters[] = new apiParameter("full_text", "全路径详细地址", 'test', true);
        //$this->parameters[] = new apiParameter("cord_x", "经度", 28.2314854, true);
        //$this->parameters[] = new apiParameter("cord_y", "纬度", 145.125478, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'uid' => '地址ID',
                'coord_x' => '经度',
                'coord_y' => '纬度',
                'full_text' => '全地址',
                'create_time' => ''
            )
        );

    }
}