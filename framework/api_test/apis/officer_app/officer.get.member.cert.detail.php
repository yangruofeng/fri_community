<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 16:43
 */
// officer.get.member.cert.detail
class officerGetMemberCertDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Cert result";
        $this->description = "会员各项认证结果";
        $this->url = C("bank_api_url") . "/officer.get.member.cert.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("type", "类型 1 身份证 2 户口本 3 护照 4 房产 5 汽车资产  6 工作证明 7 公务员（合在工作）8 家庭关系证明 9 土地", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => 'cert_result  基础信息(家庭关系没有返回) extend_info 扩展信息(家庭关系-》多条，工作证明有)',
                'cert_result' => array(
                    'uid' => '基础认证id',
                    'cert_type' => '类型',
                    'cert_name' => '证件上名字',
                    'cert_sn' => '证件号码',
                    'cert_addr' => '证件地址',
                    'cert_expire_time' => '过期时间',
                    'source_type' => '资料来源',
                    'verify_remark' => '审核备注',
                    'verify_state' => '审核结果 -1 审核中 0未审核,10审核通过，100审核未通过',
                ),
                'extend_info' => array(
                    array(
                        '@description' => '家庭关系格式，可能是多条的',
                        'uid' => '扩展认证id',
                        'cert_id' => '基础认证id',
                        'relation_type' => '关系类型',
                        'relation_name' => '关系人名字',
                        'relation_cert_type' => '关系人证件类型',
                        'relation_cert_photo' => '关系人证件照片',
                        'relation_cert_sn' => '关系人证件号码',
                        'relation_phone' => '关系人电话',
                        'relation_state' => '审核状态，0=>创建，10=>无效，11=>解除,100=>核准',
                    ),
                    array(
                        '@description' => '工作证明',
                        'uid' => '扩展认证id',
                        'cert_id' => '基础认证id',
                        'company_name' => '公司名称',
                        'company_addr' => '公司地址',
                        'position' => '职位',
                        'month_salary' => '月薪',
                        'is_government' => '是否政府员工',
                        'state' => '认证状态 0 新建 10 审核中 20 当前active 30 历史',
                    )
                ),
            )
        );

    }
}