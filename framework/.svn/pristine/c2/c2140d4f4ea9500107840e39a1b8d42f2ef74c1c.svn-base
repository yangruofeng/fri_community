<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 10:48
 */
class memberSavingsBillListApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member bill list";
        $this->description = "客户账单";
        $this->url = C("bank_api_url") . "/member.savings.bill.list.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员ID", 1, true);
        $this->parameters[]= new apiParameter("page_num", "页数", 1, true);
        $this->parameters[]= new apiParameter("page_size", "每页条数", 20, true);
        $this->parameters[]= new apiParameter("currency", "币种", '');
        $this->parameters[]= new apiParameter("min_amount", "最低金额", 500);
        $this->parameters[]= new apiParameter("max_amount", "最高金额", 5000);
        $this->parameters[]= new apiParameter("start_date", "开始日期", '2018-02-01');
        $this->parameters[]= new apiParameter("end_date", "结束日期", '2018-04-01');

        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '2018-03' => array(
                    'month' => '',
                    'summary' => array(
                        'credit' => '合计收入',
                        'debit' => '合计支出'
                    ),
                    'list' => array(
                        array(
                            'uid' => '流水ID',
                            'category' => '分类',
                            'trading_type' => '交易类型',
                            'subject' => '标题',
                            'credit' => '收入',
                            'debit' => '支出'
                        )
                    )
                ),
                '2018-02'
            )
        );

    }
}