<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/12
 * Time: 11:19
 */
// system.company.receive.account
class systemCompanyReceiveAccountApiDocument extends apiDocument
{
    function __construct()
    {
        $this->name = "Company Global Receive Bank Account";
        $this->description = "公司对外收钱银行账户";
        $this->url = C("bank_api_url") . "/system.company.receive.account.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("currency", "币种", null, false);

        // currency


        $str = '{"STS":true,"MSG":"success","DATA":[{"uid":"1","bank_code":"wing","currency":"USD","bank_name":"wing","bank_account_no":"23658745","bank_account_name":"test","bank_address":"","bank_account_phone":"+8558654874"},{"uid":"2","bank_code":"wing","currency":"KHR","bank_name":"wing","bank_account_no":"3698574511","bank_account_name":"test2","bank_address":"","bank_account_phone":"+8551254874"}],"CODE":200,"logger":[]}';
        $re = @json_decode($str,true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'uid' => '',
                    'bank_code' => '银行编码',
                    'currency' => '账户币种',
                    'bank_name' => '银行名称',
                    'bank_account_no' => '账户账号',
                    'bank_account_name' => '账户名字',
                    'bank_address' => '账户银行所在地址',
                    'bank_account_phone' => '账户联系电话',
                )
            )
        );
    }
}