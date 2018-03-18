<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 16:00
 */
class officerGetMemberWorkDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member work detail";
        $this->description = "会员工作详情";
        $this->url = C("bank_api_url") . "/officer.get.member.work.detail.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[] = new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'work_detail' => array(
                    '@description' => '没有认证为null',
                    'company_name' => '公司名字',
                    'company_addr' => '公司地址',
                    'position' => '职位',
                    'is_government' => '是否公务员',
                    'state' => '认证状态 0 新建 10 审核中 20 当前active 30 历史'
                )

            )
        );
    }
}