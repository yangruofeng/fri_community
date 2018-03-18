<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 11:27
 */
class memberSavingsBillDetailApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member bill detail";
        $this->description = "客户账单详情";
        $this->url = C("bank_api_url") . "/member.savings.bill.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("bill_id", "账单ID", 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'bill_detail' => array(
                    'uid' => '流水ID',
                    'category' => '分类',
                    'trading_type' => '交易类型',
                    'subject' => '标题',
                    'credit' => '收入',
                    'debit' => '支出',
                    'create_time' => '创建时间',
                    'state' => '状态 0 新建 90 在途 100 成功 '
                ),
                'account_info' => array(
                    'uid' => '',
                    'currency' => '',
                    'balance' => '',
                    'credit' => '',
                    'outstanding' => '',
                )
            )
        );

    }
}