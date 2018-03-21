<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 15:19
 */
class memberChangePasswordApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Change Pwd";
        $this->description = "会员修改密码";
        $this->url = C("bank_api_url") . "/member.change.pwd.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", '会员id', 1, true);
         $this->parameters[]= new apiParameter("old_pwd", "旧密码", '', true);
         $this->parameters[]= new apiParameter("new_pwd", "新密码", '', true);
         //$this->parameters[]= new apiParameter("new_pwd_confirm", "新密码确认", '', true);
         $this->parameters[]= new apiParameter("token", "token令牌", '', true);



        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(

            )
        );

    }
}