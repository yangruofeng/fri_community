<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 11:43
 */
class officerGetTaskSummaryApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Officer task summary";
        $this->description = "员工任务统计";
        $this->url = C("bank_api_url") . "/officer.get.task.summary.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter("officer_id", "id", 1, true);
        $this->parameters[] = new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'task_loan_apply' => '待处理贷款申请'
            )
        );
    }
}