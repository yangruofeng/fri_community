<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/18
 * Time: 17:51
 */

// member.guarantee.list
class memberGuaranteeListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member A guarantee list ";
        $this->description = "客户担保人列表 ";
        $this->url = C("bank_api_url") . "/member.guarantee.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'guarantee_list' => array(
                    '@description' => '担保人列表',
                    'uid' => '关系id',
                    'relation_type' => '关系类型',
                    'relation_type_name_json' => array(
                        '@description' => '关系类型多语言',
                        'en' => '',
                        'kh' => '',
                        'zh_cn' => '',
                    ),
                    'relation_state' => '0 创建, 11 拒绝 100 接受',
                    'display_name' => '英文名字',
                    'kh_display_name' => '柬文名字',
                    'member_icon' => '头像',
                    'phone_id' => '电话',
                ),
                'apply_list' => array(
                    '@description' => '别人申请列表,同上担保人列表',
                )
            )
        );

    }
}