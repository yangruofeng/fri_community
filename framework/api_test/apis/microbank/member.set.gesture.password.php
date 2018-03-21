<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/10
 * Time: 13:30
 */
class memberSetGesturePasswordApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Set Gesture";
        $this->description = "会员设置手势密码";
        $this->url = C("bank_api_url") . "/member.set.gesture.password.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("gesture_password", "手势密码", '', true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'gesture_password' => '手势密码'
            )
        );

    }
}