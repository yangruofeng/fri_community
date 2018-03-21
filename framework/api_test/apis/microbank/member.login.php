<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/8
 * Time: 11:40
 */
class memberLoginApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Password Login";
        $this->description = "member密码登录";
        $this->url = C("bank_api_url") . "/member.login.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("login_type", "登陆类型 1 账号 2 电话 3 邮箱", 1, true);
        $this->parameters[]= new apiParameter("login_code", "通行账号，login_type为账号传", 'test');
        $this->parameters[]= new apiParameter("country_code", "国家码，login_type为电话传", '86');
        $this->parameters[]= new apiParameter("phone", "电话号码，login_type为电话传", '13845612485');
        $this->parameters[]= new apiParameter("email", "邮箱，login_type为邮箱传", 'test@test.com');
        $this->parameters[]= new apiParameter("login_password", "密码", '123456',true);
        $this->parameters[]= new apiParameter("client_id", "客户端id", 0);
        $this->parameters[]= new apiParameter("client_type", "客户端类型", 'android');

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => 'API返回数据',
                'token' => '89715b07f58028ad30430060ac373292',
                'member_info' => array(
                    'uid' => 'Member ID',
                    'obj_guid' => '全局编号',
                    'login_code' => '登录账号',
                    'family_name' => '姓',
                    'given_name' => '名',
                    'initials' => '首字母',
                    'display_name' => '全名',
                    'alias_name' => 'json多语言名字',
                    'is_staff' => '是否内部员工',
                    'gender' => '性别',
                    'civil_status' => '婚姻状况',
                    'birthday' => '生日',
                    'phone_contry' => '电话国际区号',
                    'phone_id' => '格式化联系电话，如+85562548715',
                    'is_verify_phone' => '是否验证电话',
                    'verify_phone_time' => '电话验证时间',
                    'email' => '邮箱',
                    'is_verify_email' => '是否验证邮件',
                    'verify_email_time' => '邮件验证时间',
                    'member_property' => 'json保存扩展属性，如职业收入等',
                    'member_verification' => 'json字符串，计算项',
                    'member_grade' => '会员等级',
                    'member_officer' => '会员的officer',
                    'member_image' => '头像原图',
                    'member_icon' => '头像剪切图',
                    'grade_code' => '等级名称',
                    'grade_caption' => '等级描述',
                    'open_source' => '开户来源',
                    'open_org' => '开户机构',
                    'open_addr' => '开户地址',
                    'member_state' => '会员状态',
                    'create_time' => '创建时间',
                    'creator_id' => '创建人ID',
                    'creator_name' => '创建人名字',
                    'update_time' => '更新时间',
                    'last_login_time' => '最后登录时间',
                    'last_login_ip' => '最后登录ip',
                ),

            )
        );

    }
}