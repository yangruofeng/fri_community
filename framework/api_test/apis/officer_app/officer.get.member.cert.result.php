<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 16:40
 */
// officer.get.member.cert.result
class officerGetMemberCertResultApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Cert List";
        $this->description = "会员认证列表";
        $this->url = C("bank_api_url") . "/officer.get.member.cert.result.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[] = new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '认证列表,各类型含义 1 身份证 2 户口本 3 护照 4 房产 5 汽车资产 6 工作证明 7 公务员（合在工作）8 家庭关系证明 9 土地 10 居住证  11 摩托车',
                1 => '-10 未认证 -1 资料审核中 0 待审核 10 认证通过 11 已过期 100 认证失败',
                2 => '资产认证（如摩托车，房屋，汽车，土地等）返回的是已认证数量，如0,1,2',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7=> '',
                8=> '',
                9=> ''
            )
        );
    }
}