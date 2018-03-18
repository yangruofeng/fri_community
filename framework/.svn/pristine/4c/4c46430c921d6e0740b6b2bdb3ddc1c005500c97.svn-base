<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/6
 * Time: 16:32
 */
// officer.base.info
class officerBaseInfoApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Officer base info";
        $this->description = "业务员信息";
        $this->url = C("bank_api_url") . "/officer.base.info.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("officer_id", "业务员 id", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'user_info' => array(
                    '@description' => '用户信息',
                ),
            )
        );

    }
}