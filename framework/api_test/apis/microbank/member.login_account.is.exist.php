<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/27
 * Time: 14:12
 */

class memberLogin_accountIsExistApiDocument extends apiDocument
{


    public function __construct()
    {
        $this->name = "Member Account Is Exist";
        $this->description = "会员登陆账号是否存在";
        $this->url = C("bank_api_url") . "/member.login_account.is.exist.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("login_code", "登陆账号", 'test', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'is_exist' => '0 不存在  1 已经存在'
            )
        );

    }


}