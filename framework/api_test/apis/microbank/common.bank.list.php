<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/6
 * Time: 13:17
 */
// common.bank.list
class commonBankListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Common bank list";
        $this->description = "银行类型列表";
        $this->url = C("bank_api_url") . "/common.bank.list.php";

        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'list' => array(
                    array(
                        'uid' => '银行ID',
                        'bank_code' => '银行编码',
                        'currency' => '银行支持的货币类型',
                        'bank_name' => '银行的名称',
                        'bank_address' => '地址',
                        'enable_digit' => '',
                        'digit_length' => ''
                    )
                )
            )
        );

    }
}