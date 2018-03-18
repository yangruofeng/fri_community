<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/22
 * Time: 15:41
 */
// member.phone.register.new
class memberPhoneRegisterNewApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Phone Register";
        $this->description = "电话注册";
        $this->url = C("bank_api_url") . "/member.phone.register.new.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("country_code", "电话国际区号", '855', true);
        $this->parameters[]= new apiParameter("phone", "手机号码", '326587451', true);
        $this->parameters[]= new apiParameter("sms_id", "短信id", '1', true);
        $this->parameters[]= new apiParameter("sms_code", "短信验证码", '888888', true);

        $this->parameters[]= new apiParameter("login_code", "登陆账号", 'test', true);
        $this->parameters[]= new apiParameter("password", "密码", '123456', true);
        $this->parameters[]= new apiParameter("civil_status", "婚姻状况", 'married', true);

        $this->parameters[]= new apiParameter("photo", "头像图片，文件流", '', true);

        $this->parameters[]= new apiParameter("is_bind_officer", "是否绑定会员的officer", 0);
        $this->parameters[]= new apiParameter("officer_id", "officer ID", 1);
        $this->parameters[]= new apiParameter("officer_name", "officer 名字", 'officer');




        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => 'API返回数据',
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
            )
        );

    }
}