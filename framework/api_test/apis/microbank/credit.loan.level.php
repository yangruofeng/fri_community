<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/15
 * Time: 10:08
 */
class creditLoanLevelApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Credit loan level";
        $this->description = "信用贷等级";
        $this->url = C("bank_api_url") . "/credit_loan.loan.level.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter('level_type','all 全部 0 member 1 商家',0,true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'cert_list_des' =>'cert_list 类型 1 身份证 2 户口本 3 护照 4 房产 5 汽车资产 6 工作证明 7 公务员（合在工作）8 家庭关系证明 9 土地',
                0 => array(
                    'level_type' => '等级类型',
                    'min_amount' => '最低金额',
                    'max_amount' => '最高金额',
                    'disburse_time' => '放款时间',
                    'disburse_time_unit' => '放款时间单位  1 分钟 2小时 3 天',
                    'cert_list' => array(
                        1,2,
                    ),

                ),
                1 => array(
                    'level_type' => '等级类型',
                    'min_amount' => '最低金额',
                    'max_amount' => '最高金额',
                    'disburse_time' => '放款时间',
                    'disburse_time_unit' => '放款时间单位  1 分钟 2小时 3 天',
                    'cert_list' => array(1,2,4,5)
                )

            )
        );

    }
}