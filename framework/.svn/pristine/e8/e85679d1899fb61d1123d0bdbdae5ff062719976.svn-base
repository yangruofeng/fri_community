<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 13:05
 */
class systemDefineListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "System define ";
        $this->description = "系统定义项目";
        $this->url = C("bank_api_url") . "/system.define.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("type", "类型 1 性别 2 职业 3 家庭关系 4 贷款用途", 1, true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'uid' => 'ID',
                    'category' => '分类',
                    'category_name' => '分类名称',
                    'category_name_json' => '分类名称多语言',
                    'item_name' => '项目名称',
                    'item_name_json' => '项目名称多语言 en 英语 kh 柬语 zh_cn 中文',
                    'item_code' => '项目简码',
                    'item_desc' => '项目描述',
                    'item_value' => '项目计算值',
                    'is_system' => '是否系统内置'
                )
            )
        );

    }
}