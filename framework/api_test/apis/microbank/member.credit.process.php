<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/5
 * Time: 18:25
 */
// member.credit.process
class memberCreditProcessApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Credit process";
        $this->description = "会员信用认证过程";
        $this->url = C("bank_api_url") . "/member.credit.process.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'phone' => array(
                    '@description' => '电话',
                    'is_must' => '是否必须 1 必须 0 不',
                    'is_complete' => '是否完成 1 完成 0 没完成'
                ),
                'personal_info' => array(
                    '@description' => '个人信息',
                    'is_must' => '是否必须 1 必须 0 不',
                    'is_complete' => '是否完成 1 完成 0 没完成'
                ),
                'assets_cert' => array(
                    '@description' => '资产',
                    'is_must' => '是否必须 1 必须 0 不',
                    'is_complete' => '是否完成 1 完成 0 没完成'
                ),
                'fingerprint' => array(
                    '@description' => '指纹录入',
                    'is_must' => '是否必须 1 必须 0 不',
                    'is_complete' => '是否完成 1 完成 0 没完成'
                ),
                'authorized_contract' => array(
                    '@description' => '授权合同',
                    'is_must' => '是否必须 1 必须 0 不',
                    'is_complete' => '是否完成 1 完成 0 没完成'
                ),
                'credit_info' => array(
                    '@description' => '信用信息',
                    'credit' => '信用值',
                    'balance' => '信用余额'
                ),
                'is_active' => array(
                    '@description' => '信用是否可用 1 可用 0 不可用',
                ),

            )
        );

    }

}