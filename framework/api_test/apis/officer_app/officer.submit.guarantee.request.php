<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/8
 * Time: 17:24
 */
// officer.submit.guarantee.request
class officerSubmitGuaranteeRequestApiDocument extends  apiDocument
{
    public function __construct()
    {
        $this->name = "Member Add guarantee";
        $this->description = "客户添加担保人申请";
        $this->url = C("bank_api_url") . "/officer.submit.guarantee.request.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "申请的会员id", 1, true);
        $this->parameters[]= new apiParameter("country_code", "国家码", '855', true);
        $this->parameters[]= new apiParameter("phone", "电话", '325481', true);
        $this->parameters[]= new apiParameter("guarantee_member_account", "担保人会员账号", 'test', true);
        $this->parameters[]= new apiParameter("relation_type", "关系类型", 'son', true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array()
        );

    }
}