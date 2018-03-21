<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/7
 * Time: 14:05
 */
// member.repayment.request.cancel
class memberRepaymentRequestCancelApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Repayment Apply Cancel";
        $this->description = "贷款还款申请取消";
        $this->url = C("bank_api_url") . "/member.repayment.request.cancel.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("request_id", "请求ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}