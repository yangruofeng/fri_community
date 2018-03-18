<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 17:27
 */
class memberGetBizAccountHandlerApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member biz handler";
        $this->description = "会员存取款账户";
        $this->url = C("bank_api_url") . "/member.get.biz.account.handler.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'handler_list' => array(
                    '@description' => '没有为null',
                    array(
                        'uid' => 'Handler Id',
                        'handler_name' => '账户名称',
                        'handler_account' => '账户账号',
                        'handler_phone' => '电话',
                        'bank_name' => '银行名称',
                        'bank_code' => '银行编码',
                    )
                )

            )
        );

    }
}