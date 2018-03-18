<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/8
 * Time: 17:28
 */

class loanContractRepaymentApplyApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Loan Repayment Apply";
        $this->description = "贷款还款申请";
        $this->url = C("bank_api_url") . "/loan.contract.repayment.apply.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", "合同id", 1, true);
        $this->parameters[]= new apiParameter("type", "还款类型，计划还款 schema 提前还款（全部余额）balance", 'schema', true);
        $this->parameters[]= new apiParameter("amount", "还款金额", 500, true);
        $this->parameters[]= new apiParameter("currency", "还款货币", 'USD', true);
        $this->parameters[]= new apiParameter("repayment_way", "还款的方式，0 线下银行转账 1 绑定的账户自动扣款", 0, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->parameters[]= new apiParameter("name", "线下银行转账方式参数，个人账户名字", 'test');
        $this->parameters[]= new apiParameter("account", "线下银行转账方式参数，个人账户账号", '3658745512');
        $this->parameters[]= new apiParameter("country_code", "线下银行转账方式参数，国家码", '855');
        $this->parameters[]= new apiParameter("phone", "线下银行转账方式参数，电话号码", '85625415');
        $this->parameters[]= new apiParameter("company_account_id", "线下银行转账方式参数，公司账户id", 1);


        $this->parameters[]= new apiParameter("handler_id", "绑定账户自动扣款方式参数，绑定的账户id", 1);


        $this->parameters[]= new apiParameter("receipt_image", "转账回执，文件流", '');
        $this->parameters[]= new apiParameter("remark", "备注", '');


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => ''
        );

    }
}