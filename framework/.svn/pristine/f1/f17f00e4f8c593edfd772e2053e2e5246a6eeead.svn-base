<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/11
 * Time: 17:38
 */
// member.forgot.gesture
class memberForgotGestureApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member forgot Gesture";
        $this->description = "忘记手势密码";
        $this->url = C("bank_api_url") . "/member.forgot.gesture.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("token", "登陆令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'gesture_password' => null
            )
        );

    }
}