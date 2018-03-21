<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/28
 * Time: 14:02
 */
class coSignInFootprintApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "CO sign in footprint";
        $this->description = "签到记录";
        $this->url = C("bank_api_url") . "/co.sign.in.footprint.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1, true);
        $this->parameters[]= new apiParameter("date", "日期,yyyy-mm-dd格式", '2018-02-28', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '记录列表，没有为null',
                array(
                    'coord_x' => '经度',
                    'coord_y' => '纬度',
                    'location' => '地理位置',
                    'sign_time' => '签到时间',
                    'remark' => '备注'
                )
            )
        );

    }
}