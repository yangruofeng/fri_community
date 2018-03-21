<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 17:50
 */
class memberCertWorkApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Work cert";
        $this->description = "工作证明";
        $this->url = C("bank_api_url") . "/member.cert.work.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("company_name", "公司名称", '', true);
        $this->parameters[]= new apiParameter("company_address", "公司地址", '', true);
        $this->parameters[]= new apiParameter("position", "职位", '', true);
        $this->parameters[]= new apiParameter("is_government", "是否政府员工 是1 否 0", 0, true);
        $this->parameters[]= new apiParameter("work_card", "工作卡，文件流", '', true);
        $this->parameters[]= new apiParameter("employment_certification", "雇佣证明，文件流", '', true);
        $this->parameters[]= new apiParameter("cert_id", "如果是编辑，需要传记录id", null);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'cert_result' => '基本信息',
                'extend_info' => '扩展信息'
            )
        );

    }
}