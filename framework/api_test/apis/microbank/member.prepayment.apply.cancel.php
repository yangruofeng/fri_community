<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/9
 * Time: 13:08
 */
// member.prepayment.apply.cancel
class memberPrepaymentApplyCancelApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Prepayment apply cancel";
        $this->description = "提前还款申请取消";
        $this->url = C("bank_api_url") . "/member.prepayment.apply.cancel.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("apply_id", "申请ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => null
        );

    }
}