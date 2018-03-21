<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/28
 * Time: 13:48
 */
class coSignInApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "CO sign in";
        $this->description = "签到";
        $this->url = C("bank_api_url") . "/co.sign.in.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1, true);
        $this->parameters[]= new apiParameter("coord_x", "经度", 23.325412, true);
        $this->parameters[]= new apiParameter("coord_y", "纬度", 48.212365, true);
        $this->parameters[]= new apiParameter("location", "地理位置", 'A street', true);
        $this->parameters[]= new apiParameter("remark", "备注", 'remark');



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}