<?php

class memberGetApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "Get Member";
        $this->description = "获取Member信息";
        $this->url = C("entry_api_url") . "/member.get.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("game_id", "调用方游戏ID", '6', true);
        $this->parameters[]= new apiParameter("member_id", "member ID", '1', true);
        $this->parameters[]= new apiParameter("access_token", "授权令牌", "972c8210965c2c1bb0f946feb375ad8f", true);
        $this->parameters[]= new apiParameter("sign", "参数签名，计算方式：md5(所有参数按字母顺序排列的\$k=\$v使用&连接后字符串 . \$sign_key)", "", true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => 'API返回数据',
                'member_id' => 'Member ID，和传入参数一样',
                'member_name' => 'Member Name',
                'currency' => 'Member所使用的货币',
                'exchange_rate' => 'Member货币的汇率',
                'bet_limit_min' => '最小下注限制',
                'bet_limit_max' => '最大下注限制'
            )
        );
    }
}