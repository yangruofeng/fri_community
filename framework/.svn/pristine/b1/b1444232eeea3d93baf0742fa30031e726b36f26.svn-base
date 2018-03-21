<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/23
 * Time: 11:19
 */
class memberEditRegisterInfoApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Member Edit Register Info";
        $this->description = "会员修改注册信息";
        $this->url = C("bank_api_url") . "/member.edit.register.info.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("login_code", "登录账号", 'test', true);
        $this->parameters[]= new apiParameter("family_name", "姓", 'zhang', true);
        $this->parameters[]= new apiParameter("given_name", "名", 'san', true);
        $this->parameters[]= new apiParameter("gender", "性别", 'male',true);
        $this->parameters[]= new apiParameter("birthday", "生日", '1900-08-01',true);


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