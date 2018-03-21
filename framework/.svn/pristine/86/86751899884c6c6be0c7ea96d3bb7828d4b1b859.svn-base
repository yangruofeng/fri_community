<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/22
 * Time: 17:19
 */
// system.currency.exchange.rate
class systemCurrencyExchangeRateApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Currency exchange rate";
        $this->description = "汇率列表";
        $this->url = C("bank_api_url") . "/system.currency.exchange.rate.php";

        $this->parameters = array();




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(

            )
        );

    }
}