<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/27
 * Time: 17:18
 */

class systemCompanyInfoApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "System Company Info";
        $this->description = "公司信息";
        $this->url = C("bank_api_url") . "/system.company.info.php";

        $this->parameters = array();



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'address_region' => '地址区域',
                'address_detail' => '详细地址',
                'company_name' => '公司名称',
                'hotline' => array(
                    '8552365478',
                    '966854123'
                ),
                'email' => '公司邮箱',
                'file' => '',
                'company_icon' => '公司icon',
                'coord_x' => 'X坐标',
                'coord_y' => 'Y坐标',
                'description' => '公司介绍',
                'branch_list' => '分支列表'

            )
        );

    }

}