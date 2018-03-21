<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/6
 * Time: 15:26
 */
// member.get.bind.bank.list
class memberGetBindBankListApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Member bind bank list";
        $this->description = "会员绑定银行卡";
        $this->url = C("bank_api_url") . "/member.get.bind.bank.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '没有为null',
                array(
                    'uid' => 'Handler Id',
                    'handler_name' => '账户名称',
                    'handler_account' => '账户账号',
                    'handler_phone' => '电话',
                    'bank_logo' => '银行logo图片',
                    'bank_name' => '银行名称',
                    'bank_currency' => '银行支持币种',
                    'bank_code' => '银行编码',
                    'bank_detail_info' => '银行详细信息',
                )
            )
        );

    }
}