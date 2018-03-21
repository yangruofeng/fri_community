<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 15:55
 */
// credit_loan.rate.credit.level.php
class credit_loanRateCreditLevelApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Get Product Rate Credit Level ";
        $this->description = "通过利率信息查询对应信用认证等级";
        $this->url = C("bank_api_url") . "/credit_loan.rate.credit.level.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("rate_id", "利率id", 1, true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '信用等级信息'
        );

    }
}