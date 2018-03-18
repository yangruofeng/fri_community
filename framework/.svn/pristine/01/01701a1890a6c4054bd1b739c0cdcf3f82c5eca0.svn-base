<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 16:34
 */
// officer.search.member
class officerSearchMemberApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Officer search member";
        $this->description = "搜索会员";
        $this->url = C("bank_api_url") . "/officer.search.member.php";


        $this->parameters = array();
        $this->parameters[]= new apiParameter("type", "类型, 1 GUID  2 电话号码", 1, true);
        $this->parameters[]= new apiParameter("guid", "会员GUID,type为1传", '10000001');
        $this->parameters[]= new apiParameter("country_code", "国家码,type为2传", '86');
        $this->parameters[]= new apiParameter("phone_number", "电话号码,type为2传", '18902461905');


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '用户信息'
            )
        );

    }
}