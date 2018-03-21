<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/3
 * Time: 11:41
 */
class loanContractCancelApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Contract Cancel";
        $this->description = "贷款合同取消";
        $this->url = C("bank_api_url") . "/loan.contract.cancel.php";

        $this->parameters = array();
         $this->parameters[]= new apiParameter("contract_id", '合同id', 1, true);
         $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}