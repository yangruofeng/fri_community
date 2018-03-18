<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/17
 * Time: 11:57
 */
// system.address.list
class systemAddressListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "System Address Children List ";
        $this->description = "获取地址子区域";
        $this->url = C("bank_api_url") . "/system.address.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("pid", "父级区域ID", 0, true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                array(
                    'uid' => '',
                    'node_text' => '',
                    'node_text_alias' => array(
                        '只有英文，柬文',
                        'en' => '',
                        'kh' => ''
                    ),
                    'node_level' => '层级',

                )
            )
        );

    }
}