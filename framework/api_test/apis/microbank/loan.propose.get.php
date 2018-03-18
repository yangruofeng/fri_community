<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 11:10
 */
class loanProposeGetApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Propose Lists";
        $this->description = "贷款目的列表";
        $this->url = C("bank_api_url") . "/loan.propose.get.php";

        $this->parameters = array();



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => '目的列表'
        );

    }
}