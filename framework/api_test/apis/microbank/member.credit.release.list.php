<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/20
 * Time: 17:40
 */

class memberCreditReleaseListApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Member Credit List";
        $this->description = "会员授信历史列表";
        $this->url = C("bank_api_url") . "/member.credit.release.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页码", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



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

                )

            )
        );

    }


}