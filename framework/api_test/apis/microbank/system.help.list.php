<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/8
 * Time: 15:41
 */
class systemHelpListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Common Help List";
        $this->description = "系统帮助列表";
        $this->url = C("bank_api_url") . "/system.help.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("type", "类型 全部 all 信用贷 credit 保险 insurance ", 'all', true);
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
                    array(
                        'help_title' => '标题',
                        'help_content' => '内容',
                    )
                )

            )
        );

    }

}