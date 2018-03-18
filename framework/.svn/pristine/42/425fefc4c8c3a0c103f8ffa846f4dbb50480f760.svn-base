<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 14:19
 */
class systemConfigInitApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "System config init ";
        $this->description = "系统一些配置初始化";
        $this->url = C("bank_api_url") . "/system.config.init.php";

        $this->parameters = array();



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'user_define' => array(
                    '@description' =>
                    '用户定义的选项有多语言返回，格式如下 ,key含义 loan_use 贷款用途 gender 性别  marital_status 婚姻状态 occupation 职业 family_relationship 家庭关系  guarantee_relationship 担保人关系类型 ',
                    'loan_use' => array(
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
                    ),
                ),
                'system_define' => array(
                    '@description' =>
                    '系统内置的只有值，没有语言 key含义 currency 币种 repayment_type 还款方式 repayment_period 还款周期 credit_loan_level 贷款时间单位 loan_time_unit',
                    'repayment_type' => array(
                        "single_repayment","fixed_principal","annuity_scheme","flat_interest","balloon_interest"
                    ),

                )


            )
        );

    }

}