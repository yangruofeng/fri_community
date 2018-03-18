<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/28
 * Time: 16:56
 */
class coGetFootprintListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "CO get footprint list";
        $this->description = "获取足迹列表";
        $this->url = C("bank_api_url") . "/co.get.footprint.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "CO id", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'total_num' => '总条数',
                'total_pages' => '总页数',
                'current_page' => '当前页',
                'page_size' => '每页条数',
                'list' => array(
                    '@description' => '记录列表，没有为null',
                    array(
                        'coord_x' => '经度',
                        'coord_y' => '纬度',
                        'location' => '地理位置',
                        'sign_month' => '签到年月',
                        'sign_day' => '签到年月日',
                        'sign_time' => '签到时间',
                        'remark' => '备注'
                    )
                )

            )
        );

    }
}