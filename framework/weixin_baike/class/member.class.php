<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 16:00
 */
class memberClass
{
    private $member_info;

    public function __construct($memberInfo)
    {
        $this->member_info = $memberInfo;
    }

    public static function getInstanceByID($memberId) {
        $member_model = new memberModel();
        $member_info = $member_model->getRow($memberId);
        if (!$member_info) throw new Exception("Member not found - ID: $memberId");
        return new memberClass($member_info);
    }

    public static function getInstanceByGUID($memberGUID) {
        $member_model = new memberModel();
        $member_info = $member_model->getRow(array('obj_guid' =>$memberGUID));
        if (!$member_info) throw new Exception("Member not found - GUID: $memberGUID");
        return new memberClass($member_info);
    }

    /** member GUID生成规则  使用core的公共函数
     * @param int $uid
     * @return int
     */
    public static function generateMemberGuid($member_id = 0)
    {
        $uid = intval($member_id);
        $guid = intval(strval(objGuidTypeEnum::CLIENT_MEMBER) . str_pad($uid, 6, '0', STR_PAD_LEFT));
        return $guid;
    }

    /** 检查账号格式
     * @param $account
     * @return bool
     */
    public static function isValidAccount($account)
    {
        // 是否以字母开头
        $re = preg_match("/^[a-z]/i", $account);
        if (!$re) {
            return false;
        }

        // 是否存在空格
        $space = preg_match("/\s+/", $account);
        if ($space) {
            return false;
        }
        // 长度5位及以上
        $len = strlen($account);
        if ($len < 5) {
            return false;
        }
        return true;
    }

    /** 检查密码强度
     * @param $password
     * @return bool
     */
    public static function isValidPassword($password)
    {
        // 是否有空格
        $space = preg_match("/\s/", $password);
        if ($space) {
            return false;
        }
        // 长度6位及以上
        $len = strlen($password);
        if ($len < 6) {
            return false;
        }
        return true;
    }


    /** 检查登陆账号是否存在
     * @param $account
     * @return int
     */
    public static function checkLoginAccountIsExist($account)
    {
        $is = 0;
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'login_code' => $account
        ));
        if ($member) {
            $is = 1;
        }
        return $is;
    }


    /** 是否设置交易密码
     * @param $member_id
     * @return
     */
    public static function isSetTradingPassword($member_id)
    {
        $return = array(
            'is_set' => 0,
            'verify_amount' => 0,
            'currency' => null
        );

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return $return;
        }
        if ($member->trading_password) {
            $return = array(
                'is_set' => 1,
                'verify_amount' => $member->trading_verify_amount ?: 0,
                'currency' => $member->trading_verify_currency ?: 'USD'
            );
        }
        return $return;
    }


    /** 电话注册
     * @param $params
     * @return result
     */
    public static function phoneRegister($params)
    {
        return new result(false, 'Given up use!', null, errorCodesEnum::NOT_SUPPORTED);
        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $password = $params['password'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        if (!$country_code || !$phone || !$password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        // 验证验证码
        $m_verify_code = new phone_verify_codeModel();
        $row = $m_verify_code->getRow(array(
            'uid' => $sms_id,
            'verify_code' => $sms_code
        ));
        if (!$row) {
            return new result(false, 'SMS code error', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        // 检查密码
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        $params['is_verify_phone'] = 1;

        $conn = ormYo::Conn();

        try {

            $conn->startTransaction();
            $rt = memberClass::addMember($params);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $member = $rt->DATA;
            $conn->submitTransaction();

            return new result(true, 'Success', $member);

        } catch (Exception $e) {

            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }

    }


    public static function phoneRegisterNew($params)
    {
        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $login_account = trim($params['login_code']);
        $password = $params['password'];

        if (!$country_code || !$phone || !$password || !$login_account) {
            return new result(false, 'Invalid param', array($country_code, $phone, $password, $login_account), errorCodesEnum::INVALID_PARAM);
        }

        if ($params['open_source'] == 1) {
            $params['member_icon'] = $params['member_image'];
        } else {
            // 头像
            if (empty($_FILES['photo'])) {
                return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
            }

            $default_dir = 'avator';
            $upload = new UploadFile();
            $upload->set('save_path', null);
            $upload->set('default_dir', $default_dir);
            $re = $upload->server2upun('photo');
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;

            $params['member_image'] = $img_path;
            $params['member_icon'] = $img_path;
        }


        // 验证验证码
        $m_verify_code = new phone_verify_codeModel();
        $row = $m_verify_code->getRow(array(
            'uid' => $sms_id,
            'verify_code' => $sms_code,
        ));
        if (!$row) {
            return new result(false, 'SMS code error', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $params['phone_number'] = $params['phone'];
        $params['is_verify_phone'] = 1;

        // 检查account格式
        $valid = self::isValidAccount($login_account);
        if (!$valid) {
            return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
        }

        // 检查密码
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        $conn = ormYo::Conn();

        try {

            $conn->startTransaction();

            if( isset($params['is_bind_officer']) && $params['is_bind_officer'] == 1 ){

                $params['creator_id'] = intval($params['officer_id']);
                $params['creator_name'] = $params['officer_name']?:null;
                $params['open_source'] = memberSourceEnum::CLIENT;

                $rt = memberClass::addMember($params);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
                $member = $rt->DATA;


                $officer_id = intval($params['officer_id']);
                $officer_name = $params['officer_name'];
                $re = self::memberBindOfficer($member->uid,$officer_id,$officer_name);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }

            }else{

                $rt = memberClass::addMember($params);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
                $member = $rt->DATA;
                $officer_id = intval($params['officer_id']);
                $officer_name = $params['officer_name'];
                $re = self::memberBindOfficer($member->uid,$officer_id,$officer_name);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }
            }

            $conn->submitTransaction();

            return new result(true, 'Success', $member);

        } catch (Exception $e) {

            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }

    }


    public static function memberBindOfficer($member_id,$officer_id,$officer_name='')
    {
        $member_id = intval($member_id);
        $officer_id = intval($officer_id);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->member_officer = $officer_id;
        $member->update();
        // 更新历史记录
        $sql = "update member_follow_officer set is_active='0' where member_id='$member_id' and is_active='1' ";
        $up = $m_member->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }
        // 插入新的跟进officer
        $m_officer = new member_follow_officerModel();
        $officer = $m_officer->newRow();
        $officer->member_id = $member_id;
        $officer->officer_id = $officer_id;
        $officer->officer_name = $officer_name;
        $officer->is_active = 1;
        $officer->update_time = Now();
        $insert = $officer->insert();
        if( !$insert->STS ){
            return new result(false,'Db error111',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',$officer);
    }


    /** 获取贷款账户
     * @param $member_id
     */
    public static function getLoanAccountInfoByMemberId($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return false;
        }

        $m_account = new loan_accountModel();
        $account = $m_account->find(array(
            'obj_guid' => $member['obj_guid']
        ));
        return $account ?: false;
    }


    /** 获取保险账户
     * @param $member_id
     * @return bool
     */
    public static function getInsuranceAccountInfoByMemberId($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return false;
        }

        $m_account = new insurance_accountModel();
        $account = $m_account->find(array(
            'obj_guid' => $member['obj_guid']
        ));
        return $account ?: false;
    }


    /** 新增会员
     * @param $params
     * @return result
     */
    public static function addMember($params)
    {

        $m_member = new memberModel();

        // 重复检测 用户、电话、邮箱
        $login_code = $params['login_code'];
        if ($login_code) {

            $member = $m_member->getRow(array(
                'login_code' => $login_code
            ));
            if ($member) {
                return new result(false, 'Member exist', null, errorCodesEnum::USER_EXIST);
            }

            // 检查account格式
            $valid = self::isValidAccount($login_code);
            if (!$valid) {
                return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
            }

        } else {
            // 系统自己生成login_code
            // 97,122 小写
            $login_code = chr(rand(65, 90)) . substr(md5(microtime()), 0, 7) . rand(10, 99);
        }

        $contact_phone = null;
        if ($params['phone']) {
            $format_phone = tools::getFormatPhone($params['country_code'], $params['phone']);
            $contact_phone = $format_phone['contact_phone'];
        }


        if ($contact_phone) {
            $member = $m_member->getRow(array(
                'phone_id' => $contact_phone
            ));
            if ($member) {
                return new result(false, 'Phone used', null, errorCodesEnum::PHONE_USED);
            }
        }

        //  邮箱唯一
        if ($params['email']) {
            $member = $m_member->getRow(array(
                'email' => $params['email']
            ));
            if ($member) {
                return new result(false, 'Email used', null, errorCodesEnum::EMAIL_BEEN_REGISTERED);
            }
        }

        // 密码强度
        $valid = self::isValidPassword($params['password']);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        $now = date('Y-m-d H:i:s');

        $member = $m_member->newRow();
        $member->obj_guid = 0;
        $member->login_code = $login_code;
        $member->login_password = md5(trim($params['password']));
        if ($params['trading_password']) $member->trading_password = md5(trim($params['trading_password']));
        $member->family_name = $params['family_name'];
        $member->given_name = $params['given_name'];
        $member->initials = strtoupper(substr(trim($params['family_name']), 0, 1));  // todo 默认是英语的
        if ($params['family_name'] || $params['given_name']) {
            $member->display_name = $params['family_name'] . ' ' . $params['given_name'];
        }
        $member->alias_name = $params['alias_name'];
        $member->is_staff = intval($params['is_staff']) ?: 0;
        $member->gender = $params['gender'];
        $member->civil_status = $params['civil_status'];
        $member->birthday = $params['birthday'];
        $member->phone_country = $params['country_code'];
        $member->phone_number = $params['phone_number'];
        $member->phone_id = $contact_phone;
        if ($params['is_verify_phone']) {
            $member->is_verify_phone = 1;
            $member->verify_phone_time = $now;
        }
        $member->email = $params['email'];
        if ($params['is_verify_email']) {
            $member->is_verify_email = 1;
            $member->verify_email_time = $now;
        }
        $member->member_property = $params['member_property'];
        $member->member_profile = $params['member_profile'];
        $member->member_grade = $params['member_grade'] ?: null;
        $member->member_officer = intval($params['member_officer'])?: 0;
        $member->member_image = $params['member_image'];
        $member->member_icon = $params['member_icon'];
        $member->open_source = isset($params['open_source']) ? intval($params['open_source']) : memberSourceEnum::ONLINE;
        $member->open_org = $params['open_org'] ?: 0;
        $member->open_addr = $params['open_addr'] ?: null;
        $member->member_state = memberStateEnum::CREATE;
        $member->create_time = date('Y-m-d H:i:s');
        if( $params['creator_id'] ){
            $member->creator_id = intval($params['creator_id']);
            $member->creator_name = $params['creator_name'];
        }else{
            $member->creator_id = 0;
            $member->creator_name = 'System';
        }

        $insert = $member->insert();
        if (!$insert->STS) {
            return new result(false, 'Create member fail', null, errorCodesEnum::DB_ERROR);
        }

        // 1位type+6位id
        $member->obj_guid = generateGuid($member->uid, objGuidTypeEnum::CLIENT_MEMBER);  // self::generateMemberGuid($member->uid)
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Create member GUID fail', null, errorCodesEnum::DB_ERROR);
        }


        // 创建通行令牌 code
        if ($member->login_code) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['login_code'] . "','" . $member['login_password'] . "')";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        if ($member->is_verify_phone) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['phone_id'] . "','" . $member['login_password'] . "') ";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        if ($member->is_verify_email) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['email'] . "','" . $member['login_password'] . "') ";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }


        // 创建贷款账户
        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->newRow();
        $loan_account->obj_guid = $member['obj_guid'];
        $loan_account->account_type = loanAccountTypeEnum::MEMBER;
        $insert = $loan_account->insert();
        if (!$insert->STS) {
            return new result(false, 'Create loan account fail', null, errorCodesEnum::DB_ERROR);
        }

        // 注册初始信用
        $is_allow = global_settingClass::isAllowRegisterToSendCredit();
        if ($is_allow) {
            $common_setting = global_settingClass::getCommonSetting();
            $register_credit = intval($common_setting['credit_register']);

            if ($register_credit > 0) {
                $re = member_creditClass::grantCredit($member->uid, $register_credit);
                if (!$re->STS) {
                    return $re;
                }
                // 添加信用调整日志
                $m_release = new loan_credit_releaseModel();
                $release = $m_release->newRow();
                $release->obj_guid = $member['obj_guid'];
                $release->before_credit = 0;
                $release->current_credit = $loan_account->credit;
                $release->operator_id = 0;
                $release->operate_time = $now;
                $release->update_time = $now;
                $insert = $release->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add credit fail', null, errorCodesEnum::DB_ERROR);
                }
            }
        }


        // 创建保险账户
        $m_insurance_account = new insurance_accountModel();
        $insurance_account = $m_insurance_account->newRow();
        $insurance_account->obj_guid = $member['obj_guid'];
        $insert2 = $insurance_account->insert();
        if (!$insert2->STS) {
            return new result(false, 'Create insurance account fail', null, errorCodesEnum::DB_ERROR);
        }


        $m_handler = new member_account_handlerModel();

        // 默认创建一个cash handler
        $cash_handler = $m_handler->newRow();
        $cash_handler->member_id = $member->uid;
        $cash_handler->handler_type = memberAccountHandlerTypeEnum::CASH;
        $cash_handler->handler_name = $member->display_name ? $member->display_name : $member->login_code;
        $cash_handler->handler_phone = $member->phone_id;
        $cash_handler->is_verified = 1;
        $cash_handler->create_time = $now;
        $in = $cash_handler->insert();
        if (!$in->STS) {
            return new result(false, 'Create insurance handler fail', null, errorCodesEnum::DB_ERROR);
        }

        // 默认创建一个贷款保险handler
        $insurance_handler = $m_handler->newRow();
        $insurance_handler->member_id = $member->uid;
        $insurance_handler->handler_type = memberAccountHandlerTypeEnum::PARTNER_LOAN;
        $insurance_handler->handler_name = $member->display_name ? $member->display_name : $member->login_code;
        $insurance_handler->handler_phone = $member->phone_id;
        $insurance_handler->is_verified = 1;
        $insurance_handler->create_time = $now;
        $in = $insurance_handler->insert();
        if (!$in->STS) {
            return new result(false, 'Create insurance handler fail:' . $in->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // todo 更换新的handler方式

        // 自动创建储蓄账户
        $m_passbook = new passbookModel();
        $passbook = $m_passbook->newRow();
        $passbook->book_type = passbookTypeEnum::DEBT;
        $passbook->obj_type = passbookObjTypeEnum::CLIENT_MEMBER;
        $passbook->obj_guid = $member->obj_guid;
        $passbook->state = passbookStateEnum::ACTIVE;
        $passbook->create_time = Now();
        $passbook->create_org = 0;
        $passbook->operator_id = 0;
        $passbook->operator_name = 'System';
        $insert = $passbook->insert();
        if( !$insert->STS ){
            return new result(false,'Create passbook fail',null,errorCodesEnum::DB_ERROR);
        }

        // 创建货币账户
        $book_id = $passbook->uid;
        $currency_arr = (new currencyEnum())->toArray();
        $sql = "insert into passbook_account(book_id,currency,create_time,operator_id,operator_name) values ";

        $data = array();
        $create_time = Now();
        foreach( $currency_arr as $currency ){
            $str = "('$book_id','$currency','$create_time','0','System')";
            $data[] = $str;
        }
        $sql_str = implode(',',$data);
        $sql .= trim($sql_str,',');
        $insert = $m_passbook->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Create passbook account fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member);
    }


    /** 注册修改会员信息
     * @param $params
     * @return result
     */
    public static function editRegisterMemberInfo($params)
    {
        return new result(false,'close',null,errorCodesEnum::UNIMPLEMENTED);
        $m_member = new memberModel();
        $member_id = intval($params['member_id']);
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }


        $login_code = $params['login_code'];
        if (!$login_code) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        // 检查account格式
        $valid = self::isValidAccount($login_code);
        if (!$valid) {
            return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
        }

        $row = $m_member->getRow(array(
            'login_code' => $login_code
        ));
        if ($row) {
            return new result(false, 'Member exist', null, errorCodesEnum::USER_EXIST);
        }

        $member->login_code = $login_code;
        $member->family_name = $params['family_name'];
        $member->given_name = $params['given_name'];
        $member->initials = strtoupper(substr(trim($params['family_name']), 0, 1));  // todo 默认是英语的
        $member->display_name = $params['family_name'] . ' ' . $params['given_name'];
        $member->gender = $params['gender'];
        $member->birthday = $params['birthday'];
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }

        // 创建login_code passport
        $m_passport = new member_passportModel();
        $passport = $m_passport->newRow();
        $passport->member_id = $member->uid;
        $passport->passport_type = 0;
        $passport->passport_account = $member->login_code;
        $passport->passport_token = $member->login_password;
        $in = $passport->insert();
        if (!$in->STS) {
            return new result(false, 'Create passport fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');

    }


    public static function editMemberLoginCode($member_id, $login_code)
    {
        $member_id = intval($member_id);
        if (!$member_id || !$login_code) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $is_exist = self::checkLoginAccountIsExist($login_code);
        if ($is_exist) {
            return new result(false, 'Account exist', null, errorCodesEnum::USER_EXIST);
        }

        // 检查account格式
        $valid = self::isValidAccount($login_code);
        if (!$valid) {
            return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
        }

        $member->login_code = $login_code;
        $member->update_time = Now();
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 插入passport
        $m_passport = new member_passportModel();
        $passport = $m_passport->newRow();
        $passport->member_id = $member_id;
        $passport->passport_type = 0;
        $passport->passport_account = $member->login_code;
        $passport->passport_token = $member->login_password;
        $insert = $passport->insert();
        if (!$insert->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'login_code' => $member->login_code
        ));
    }


    /** 更新会员密码操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberPassword($member_id, $password)
    {
        $member_id = intval($member_id);
        if ($member_id <= 0 || empty($password)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 密码强度检测
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        // 更新密码
        $member->login_password = md5($password);
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        // 更新新令牌
        $sql = "update member_passport set passport_token='" . md5($password) . "' where member_id='" . $member->uid . "' and passport_type='0' ";
        $up = $m_member->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }

    /** 更新交易密码操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberTradePassword($member_id, $password)
    {
        $member_id = intval($member_id);
        if ($member_id <= 0 || empty($password)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 密码强度检测
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        // 更新密码
        $member->trading_password = md5($password);
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    /** 更新会员电话操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberPhoneNum($member_id, $num)
    {
        $member_id = intval($member_id);
        if ($member_id <= 0 || empty($num)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }


        // 更新手机号
        $member->phone_id = $num;
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    /** 短信重置密码
     * @param $params
     * @return result
     */
    public static function resetPwdBySms($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];
        $new_pwd = $params['password'];

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'phone_id' => $contact_phone
        ));

        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $verify_id = $params['sms_id'];
        $verify_code = $params['sms_code'];

        $m_phone_code = new phone_verify_codeModel();
        $row = $m_phone_code->getRow(array(
            'uid' => $verify_id,
            'verify_code' => $verify_code
        ));
        if (!$row) {
            return new result(false, 'Phone code not right', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $re = self::commonUpdateMemberPassword($member->uid, $new_pwd);
        return $re;

    }


    /** 修改登录密码
     * @param $params
     * @return result
     */
    public static function changePassword($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $member_id = $params['member_id'];
        $old_pwd = $params['old_pwd'];
        $new_pwd = $params['new_pwd'];

        if (!$member_id || !$old_pwd || !$new_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->login_password != md5($old_pwd)) {
            return new result(false, 'The old password is not right', null, errorCodesEnum::PASSWORD_ERROR);
        }


        $re = self::commonUpdateMemberPassword($member_id, $new_pwd);
        return $re;

    }

    /** 修改交易密码
     * @param $params
     * @return result
     */
    public static function changeTradePassword($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $member_id = $params['member_id'];
        $old_pwd = $params['old_pwd'];
        $new_pwd = $params['new_pwd'];

        if (!$member_id || !$old_pwd || !$new_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->trading_password != md5($old_pwd)) {
            return new result(false, 'The old password is not right', null, errorCodesEnum::PASSWORD_ERROR);
        }


        $re = self::commonUpdateMemberPassword($member_id, $new_pwd);
        return $re;

    }



    /** 登陆成功日志记录
     * @param $member
     * @param $login_code
     * @param $client_id
     * @param $client_type
     * @return result
     */
    protected static function loginSuccess($member, $login_code, $client_id, $client_type)
    {
        // 更新登陆信息
        $login_ip = getIp();
        $member->last_login_time = Now();
        $member->last_login_ip = $login_ip;
        $member->update();

        // 创建token令牌
        $m_member_token = new member_tokenModel();
        $token_row = $m_member_token->newRow();
        $token_row->member_id = $member->uid;
        $token_row->login_code = $login_code;
        $token_row->token = md5($login_code . time());
        $token_row->create_time = Now();
        $token_row->login_time = Now();
        $token_row->client_type = $client_type;
        $insert = $token_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Create token fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加登陆日志
        $m_member_login_log = new member_login_logModel();
        $log = $m_member_login_log->newRow();
        $log->member_id = $member->uid;
        $log->client_id = $client_id;
        $log->client_type = $client_type;
        $log->login_time = Now();
        $log->login_ip = $login_ip;
        $log->login_area = '';  // todo ip获取区域？
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Log error', null, errorCodesEnum::DB_ERROR);
        }

        $member_info = $member->toArray();
        unset($member_info['login_password']);
        unset($member_info['trading_password']);

        // 获得会员等级
        $member_info['grade_code'] = '';
        $member_info['grade_caption'] = '';
        $m_member_grade = new member_gradeModel();
        $grade_info = $m_member_grade->getRow(array(
            'grade_code' => $member->member_grade,
        ));
        if ($grade_info) {
            $member_info['grade_code'] = $grade_info->grade_code;
            $member_info['grade_caption'] = $grade_info->grade_caption;
        }
        return new result(true, 'Login success', array(
            'token' => $token_row->token,
            'member_info' => $member_info
        ));
    }


    public static function getMemberBaseInfo($member_id)
    {
        $return = null;
        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return $return;
        }

        $member_info = $member;

        unset($member_info['login_password']);
        unset($member_info['trading_password']);

        // 获得会员等级
        $member_info['grade_code'] = '';
        $member_info['grade_caption'] = '';
        $m_member_grade = new member_gradeModel();
        $grade_info = $m_member_grade->getRow(array(
            'grade_code' => $member->member_grade,
        ));
        if ($grade_info) {
            $member_info['grade_code'] = $grade_info->grade_code;
            $member_info['grade_caption'] = $grade_info->grade_caption;
        }

        // 储蓄账户余额
        $memberObject = new objectMemberClass($member_id);
        $cny_balance = $memberObject->getSavingsAccountBalance();
        $member_info['savings_balance'] = $cny_balance;

        return $member_info;

    }


    /** 密码登陆
     * @param $params
     * @return result
     */
    public static function passwordLogin($params)
    {
        $login_type = $params['login_type'];
        $password = $params['login_password'];
        $token = md5($password);
        $member = null;
        $login_code = '';
        switch ($login_type) {
            case memberLoginTypeEnum::LOGIN_CODE :
                $login_code = $params['login_code'];
                $re = self::verifyMemberPassport($login_code, $token);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            case memberLoginTypeEnum::PHONE :
                $country_code = $params['country_code'];
                $phone = $params['phone'];
                $format_phone = tools::getFormatPhone($country_code, $phone);
                $login_code = $format_phone['contact_phone'];
                $re = self::verifyMemberPassport($login_code, $token);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            case memberLoginTypeEnum::EMAIL :
                $login_code = $params['email'];
                $re = self::verifyMemberPassport($login_code, $token);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            default:
                return new result(false, 'Un support type', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];

        if (!$member) {
            return new result(false, 'Login fail', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        return self::loginSuccess($member, $login_code, $client_id, $client_type);

    }

    /** 手势密码登陆
     * @param $params
     * @return result
     */
    public static function gestureLogin($params)
    {
        $member_id = $params['member_id'];
        $gesture_pwd = $params['gesture_password'];
        if (!$member_id || !$gesture_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        if ($member->gesture_password != $gesture_pwd) {
            return new result(false, 'Pwd error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];

        return self::loginSuccess($member, $member->login_code, $client_id, $client_type);

    }

    public static function fingerprintLogin($params)
    {
        $member_id = $params['member_id'];
        $fingerprint = trim($params['fingerprint']);
        if (!$member_id || !$fingerprint) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // url 解码
        $fingerprint = urldecode($fingerprint);

        if ($member->fingerprint != $fingerprint) {
            return new result(false, 'Fingerprint error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];

        return self::loginSuccess($member, $member->login_code, $client_id, $client_type);

    }

    public static function verifyLoginPassword($member_id, $password)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->login_password != md5($password)) {
            return new result(true, 'success', 0);
        }
        return new result(true, 'success', 1);

    }


    /** 检查通行令牌
     * @param $account
     * @param $token
     * @param int $pass_type 0 密码 1 第三方
     * @return result
     */
    public static function verifyMemberPassport($account, $token, $pass_type = 0)
    {
        $m_member = new memberModel();
        $m_member_passport = new member_passportModel();
        // 取最新，废弃历史的
        $passport = $m_member_passport->orderBy('uid desc')->getRow(array(  // 有可能有不同来源同一个passport_account
            'passport_type' => $pass_type,
            'passport_account' => $account,
            'is_invalid' => 0
        ));
        if (!$passport) {
            return new result(false, 'No passport', null, errorCodesEnum::NO_PASSPORT);
        }

        if ($passport['expire_seconds'] && $passport['expire_seconds'] < time()) {
            return new result(false, 'Passport expired', null, errorCodesEnum::PASSPORT_EXPIRED);
        }

        if ($passport->passport_token != $token) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $member = $m_member->getRow($passport->member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        return new result(true, 'success', $member);

    }


    /** 弃用
     * 检查member的passport通行，第三方自动注册
     * @param $param
     * @param int $pass_type
     * @return result
     */
    public static function checkPassport($param, $pass_type = 0)
    {
        return new result(false);
        $login_code = $param['login_code'];
        $login_pwd = $param['login_password'];

        $m_member = new memberModel();
        $m_member_passport = new member_passportModel();

        /**** 先检查有没有合法通行令牌 令牌错误不退出登陆，再验证密码****/
        // 取最新，废弃历史的
        $passport = $m_member_passport->orderBy('uid desc')->getRow(array(  // 有可能有不同来源同一个passport_account
            'passport_type' => $pass_type,
            'passport_account' => $login_code,
            'is_invalid' => 0
        ));

        if ($passport) {
            // 通行有效
            if ($passport['expire_seconds'] == 0 || $passport['expire_seconds'] >= time()) {

                $member = $m_member->getRow($passport->member_id);
                if ($member) {
                    // 密码登陆需要验证密码令牌，不然登陆过后错误密码也能登陆
                    if ($pass_type == 0) {

                        if ($passport->passport_token == md5($login_pwd)) {
                            return new result(true, '', $member);
                        }

                    } else {
                        return new result(true, '', $member);  // 第三方是先去第三方验证密码，再创建passport
                    }
                }

            }
        }
        /**** 先检查有没有合法通行令牌 ****/


        // 没有有效通行令牌的登陆
        if ($pass_type == 0) {
            // 注册会员的密码登陆
            $sql = "select * from client_member where login_code='$login_code' or phone_id='$login_code' ";
            $info = $m_member->reader->getRow($sql);
            $uid = $info ? $info['uid'] : 0;
            $member = $m_member->getRow(array(
                'uid' => $uid
            ));
            if (!$member) {
                return new result(false);
            }

            if ($member->login_password != md5($login_pwd)) {
                return new result(false);
            }

            $new_pass = $m_member_passport->newRow();
            $new_pass->member_id = $member->uid;
            $new_pass->passport_type = $pass_type;
            $new_pass->passport_account = $login_code;
            $new_pass->passport_token = md5($login_pwd);
            $new_pass->expire_seconds = 0;  // 因为会验证密码，可以设置永久有效
            $new_pass->insert();

            return new result(true, '', $member);

        } else {
            // todo  第三方登陆,电话验证问题？APP已经取消电话认证
            $member = $m_member->getRow(array(
                'login_code' => $login_code,
                'open_source' => memberSourceEnum::THIRD
            ));

            if (!$member) {
                // 自动创建会员
                $rt = self::addMember(array(
                    'login_code' => $login_code,
                    'login_password' => '',
                    'open_source' => memberSourceEnum::THIRD
                ));
                if (!$rt->STS) {
                    return new result(false);
                }
                $member = $rt->DATA;
            }

            $new_pass = $m_member_passport->newRow();
            $new_pass->member_id = $member->uid;
            $new_pass->passport_type = $pass_type;
            $new_pass->passport_account = $login_code;
            $new_pass->passport_token = md5($login_code . time());
            $new_pass->expire_seconds = 0;  // 三方登陆的，令牌永久保存
            $new_pass->insert();

            return new result(true, '', $member);

        }


    }


    /** 身份证认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function idVerifyCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $en_name = $params['name_en'];
        $kh_name = $params['name_kh'];
        $cert_sn = $params['cert_sn'];
        if (!$cert_sn || !$en_name || !$kh_name) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $name_json = json_encode(array(
            'en' => $en_name,
            'kh' => $kh_name,
            'zh_cn' => ''
        ));

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        // 检查是否被他人认证过
        $sql = "select * from member_verify_cert where member_id!='$member_id' and cert_type='" . certificationTypeEnum::ID . "'
        and cert_sn='$cert_sn'  order by uid desc";
        $other = $m_member->reader->getRow($sql);
        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }

        $files = $_FILES;
        $hand_photo = $files['hand_photo'];
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($hand_photo) || empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'hand_photo' => '',
            'front_photo' => '',
            'back_photo' => ''
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . fileDirsEnum::ID;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::ID_HANDHELD => array(
                'image_url' => $photos['hand_photo'],
                'image_sha' => sha1_file($photos['hand_photo'])
            ),
            certImageKeyEnum::ID_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file($photos['front_photo'])
            ),
            certImageKeyEnum::ID_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file($photos['back_photo'])
            ),
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::ID
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $o_cert_row->cert_name = $en_name;
            $o_cert_row->cert_name_json = $name_json;
            $o_cert_row->cert_sn = $cert_sn;
            $up = $o_cert_row->update();
            if (!$up->STS) {
                return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
            }


            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));

            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::ID . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            // add
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::ID;
            $new_row->cert_name = $en_name;
            $new_row->cert_name_json = $name_json;
            $new_row->cert_sn = $cert_sn;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }

    /** 户口本认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function familyBookVerifyCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $householder_photo = $files['householder_photo'];
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($householder_photo) || empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'front_photo' => '',
            'back_photo' => '',
            'householder_photo' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . fileDirsEnum::FAMILY_BOOK;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::FAMILY_BOOK_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file($photos['front_photo'])
            ),
            certImageKeyEnum::FAMILY_BOOK_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file($photos['back_photo'])
            ),
            certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD => array(
                'image_url' => $photos['householder_photo'],
                'image_sha' => sha1_file($photos['householder_photo'])
            ),
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::FAIMILYBOOK
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit

            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));

            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::FAIMILYBOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::FAIMILYBOOK;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    /** 居住证认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function residentBookCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::RESIDENT_BOOK;
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'front_photo' => '',
            'back_photo' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . fileDirsEnum::RESIDENT_BOOK;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::RESIDENT_BOOK_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file($photos['front_photo'])
            ),
            certImageKeyEnum::RESIDENT_BOOK_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file($photos['back_photo'])
            )
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    /** 会员家庭关系证明
     * @param $params
     * @param int $source
     * @return result
     */
    public static function familyRelationshipCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $relation_type = $params['relation_type'];
        $relation_name = $params['relation_name'];
        $relation_cert_type = $params['relation_cert_type'];
        $country_code = $params['country_code'];
        $relation_phone = $params['relation_phone']; // relation_phone

        if (empty($_FILES['relation_cert_photo'])) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $phone_arr = tools::getFormatPhone($country_code, $relation_phone);
        $contact_phone = $phone_arr['contact_phone'];

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . fileDirsEnum::FAMILY_RELATION;
        $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
        $upload->set('save_path', null);
        $upload->set('default_dir', $save_path);
        $re = $upload->server2upun('relation_cert_photo');
        if ($re == false) {
            return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
        }
        $img_path = $upload->full_path;
        unset($upload);

        $image_arr = array(
            certImageKeyEnum::FAMILY_RELATION_CERT_PHOTO => array(
                'image_url' => $img_path,
                'image_sha' => sha1_file($img_path)
            )
        );


        $m_cert = new member_verify_certModel();
        $m_family = new member_familyModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->verify_state == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $family_row = $m_family->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if ($family_row) {
                $family_row->relation_type = $relation_type;
                $family_row->relation_name = $relation_name;
                $family_row->relation_cert_type = $relation_cert_type;
                $family_row->relation_cert_photo = $img_path;
                $family_row->relation_phone = $contact_phone;
                $up = $family_row->update();
                if (!$up->STS) {
                    return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
                }

            } else {
                $family_row = $m_family->newRow();
                $family_row->cert_id = $o_cert_row->uid;
                $family_row->member_id = $member_id;
                $family_row->relation_type = $relation_type;
                $family_row->relation_name = $relation_name;
                $family_row->relation_cert_type = $relation_cert_type;
                $family_row->relation_cert_photo = $img_path;
                $family_row->relation_phone = $contact_phone;
                $family_row->create_time = Now();
                $in = $family_row->insert();
                if (!$in->STS) {
                    return new result(false, 'Add family member fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $family_row
            ));


        } else {

            // add
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::FAMILY_RELATIONSHIP;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $row = $m_family->newRow();
            $row->cert_id = $new_row->uid;
            $row->member_id = $member_id;
            $row->relation_type = $relation_type;
            $row->relation_name = $relation_name;
            $row->relation_cert_type = $relation_cert_type;
            $row->relation_cert_photo = $img_path;
            $row->relation_phone = $contact_phone;
            $row->create_time = Now();
            $in = $row->insert();
            if (!$in->STS) {
                return new result(false, 'Add family member fail', null, errorCodesEnum::DB_ERROR);
            }
            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $row
            ));

        }


    }


    /** 会员工作认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function workCert($params, $source = 0)
    {
        $member_id = $params['member_id'];
        $company_name = $params['company_name'];
        $company_addr = $params['company_address'];
        $position = $params['position'];
        $is_government = $params['is_government'];


        $front_photo = $_FILES['work_card'];
        $back_photo = $_FILES['employment_certification'];
        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // key 是上传表单名
        $photos = array(
            'work_card' => '',
            'employment_certification' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . fileDirsEnum::WORK_CERT;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::WORK_CARD => array(
                'image_url' => $photos['work_card'],
                'image_sha' => sha1_file($photos['work_card'])
            ),
            certImageKeyEnum::WORK_EMPLOYMENT_CERTIFICATION => array(
                'image_url' => $photos['employment_certification'],
                'image_sha' => sha1_file($photos['employment_certification'])
            )
        );


        $m_cert = new member_verify_certModel();
        $m_work = new member_workModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::WORK_CERTIFICATION
        ));

        if ($o_cert_row && $o_cert_row->verify_state == certStateEnum::CREATE) {

            // 编辑
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $work_row = $m_work->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));

            if ($work_row) {
                $work_row->company_name = $company_name;
                $work_row->company_addr = $company_addr;
                $work_row->position = $position;
                $work_row->is_government = intval($is_government);
                $work_row->state = workStateStateEnum::CREATE;
                $up = $work_row->update();
                if (!$up->STS) {
                    return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
                }

            } else {
                $work_row = $m_work->newRow();
                $work_row->cert_id = $o_cert_row->uid;
                $work_row->member_id = $member_id;
                $work_row->company_name = $company_name;
                $work_row->company_addr = $company_addr;
                $work_row->position = $position;
                $work_row->is_government = intval($is_government);
                $work_row->create_time = Now();
                $in = $work_row->insert();
                if (!$in->STS) {
                    return new result(false, 'Add work cert fail', null, errorCodesEnum::DB_ERROR);
                }

            }

            $work_row->photo1 = $photos['work_card'];
            $work_row->photo2 = $photos['employment_certification'];

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $work_row
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::WORK_CERTIFICATION . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            // 当前只能有一条合法的，其余的更新为历史
            $sql = "update member_work set state='" . workStateStateEnum::HISTORY . "' where member_id='" . $member_id . "' and state='" . workStateStateEnum::VALID . "' ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }


            // 新增
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::WORK_CERTIFICATION;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $row = $m_work->newRow();
            $row->cert_id = $new_row->uid;
            $row->member_id = $member_id;
            $row->company_name = $company_name;
            $row->company_addr = $company_addr;
            $row->position = $position;
            $row->is_government = intval($is_government);
            $row->create_time = Now();
            $in = $row->insert();
            if (!$in->STS) {
                return new result(false, 'Add work cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $row->photo1 = $photos['work_card'];
            $row->photo2 = $photos['employment_certification'];

            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => $row
            ));

        }


    }

    /** 资产认证
     * @param $param
     * @return result
     */
    public static function assetCert($params, $source = 0)
    {
        // 可以多条
        $asset_type = $params['type'];
        switch ($asset_type) {
            case 'motorbike':
                return self::motorbikeCert($params, $source);
                break;
            case 'house':
                return self::houseCert($params, $source);
                break;
            case 'car':
                return self::carCert($params, $source);
                break;
            case 'land':
                return self::landCert($params, $source);
                break;
            default:
                return new result(false, 'Unsurpport type', null, errorCodesEnum::NOT_SUPPORTED);
        }

    }


    /** 摩托车认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function motorbikeCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::MOTORBIKE;
        $file_dir = fileDirsEnum::MOTORBIKE;

        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $motorbike_photo = $files['motorbike_photo'];
        $certificate_front = $files['certificate_front'];
        $certificate_back = $files['certificate_back'];


        if (empty($motorbike_photo) || empty($certificate_front) || empty($certificate_back)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'motorbike_photo' => '',
            'certificate_front' => '',
            'certificate_back' => ''
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . $file_dir;

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->full_path;
                $photos[$field] = $img_path;
                unset($upload);
            }

        }

        $image_arr = array(
            certImageKeyEnum::MOTORBIKE_PHOTO => array(
                'image_url' => $photos['motorbike_photo'],
                'image_sha' => sha1_file($photos['motorbike_photo'])
            ),
            certImageKeyEnum::MOTORBIKE_CERT_FRONT => array(
                'image_url' => $photos['certificate_front'],
                'image_sha' => sha1_file($photos['certificate_front'])
            ),
            certImageKeyEnum::MOTORBIKE_CERT_BACK => array(
                'image_url' => $photos['certificate_back'],
                'image_sha' => sha1_file($photos['certificate_back'])
            )
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->cert_type == $cert_type && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            //$sql = "update member_verify_cert set verify_state='".certStateEnum::CREATE."' where uid='".$o_cert_row->uid."' ";
            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {

            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表

            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_type = $cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 汽车认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function carCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::CAR;
        $file_dir = fileDirsEnum::CAR;

        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $car_front = $files['car_front'];
        $car_back = $files['car_back'];
        $certificate_front = $files['certificate_front'];
        $certificate_back = $files['certificate_back'];

        if (empty($car_front) || empty($car_back) || empty($certificate_front) || empty($certificate_back)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'car_front' => '',
            'car_back' => '',
            'certificate_front' => '',
            'certificate_back' => ''
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . $file_dir;

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->full_path;
                $photos[$field] = $img_path;
                unset($upload);
            }
        }

        $image_arr = array(
            certImageKeyEnum::CAR_FRONT => array(
                'image_url' => $photos['car_front'],
                'image_sha' => sha1_file($photos['car_front'])
            ),
            certImageKeyEnum::CAR_BACK => array(
                'image_url' => $photos['car_back'],
                'image_sha' => sha1_file($photos['car_back'])
            ),
            certImageKeyEnum::CAR_CERT_FRONT => array(
                'image_url' => $photos['certificate_front'],
                'image_sha' => sha1_file($photos['certificate_front'])
            ),
            certImageKeyEnum::CAR_CERT_BACK => array(
                'image_url' => $photos['certificate_back'],
                'image_sha' => sha1_file($photos['certificate_back'])
            )
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->cert_type == $cert_type && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_type = $cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 房屋认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function houseCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::HOUSE;
        $file_dir = fileDirsEnum::HOUSE;

        $member_id = intval($params['member_id']);
        $x_coordinate = $params['x_coordinate'];
        $y_coordinate = $params['y_coordinate'];

        if (empty($x_coordinate) || empty($y_coordinate) || !is_numeric($x_coordinate) || !is_numeric($y_coordinate)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $house_property_card = $files['property_card'];
        $house_front = $files['house_front'];
        $house_front_road = $files['house_front_road'];
        $house_side_face = $files['house_side_face'];

        if (empty($house_property_card) || empty($house_front) || empty($house_front_road) || empty($house_side_face)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'property_card' => '',
            'house_front' => '',
            'house_front_road' => '',
            'house_side_face' => '',
            'house_inside' => '',
            'house_relationships_certify' => ''
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . $file_dir;

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->full_path;
                $photos[$field] = $img_path;
                unset($upload);
            }
        }

        $image_arr = array(
            certImageKeyEnum::HOUSE_PROPERTY_CARD => array(
                'image_url' => $photos['property_card'],
                'image_sha' => sha1_file($photos['property_card'])
            ),
            certImageKeyEnum::HOUSE_FRONT => array(
                'image_url' => $photos['house_front'],
                'image_sha' => sha1_file($photos['house_front'])
            ),
            certImageKeyEnum::HOUSE_FRONT_ROAD => array(
                'image_url' => $photos['house_front_road'],
                'image_sha' => sha1_file($photos['house_front_road'])
            ),
            certImageKeyEnum::HOUSE_SIDE_FACE => array(
                'image_url' => $photos['house_side_face'],
                'image_sha' => sha1_file($photos['house_side_face'])
            )

        );
        if ($photos['house_inside']) {
            $image_arr[certImageKeyEnum::HOUSE_INSIDE] = array(
                'image_url' => $photos['house_inside'],
                'image_sha' => sha1_file($photos['house_inside'])
            );
        }

        if ($photos['house_relationships_certify']) {
            $image_arr[certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY] = array(
                'image_url' => $photos['house_relationships_certify'],
                'image_sha' => sha1_file($photos['house_relationships_certify'])
            );
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->cert_type == $cert_type && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $o_cert_row->x_coordinate = $x_coordinate;
            $o_cert_row->y_coordinate = $y_coordinate;

            $up = $o_cert_row->update();
            if (!$up->STS) {
                return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
            }

            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->x_coordinate = $x_coordinate;
            $new_row->y_coordinate = $y_coordinate;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_type = $cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 土地认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function landCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::LAND;
        $file_dir = fileDirsEnum::LAND;

        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $front_photo = $files['property_card'];
        $back_photo = $files['trading_record'];

        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'property_card' => '',
            'trading_record' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::CLIENT . '/' . $member_id . '/' . $file_dir;

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->full_path;
                $photos[$field] = $img_path;
                unset($upload);
            }
        }

        $image_arr = array(
            certImageKeyEnum::LAND_PROPERTY_CARD => array(
                'image_url' => $photos['property_card'],
                'image_sha' => sha1_file($photos['property_card'])
            ),
            certImageKeyEnum::LAND_TRADING_RECORD => array(
                'image_url' => $photos['trading_record'],
                'image_sha' => sha1_file($photos['trading_record'])
            )
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->cert_type == $cert_type && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_type = $cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 会员绑定贷款ACE账户
     * @param $params
     * @return result
     */
    public static function bindLoanAceAccount($params)
    {
        $member_id = $params['member_id'];
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $bind_name = $params['name'];
        $bind_account = trim($params['account']);
        $phone_country = $params['country_code'];
        $phone_number = $params['phone'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        if ( !$phone_country || !$phone_number) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 验证账户是否真实存在
        /*$verify = asiaweiluyClass::verifyAceAccount($bind_account);
        if (!$verify->STS) {
            return $verify;
        }
        $is = $verify->DATA;
        if (!$is) {
            return new result(false, 'Not ace member', null, errorCodesEnum::NOT_ACE_MEMBER);
        }*/

        // 验证验证码
        $re = asiaweiluyClass::bindAccountCheckVerifyCode($sms_id, $sms_code);
        if (!$re->STS) {
            return $re;
        }
        $ok = $re->DATA;
        if ($ok != 1) {
            return new result(false, 'Wrong code', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $phone_arr = tools::getFormatPhone($phone_country, $phone_number);
        $contact_phone = $phone_arr['contact_phone'];


        $m_handler = new member_account_handlerModel();
        // add
        $account_handler = $m_handler->newRow();
        $account_handler->member_id = $member_id;
        $account_handler->handler_type = memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY;
        $account_handler->handler_name = $bind_name;
        $account_handler->handler_account = $bind_account;
        $account_handler->handler_phone = $contact_phone;
        $account_handler->is_verified = 1;
        $account_handler->create_time = Now();
        $insert = $account_handler->insert();
        if (!$insert->STS) {
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $account_handler);


    }


    /**
     * @param $params
     * @return result
     */
    public static function editLoanBindAceAccountInfo($params)
    {
        // todo 按需绑定的银行卡是不可编辑的，只应该新增或解绑
        $member_id = $params['member_id'];
        $bind_name = $params['name'];
        $bind_account = trim($params['account']);
        $phone_country = $params['country_code'];
        $phone_number = $params['phone'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 验证账户是否真实存在
        $verify = asiaweiluyClass::verifyAceAccount($bind_account);
        if (!$verify->STS) {
            return $verify;
        }
        $is = $verify->DATA;
        if (!$is) {
            return new result(false, 'Not ace member', null, errorCodesEnum::NOT_ACE_MEMBER);
        }

        // 验证验证码
        $re = asiaweiluyClass::bindAccountCheckVerifyCode($sms_id, $sms_code);
        if (!$re->STS) {
            return $re;
        }
        $ok = $re->DATA;
        if ($ok != 1) {
            return new result(false, 'Wrong code', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $phone_arr = tools::getFormatPhone($phone_country, $phone_number);
        $contact_phone = $phone_arr['contact_phone'];


        $m_handler = new member_account_handlerModel();
        $handler_id = intval($params['account_handler_id']);
        $account_handler = $m_handler->getRow(array(
            'uid' => $handler_id,
            'member_id' => $member_id
        ));
        if (!$account_handler) {
            return new result(false, 'Invalid param', errorCodesEnum::INVALID_PARAM);
        }

        // 更新原来账户状态
        $old_handler_id = $account_handler->uid;
        $account_handler->state = accountHandlerStateEnum::HISTORY;
        $account_handler->update_time = Now();
        $up = $account_handler->update();
        if (!$up->STS) {
            return new result(false, 'Update history fail', null, errorCodesEnum::DB_ERROR);
        }

        // 插入新的handler
        $account_handler = $m_handler->newRow();
        $account_handler->member_id = $member_id;
        $account_handler->handler_type = memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY;
        $account_handler->handler_name = $bind_name;
        $account_handler->handler_account = $bind_account;
        $account_handler->handler_phone = $contact_phone;
        $account_handler->is_verified = 1;
        $account_handler->create_time = Now();
        $insert = $account_handler->insert();
        if (!$insert->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        $new_handler_id = $account_handler->uid;


        // todo 通用的话，应该让客户自己手动去处理或者修改就是覆盖旧值

        // todo 以后是否有其他业务需要更新，如保险等
        // 贷款的放款和还款更换新的handler
        $sql = "update loan_disbursement_scheme set account_handler_id='$new_handler_id' where account_handler_id='$old_handler_id' 
          and state!='" . schemaStateTypeEnum::COMPLETE . "'";
        $exe = $m_handler->conn->execute($sql);
        if (!$exe->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        $sql = "update loan_installment_scheme set account_handler_id='$new_handler_id' where account_handler_id='$old_handler_id' 
          and state!='" . schemaStateTypeEnum::COMPLETE . "'";
        $exe = $m_handler->conn->execute($sql);
        if (!$exe->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $account_handler);


    }


    /** 获取会员绑定的ACE账户信息
     * @param $params
     * @return result
     */
    public static function getMemberLoanAceAccountInfo($member_id)
    {
        $member_id = intval($member_id);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$loan_account) {
            return new result(false, 'No loan account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }
        $m_handler = new member_account_handlerModel();
        $ace_info = $m_handler->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if ($ace_info) {
            // 屏蔽账号信息
            $ace_info['handler_account'] = maskInfo($ace_info['handler_account']);
            // 屏蔽电话
            $ace_info['handler_phone'] = maskInfo($ace_info['handler_phone']);
        } else {
            $ace_info = null;
        }

        return new result(true, 'success', $ace_info);

    }


    /** 会员是否录入指纹
     * @param $member_id
     * @return int
     */
    public static function isLoggingFingerprint($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => $member_id
        ));
        $fingerprint_cert = 0;
        $m_fingerprint = new common_fingerprint_libraryModel();
        $fingerprint = $m_fingerprint->orderBy('uid desc')->getRow(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_uid' => $member['obj_guid']
        ));
        if( $fingerprint ){
            $fingerprint_cert = 1;
        }
        return $fingerprint_cert;
    }


    /** 会员是否签订授权合同
     * @param $member_id
     * @return int
     */
    public static function isSignAuthorizedContract($member_id)
    {
        // 授权合同
        $authorized_contract = 0;
        $m_authorized_contract = new member_authorized_contractModel();
        $contract = $m_authorized_contract->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id
        ));
        if( $contract ){
            $authorized_contract = 1;
        }
        return $authorized_contract;
    }

    /** 获取会员的信用余额
     * @param $member_id
     * @return
     */
    public static function getCreditBalance($member_id)
    {

        $member_id = intval($member_id);
        $m_credit = new member_creditModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));

        // 检查信用激活情况

        // 指纹
        $fingerprint_cert = self::isLoggingFingerprint($member_id);

        // 授权合同
        $authorized_contract = self::isSignAuthorizedContract($member_id);

        // 是否检查指纹
        $check_fingerprint_cert = global_settingClass::isCheckCreditFingerprintCert();
        // 是否检查授权合同
        $check_authorized_contract = global_settingClass::isCheckCreditAuthorizedContract();

        $is_active = 1;
        if( $check_fingerprint_cert && !$fingerprint_cert ){
            $is_active = 0;
        }
        if( $check_authorized_contract && !$authorized_contract ){
            $is_active = 0;
        }

        $credit_process = array(
            creditProcessEnum::FINGERPRINT => array(
                'is_check' => $check_fingerprint_cert,
                'is_complete' => $fingerprint_cert
            ),
            creditProcessEnum::AUTHORIZED_CONTRACT => array(
                'is_check' => $check_authorized_contract,
                'is_complete' => $authorized_contract
            )
        );


        if (!$member_credit) {

            return array(
                'is_active' => $is_active,
                'credit' => 0,
                'balance' => 0,
                'evaluate_time' => null,
                'expire_time' => null,
                'credit_process' => $credit_process
            );

        }

        // 信用过期了
        if( $member_credit['expire_time'] && ( strtotime($member_credit['expire_time']) <= time() ) ){
            return array(
                'is_active' => 0,
                'credit' => 0,
                'balance' => 0,
                'evaluate_time' => $member_credit->grant_time,
                'expire_time' => $member_credit->expire_time,
                'credit_process' => $credit_process
            );
        }

        return array(
            'is_active' => $is_active,
            'credit' => $member_credit->credit,
            'balance' => $member_credit->credit_balance,
            'evaluate_time' => $member_credit->grant_time,
            'expire_time' => $member_credit->expire_time,
            'credit_process' => $credit_process
        );

    }

    /** 会员的贷款余额 返回USD的基础单位
     * @param $member_id
     * @return result
     */
    public static function getLoanBalance($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";
        $result = $m_member->reader->getRows($sql);
        $total_debt = 0;
        if (count($result) > 0) {
            // 计算方式-> 执行中的未还款金额
            foreach ($result as $v) {
                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    // 不同币种的换算问题
                    $source = ($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total_debt += round($v['receivable_principal'] * $rate, 2);  // 只计算本金
                }
            }
        }
        return new result(true, 'success', $total_debt);
    }


    /** 获得贷款总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotal($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        //$m_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select * from loan_contract where account_id='$account_id' and state >='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {
                $source = ($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $total += round($v['receivable_principal'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }


    /** 获得贷款应还总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotalRepayable($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;

        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";

        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {

                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    $penalty = 0;
                    if ($v['penalty_start_date'] < date('Y-m-d')) {
                        $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                    }

                    $total_amount = $v['amount'] - $v['actual_payment_amount'] + $penalty;
                    $source = strtoupper($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total += round($total_amount * $rate, 2);
                }

            }

        }
        return new result(true, 'success', $total);
    }


    /** 获取会员保险总额
     * @param $member_id
     * @return result
     */
    public static function getMemberInsuranceTotal($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new insurance_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        // 代收款保险的都算
        $sql = "select * from insurance_contract where account_id='$account_id' and state>='" . insuranceContractStateEnum::PENDING_RECEIPT . "' ";
        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {
                $source = strtoupper($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $total += round($v['price'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }


    public static function getMemberWriteOffContractTotal($member_id)
    {
        $account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $account ? $account['uid'] : 0;
        $r = new ormReader();
        $sql = "select * from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::WRITE_OFF . "' ";
        $total = 0;
        $lists = $r->getRows($sql);
        foreach ($lists as $v) {
            $principal = $v['receivable_principal'];
            $source = $v['currency'];
            $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
            if ($rate <= 0) {
                return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $total += round($principal * $rate, 2);
        }
        return new result(true, 'success', $total);
    }

    public static function getMemberOutstandingWriteOffContractTotal($member_id)
    {
        $account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $account ? $account['uid'] : 0;
        $r = new ormReader();
        $sql = "select * from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::WRITE_OFF . "' ";
        $total = 0;
        $lists = $r->getRows($sql);
        foreach ($lists as $v) {
            $outstanding = $v['loss_principal'];
            $source = $v['currency'];
            $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
            if ($rate <= 0) {
                return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $total += round($outstanding * $rate, 2);
        }
        return new result(true, 'success', $total);
    }


    /** 获取会员认证的简单结果(yes or no)
     * @param $member_id
     * @return array|result
     */
    public static function getMemberSimpleCertResult($member_id)
    {
        $re = self::getMemberCertStateOrCount($member_id);
        if( !$re->STS ){
            return $re;
        }
        $list = $re->DATA;
        // 只有一条的
        $one_type = array(
            certificationTypeEnum::ID,
            certificationTypeEnum::PASSPORT,
            certificationTypeEnum::FAIMILYBOOK,
            certificationTypeEnum::WORK_CERTIFICATION,
            certificationTypeEnum::RESIDENT_BOOK
        );
        $result = array();
        foreach( $list as $key=>$value){

            if( in_array($key,$one_type) ){

                if( $value == certStateEnum::PASS ){
                    $is = 1;
                }else{
                    $is = 0;
                }
                $result[$key] = $is;
            }else{
                $result[$key] = ($value>0)?1:0;
            }
        }

        return new result(true,'success',$result);
    }


    /** 获取member各项认证的状态或数量
     * @param $member_id
     * @return array|result
     */
    public static function getMemberCertStateOrCount($member_id)
    {
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $type = array();
        $cert = (new certificationTypeEnum())->toArray();
        foreach ($cert as $const => $v) {
            $type[$v] = -10;  // 没有认证过
        }

        // 只有一条的
        $one_type = array(
            certificationTypeEnum::ID,
            certificationTypeEnum::PASSPORT,
            certificationTypeEnum::FAIMILYBOOK,
            certificationTypeEnum::WORK_CERTIFICATION,
            certificationTypeEnum::RESIDENT_BOOK
        );
        // 取最新认证的记录作为认证结果
        $sql = "select * from ( select * from member_verify_cert where member_id='$member_id' order by uid desc ) x group by member_id,cert_type ";
        $results = $m_member->reader->getRows($sql);
        if (count($results) > 0) {
            foreach ($results as $item) {
                $cert_type = $item['cert_type'];
                if (in_array($cert_type, $one_type)) {
                    $type[$cert_type] = $item['verify_state'] ?: 0;
                }
            }
        }

        // 非一条的
        foreach ($type as $k => $v) {
            if (!in_array($k, $one_type)) {
                $sql = "select count(*) from member_verify_cert where member_id='$member_id' and cert_type='$k' and verify_state='" . certStateEnum::PASS . "' ";
                $count = $m_member->reader->getOne($sql);
                $type[$k] = $count ?: 0;
            }
        }

        // 担保人
        $sql = "select count(*) from member_guarantee where member_id='$member_id' and relation_state='" . memberGuaranteeStateEnum::ACCEPT . "' ";
        $num = $m_member->reader->getOne($sql);
        $type[certificationTypeEnum::GUARANTEE_RELATIONSHIP] = $num;


        return new result(true, 'success', $type);

    }

    /** 获取会员所有的认证通过情况(API弃用)
     * @param $member_id
     * @return result
     */
    public static function getAllCertDetail($member_id)
    {
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $type = array();
        $cert = (new certificationTypeEnum())->toArray();
        foreach ($cert as $const => $v) {
            $type[$v] = null;  // 没有认证过
        }

        $m_cert = new member_verify_certModel();
        // 取最新认证的记录作为认证结果
        $sql = "select * from ( select * from member_verify_cert where member_id='$member_id' order by uid desc ) x group by member_id,cert_type ";
        $results = $m_member->reader->getRows($sql);

        if (count($results) > 0) {
            foreach ($results as $item) {
                $type[$item['cert_type']] = $item;
            }
        }

        // family 是多条，有认证通过就表示认证了，后面不覆盖前面
        $family_cert = $m_cert->find(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::FAMILY_RELATIONSHIP,
            'verify_state' => certStateEnum::PASS
        ));
        if ($family_cert) {
            $type[$family_cert['cert_type']] = $family_cert;
        }



        return new result(true, 'success', $type);

    }


    /** 获取贷款合同列表
     * @param array $params
     * @return result
     */
    public static function getLoanContractList($params = array())
    {

        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$loan_account) {
            return new result(false, 'No account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }
        $account_id = $loan_account->uid;

        if (isset($params['loan_type'])) {
            $loan_type = intval($params['loan_type']);
        } else {
            $loan_type = 0;
        }

        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;
        $type = $params['type'];

        if ($loan_type == 0) {

            switch ($type) {
                case 1: // all
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::CREATE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 2: // 执行中的( 待放款+进行中)
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state in('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "') ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 3: // 待审核的
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state='" . loanContractStateEnum::CREATE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 4:  // 有逾期的
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id left join loan_installment_scheme s on s.contract_id=c.uid where c.account_id='$account_id' and c.state in('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "')
                 and s.state !='" . schemaStateTypeEnum::CANCEL . "' and s.state!='".schemaStateTypeEnum::COMPLETE."' and date_format(s.receivable_date,'%Y%m%d') < '" . date('Ymd') . "' group by c.uid ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 5:
                    // 还款完成的
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state='" . loanContractStateEnum::COMPLETE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 6:
                    //  正常执行无逾期的
                    $sql = "select c.*,p.product_code,p.product_name,s.receivable_date from loan_contract c left join loan_product p on p.uid=c.product_id left join (select * from loan_installment_scheme where  state !='" . schemaStateTypeEnum::CANCEL . "' and state !='" . schemaStateTypeEnum::COMPLETE . "' and date_format(receivable_date,'%Y%m%d') < '" . date('Ymd') . "' ) s on s.contract_id=c.uid where c.account_id='$account_id' and c.state in('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "')
                and s.receivable_date is null  group by c.uid";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 20: // 信用贷合同
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where p.is_credit_loan='1' and  c.account_id='$account_id' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                default:
                    $sql = "select c.*,p.product_code,p.product_name from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::CREATE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
            }

            $count = $list->count;
            $page_count = $list->pageCount;
            $contracts = $list->rows;

        } else {
            // todo 暂时没有
            /*$sql = "select * from loan_contract where 1=0 ";
            $list = $m_member->reader->getPage($sql,$page_num,$page_size);*/

            $count = 0;
            $page_count = 0;
            $contracts = null;
        }


        $reader = new ormReader();
        if (count($contracts) > 0) {
            foreach ($contracts as $k => $v) {
                // 未还款统计信息
                $contract_id = $v['uid'];
                $item = $v;
                $sql = "select count(uid) left_period,sum(receivable_principal) left_principal from loan_installment_scheme where contract_id='$contract_id'  and state !='" . schemaStateTypeEnum::COMPLETE . "' and state !='" . schemaStateTypeEnum::CANCEL . "' ";
                $re = $reader->getRow($sql);
                $item['left_period'] = $re['left_period'];
                $item['left_principal'] = $re['left_principal']?:0;
                $contracts[$k] = $item;

            }
        }

        return new result(true, 'success', array(
            'total_num' => $count,
            'total_pages' => $page_count,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $contracts ?: null
        ));

    }

    /** 获取保险合同列表
     * @param $params
     * @return result
     */
    public static function getInsuranceContractList($params)
    {
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_insurance_account = new insurance_accountModel();
        $insurance_account = $m_insurance_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$insurance_account) {
            return new result(false, 'No account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }
        $account_id = $insurance_account->uid;

        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;

        $type = $params['type'];
        switch ($type) {
            case 1:  // all
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id  where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 2: // processing (待收款+进行中)
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state in('" . insuranceContractStateEnum::PENDING_RECEIPT . "','" . insuranceContractStateEnum::PROCESSING . "') ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 3:  // pending approval
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state in('" . insuranceContractStateEnum::CREATE . "','" . insuranceContractStateEnum::PENDING_APPROVAL . "') ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 4: // expired
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' and c.end_date is not null and date_format(c.end_date,'%Y%m%d') < '" . date('Ymd') . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            default: // all
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
        }
        $count = $list->count;
        $page_count = $list->pageCount;
        $contracts = $list->rows;
        return new result(true, 'success', array(
            'total_num' => $count,
            'total_pages' => $page_count,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $contracts ?: null
        ));
    }


    /** 获得用户的认证结果
     * @param $params
     * @return result
     */
    public static function getMemberCertResult($params)
    {
        $member_id = intval($params['member_id']);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $type = $params['type'];
        $m_cert = new member_verify_certModel();

        $row = null;
        $extend = null;
        switch ($type) {
            case certificationTypeEnum::RESIDENT_BOOK :
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));
                break;
            case certificationTypeEnum::ID:
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));
                break;
            case certificationTypeEnum::FAIMILYBOOK:
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));
                break;
            case certificationTypeEnum::PASSPORT :
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));
                break;
            case certificationTypeEnum::MOTORBIKE :
                // 资产
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));  // 最新一条的
                $sql = "select a.*,c.verify_state,c.verify_remark from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id' 
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
                $extend = $m_cert->reader->getRows($sql);
                $m_image = new member_verify_cert_imageModel();
                foreach ($extend as $k => $v) {
                    $images = $m_image->getRows(array(
                        'cert_id' => $v['cert_id']
                    ));
                    $image_list = array();
                    foreach ($images as $item) {
                        $image_list[$item['image_key']] = $item['image_url'];
                    }
                    $v['cert_images'] = $image_list;
                    $v['main_image'] = $image_list[certImageKeyEnum::MOTORBIKE_PHOTO];
                    $extend[$k] = $v;
                }
                break;
            case certificationTypeEnum::CAR :
                // 资产
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));  // 最新一条的
                $sql = "select a.*,c.verify_state,c.verify_remark from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id' 
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
                $extend = $m_cert->reader->getRows($sql);
                $m_image = new member_verify_cert_imageModel();
                foreach ($extend as $k => $v) {
                    $images = $m_image->getRows(array(
                        'cert_id' => $v['cert_id']
                    ));
                    $image_list = array();
                    foreach ($images as $item) {
                        $image_list[$item['image_key']] = $item['image_url'];
                    }
                    $v['cert_images'] = $image_list;
                    $v['main_image'] = $image_list[certImageKeyEnum::CAR_FRONT];

                    $extend[$k] = $v;
                }
                break;
            case certificationTypeEnum::HOUSE :
                // 资产
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));  // 最新一条的
                $sql = "select a.*,c.verify_state,c.verify_remark from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id' 
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
                $extend = $m_cert->reader->getRows($sql);
                $m_image = new member_verify_cert_imageModel();
                foreach ($extend as $k => $v) {
                    $images = $m_image->getRows(array(
                        'cert_id' => $v['cert_id']
                    ));
                    $image_list = array();
                    foreach ($images as $item) {
                        $image_list[$item['image_key']] = $item['image_url'];
                    }
                    $v['cert_images'] = $image_list;
                    $v['main_image'] = $image_list[certImageKeyEnum::HOUSE_FRONT];

                    $extend[$k] = $v;
                }
                break;
            case certificationTypeEnum::LAND:
                // 资产
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));  // 最新一条的
                $sql = "select a.*,c.verify_state,c.verify_remark from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id' 
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
                $extend = $m_cert->reader->getRows($sql);
                $m_image = new member_verify_cert_imageModel();
                foreach ($extend as $k => $v) {
                    $images = $m_image->getRows(array(
                        'cert_id' => $v['cert_id']
                    ));
                    $image_list = array();
                    foreach ($images as $item) {
                        $image_list[$item['image_key']] = $item['image_url'];
                    }
                    $v['cert_images'] = $image_list;
                    $v['main_image'] = $image_list[certImageKeyEnum::LAND_PROPERTY_CARD];

                    $extend[$k] = $v;
                }
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $row = $m_cert->orderBy('uid desc')->getRow(array(
                    'member_id' => $member_id,
                    'cert_type' => $type
                ));
                $m_work = new member_workModel();
                $extend = $m_work->getRow(array(
                    'cert_id' => $row ? $row->uid : 0
                ));
                break;
            default :
                $row = null;

        }
        return new result(true, 'success', array(
            'cert_result' => $row ?: null,
            'extend_info' => $extend ?: null
        ));
    }


    /** 获取用户账户统计
     * @param $member_id
     * @return mixed|null|result
     */
    public static function getMemberAccountSumInfo($member_id)
    {
        $member_id = intval($member_id);

        $loan_account = self::getLoanAccountInfoByMemberId($member_id);

        $credit = self::getCreditBalance($member_id);
        if (!$credit->STS) {
            return $credit;
        }

        $credit = $credit->DATA;

        $loan_balance = self::getLoanBalance($member_id);
        $loan_balance = $loan_balance->STS ? $loan_balance->DATA : 0;

        $loan_total = self::getLoanTotal($member_id);
        $loan_total = $loan_total->STS ? $loan_total->DATA : 0;

        $loan_total_repayable = self::getLoanTotalRepayable($member_id);
        $loan_total_repayable = $loan_total_repayable->STS ? $loan_total_repayable->DATA : 0;

        $insurance_total = self::getMemberInsuranceTotal($member_id);
        $insurance_total = $insurance_total->STS ? $insurance_total->DATA : 0;

        $reader = new ormReader();
        $account_id = intval($loan_account['uid']);
        $sql = "select count(*) from loan_contract where account_id='$account_id' and state in ('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "') ";
        $processing_loan_contracts = $reader->getOne($sql);
        $processing_loan_contracts = $processing_loan_contracts ?: 0;

        $insurance_account = self::getInsuranceAccountInfoByMemberId($member_id);
        $insurance_account_id = intval($insurance_account['uid']);
        $sql = "select count(*) from insurance_contract where account_id='$insurance_account_id' and state in ('" . insuranceContractStateEnum::PENDING_RECEIPT . "','" . insuranceContractStateEnum::PROCESSING . "') ";
        $processing_insurance_contracts = $reader->getOne($sql);
        $processing_insurance_contracts = $processing_insurance_contracts ?: 0;


        return new result(true, 'success', array(
            'credit' => $credit,
            'loan_total' => $loan_total,
            'loan_balance' => $loan_balance,
            'loan_total_repayable' => $loan_total_repayable,
            'insurance_total' => $insurance_total,
            'processing_loan_contracts' => $processing_loan_contracts,
            'processing_insurance_contracts' => $processing_insurance_contracts
        ));


    }


    public static function getMemberLoanApplyList($params)
    {
        $member_id = intval($params['member_id']);
        $reader = new ormReader();
        $page_num = intval($params['page_num']) ? intval($params['page_num']) : 1;
        $page_size = intval($params['page_size']) ? intval($params['page_size']) : 100000;
        $sql = "select * from loan_apply where member_id='$member_id' order by uid desc ";
        $rows = $reader->getPage($sql, $page_num, $page_size);
        return new result(true, 'success', array(
            'total_num' => $rows->count,
            'total_pages' => $rows->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows->rows
        ));
    }


    public static function getMemberCreditHistory($params)
    {
        $member_id = $params['member_id'];
        $page_num = intval($params['page_num']) ? intval($params['page_num']) : 1;
        $page_size = intval($params['page_size']) ? intval($params['page_size']) : 100000;

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $sql = "select * from loan_credit_release where obj_guid='" . $member->obj_guid . "' order by uid desc ";
        $rows = $m_member->reader->getPage($sql, $page_num, $page_size);

        return new result(true, '', array(
            'total_num' => $rows->count,
            'total_pages' => $rows->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows->rows ? $rows->rows : null
        ));
    }

    public static function getLoanBindAutoDeductionAccount($member_id)
    {

        $r = new ormReader();
        $sql = "select uid,handler_type,handler_name,handler_account,handler_phone,handler_property from member_account_handler where member_id='$member_id' and is_verified=1 and state='" . accountHandlerStateEnum::ACTIVE . "'
        and handler_type in('" . memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY . "','" . memberAccountHandlerTypeEnum::PASSBOOK . "')";
        $rows = $r->getRows($sql);
        $list = array();
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                $v['handler_account'] = maskInfo($v['handler_account']);
                $list[] = $v;
            }
        }

        return new result(true, 'success', $list);


    }

    /** 资产删除
     * @param $member_id
     * @param $asset_id
     * @return result
     */
    public static function deleteAsset($member_id, $asset_id)
    {
        $m_asset = new member_assetsModel();
        $asset = $m_asset->getRow(array(
            'member_id' => $member_id,
            'uid' => $asset_id
        ));
        if (!$asset) {
            return new result(false, 'Invalid asset', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_cert = new member_verify_certModel();
        $cert = $m_cert->getRow($asset->cert_id);

        // 审核中不能删除
        if ($cert && $cert['verify_state'] == certStateEnum::LOCK) {
            return new result(false, 'Delete fail', null, errorCodesEnum::APPROVING_CAN_NOT_DELETE);
        }

        // 软删除
        $asset->asset_state = assetStateEnum::CANCEL;
        $asset->update_time = Now();
        $up = $asset->update();
        if (!$up->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }

        // 更新认证记录的状态
        if ($cert) {
            if ($cert->verify_state != certStateEnum::EXPIRED) {
                $cert->verify_state = certStateEnum::EXPIRED;
                $up = $cert->update();
                if (!$up->STS) {
                    return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }
        return new result(true, 'success');
    }


    /** 移除家庭关系
     * @param $member_id
     * @param $relation_id
     * @return result
     */
    public static function deleteFamilyRelationship($member_id, $relation_id)
    {

        $m_family = new member_familyModel();
        $family = $m_family->getRow(array(
            'uid' => $relation_id,
            'member_id' => $member_id
        ));
        if (!$family) {
            return new result(false, 'Invalid family relationship', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_cert = new member_verify_certModel();
        $cert = $m_cert->getRow($family->cert_id);

        // 审核中不能删除
        if ($cert && $cert['verify_state'] == certStateEnum::LOCK) {
            return new result(false, 'Delete fail', null, errorCodesEnum::APPROVING_CAN_NOT_DELETE);
        }

        // 移除关系
        $family->relation_state = memberFamilyStateEnum::REMOVE;
        $family->update_time = Now();
        $up = $family->update();
        if (!$up->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }
        // 更新认证记录的状态
        if ($cert) {
            if ($cert->verify_state != certStateEnum::EXPIRED) {
                $cert->verify_state = certStateEnum::EXPIRED;
                $up = $cert->update();
                if (!$up->STS) {
                    return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }
        return new result(true, 'success');

    }


    public static function getMemberLoanSummary($member_id, $type = 1)
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        // 返回数据格式
        $return = array(
            'contract_num_summary' => array(
                'total_contracts' => 0,
                'processing_contracts' => 0,
                'normal_processing_contracts' => 0,
                'delinquent_contracts' => 0,
                'complete_contracts' => 0,
                'rejected_contracts' => 0,
                'pending_approval_contracts' => 0,
                'write_off_contracts' => 0
            ),
            'contract_amount_summary' => array(
                'total_principal' => 0,
                'total_liabilities' => 0,
                'total_write_off_amount' => 0,
                'total_outstanding_write_off_balance' => 0
            ),
            'next_schema' => null
        );

        $r = new ormReader();
        switch ($type) {
            case 1:
                // 自己贷款的
                $total_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 0);
                $processing_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 1);
                $delinquent_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 2);

                $pending_approval_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 6);
                $return['contract_num_summary']['total_contracts'] = $total_contracts;
                $return['contract_num_summary']['processing_contracts'] = $processing_contracts;
                $return['contract_num_summary']['delinquent_contracts'] = $delinquent_contracts;
                $return['contract_num_summary']['normal_processing_contracts'] = $processing_contracts - $delinquent_contracts;
                $return['contract_num_summary']['complete_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 5);
                $return['contract_num_summary']['rejected_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 3);
                $return['contract_num_summary']['pending_approval_contracts'] = $pending_approval_contracts;
                $return['contract_num_summary']['write_off_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 4);

                $loan_total = 0;
                $loan_re = self::getLoanTotal($member_id);
                if ($loan_re->STS) {
                    $loan_total = $loan_re->DATA;
                }
                $payable_total = 0;
                $payable_re = self::getLoanTotalRepayable($member_id);
                if ($payable_re->STS) {
                    $payable_total = $payable_re->DATA;
                }

                $write_off_total = 0;
                $off_re1 = self::getMemberWriteOffContractTotal($member_id);
                if ($off_re1->STS) {
                    $write_off_total = $off_re1->DATA;
                }

                $outstanding_write_off_total = 0;
                $off_re2 = self::getMemberOutstandingWriteOffContractTotal($member_id);
                if ($off_re2->STS) {
                    $outstanding_write_off_total = $off_re2->DATA;
                }

                $return['contract_amount_summary']['total_principal'] = $loan_total;
                $return['contract_amount_summary']['total_liabilities'] = $payable_total;
                $return['contract_amount_summary']['total_write_off_amount'] = $write_off_total;
                $return['contract_amount_summary']['total_outstanding_write_off_balance'] = $outstanding_write_off_total;

                // 下期应还
                $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' and s.receivable_date>='" . date('Y-m-d') . "' order by s.receivable_date asc ";
                $schema = $r->getRow($sql);
                if ($schema) {
                    $return['next_schema'] = array(
                        'repayment_time' => date('Y-m-d', strtotime($schema['receivable_date'])),
                        'repayment_amount' => $schema['amount'] - $schema['actual_payment_amount'],
                        'currency' => $schema['currency']
                    );
                }
                break;
            case 2:
                // 担保的贷款
                break;
            default:
                break;
        }

        return new result(true, 'success', $return);
    }


    public static function getMemberLoanNextRepaymentSchema($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='$account_id'  
        and  c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' and s.state!='".schemaStateTypeEnum::CANCEL."' and s.state!='".schemaStateTypeEnum::COMPLETE."' 
        and s.receivable_date>='".date('Y-m-d')."'  order by s.receivable_date asc ";

        $schemas = $r->getRow($sql);
        return $schemas;
    }

    /** 会员信用激活信息
     * @param $member_id
     * @return result
     */
    public static function getMemberCreditProcess($member_id)
    {

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){

            //return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);

            // 是否检查指纹
            $check_fingerprint_cert = global_settingClass::isCheckCreditFingerprintCert();
            // 是否检查授权合同
            $check_authorized_contract = global_settingClass::isCheckCreditAuthorizedContract();

            return new result(true,'success',array(
                'phone' => array(
                    'is_must' => 1,
                    'is_complete' => 0,
                ),
                'personal_info' => array(
                    'is_must' => 1,
                    'is_complete' => 0,
                ),
                'assets_cert' => array(
                    'is_must' => 0,
                    'is_complete' => 0,
                ),
                'fingerprint' => array(
                    'is_must' => $check_fingerprint_cert,
                    'is_complete' => 0,
                ),
                'authorized_contract' => array(
                    'is_must' => $check_authorized_contract,
                    'is_complete' => 0,
                ),
                'credit_info' => array(
                    'credit' => 0,
                    'balance' => 0,
                ),
                'is_active' => 0,
            ));
        }

        $credit = memberClass::getCreditBalance($member_id);

        $cert_list = array();
        $re = self::getMemberCertStateOrCount($member_id);
        if( $re->STS ){
            $cert_list = $re->DATA;
        }


        $return = array();
        $return['phone'] = array(
            'is_must' => 1,
            'is_complete' => $member->is_verify_phone?1:0
        );

        $personal_info = 0;
        if ( $cert_list[certificationTypeEnum::ID] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::PASS
         )
        {
            $personal_info = 1;
        }

        $return['personal_info'] = array(
            'is_must' => 1,
            'is_complete' => $personal_info
        );

        $assets_cert = 0;
        if ( $cert_list[certificationTypeEnum::MOTORBIKE] > 0
            || $cert_list[certificationTypeEnum::CAR] > 0
            || $cert_list[certificationTypeEnum::HOUSE] > 0
            || $cert_list[certificationTypeEnum::LAND] > 0
        )
        {
            $assets_cert = 1;
        }

        $return['assets_cert'] = array(
            'is_must' => 0,
            'is_complete' => $assets_cert
        );

        $fingerprint_cert = $credit['credit_process'][creditProcessEnum::FINGERPRINT];

        $return['fingerprint'] = array(
            'is_must' => $fingerprint_cert['is_check'],
            'is_complete' => $fingerprint_cert['is_complete']
        );

        $authorized_contract = $credit['credit_process'][creditProcessEnum::AUTHORIZED_CONTRACT];
        $return['authorized_contract'] = array(
            'is_must' => $authorized_contract['is_check'],
            'is_complete' => $authorized_contract['is_complete']
        );

        $return['credit_info'] = $credit;

        $return['is_active'] = $credit['is_active'];


        return new result(true,'success',$return);
    }


    /** 获得会员贷款收款记录
     * @param $member_id
     * @return ormCollection
     */
    public static function getLoanReceivedRecord($member_id)
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $r = new ormReader();
        $sql = "select r.amount,r.currency,r.contract_id,r.create_time,c.contract_sn,DATE_FORMAT(r.create_time,'%Y-%m') month_time,DATE_FORMAT(r.create_time,'%m-%d %H:%i') day_time from loan_disbursement r left join loan_contract c on c.uid=r.contract_id where c.account_id='$account_id' 
        and r.state='".disbursementStateEnum::DONE."' order by r.create_time desc ";
        $rows = $r->getRows($sql);
        return $rows;

    }

    /** 获得会员贷款还款记录
     * @param $member_id
     * @return ormCollection
     */
    public static function getLoanRepaymentRecord($member_id)
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $r = new ormReader();
        $sql = "select r.amount,r.currency,r.contract_id,r.create_time,c.contract_sn,DATE_FORMAT(r.create_time,'%Y-%m') month_time,DATE_FORMAT(r.create_time,'%m-%d %H:%i') day_time from loan_repayment r left join loan_contract c on c.uid=r.contract_id where c.account_id='$account_id' 
        and r.state='".repaymentStateEnum::DONE."' order by r.create_time desc ";
        $rows = $r->getRows($sql);
        return $rows;
    }

    public static function searchMember($params)
    {
        $m = new memberModel();
        $type = $params['type'];
        switch( $type ){
            case 1:
                // guid
                $guid = $params['guid'];
                $member = $m->find(array(
                    'obj_guid' => $guid
                ));
                break;
            case 2:
                // phone
                $country_code = $params['country_code'];
                $phone = $params['phone_number'];
                $phone_arr = tools::getFormatPhone($country_code,$phone);
                $contact_phone = $phone_arr['contact_phone'];
                $member = $m->find(array(
                    'phone_id' => $contact_phone
                ));
                break;
            default:
                return null;
                break;
        }
        if( !$member ){
            return null;
        }

        unset($member['login_password']);
        unset($member['trading_password']);
        unset($member['gesture_password']);

        // 信用值
        $m_credit = new member_creditModel();
        $credit_info = $m_credit->find(array(
            'member_id' => $member['uid']
        ));

        $member['credit'] = $credit_info?$credit_info['credit']:0;
        $member['credit_balance'] = $credit_info?$credit_info['credit_balance']:0;

        return $member;

    }


    public static function getMemberPassedGuaranteeList($member_id)
    {
        // 我的担保人列表
        $r = new ormReader();
        $sql = "select g.*,m.login_code,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.relation_member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.member_id='$member_id' and g.relation_state='".memberGuaranteeStateEnum::ACCEPT."'  ";

        $list1 = $r->getRows($sql);


        // 作为担保人的（通过的）
        $sql = "select g.*,m.login_code,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.relation_member_id='$member_id' and g.relation_state='".memberGuaranteeStateEnum::ACCEPT."'  ";
        $list2 = $r->getRows($sql);

        return array(
            'guarantee_list' => $list1,
            'as_guarantee_list' => $list2
        );
    }

    public function getSavingsGUID() {
        return $this->member_info->obj_guid;
    }

    public function getShortLoanGUID() {
        if (!$this->member_info->short_loan_guid) {
            $this->member_info->short_loan_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::SHORT_LOAN);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate short loan account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->short_loan_guid;
    }

    public function getLongLoanGUID() {
        if (!$this->member_info->long_loan_guid) {
            $this->member_info->long_loan_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::LONG_LOAN);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate long loan account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->long_loan_guid;
    }

    public function getShortDepositGUID() {
        if (!$this->member_info->short_deposit_guid) {
            $this->member_info->short_deposit_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::SHORT_DEPOSIT);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate short deposit account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->short_deposit_guid;
    }

    public function getLongDepositGUID() {
        if (!$this->member_info->long_deposit_guid) {
            $this->member_info->long_deposit_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::LONG_DEPOSIT);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate long deposit account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->long_deposit_guid;
    }

    public static function memberBindBankAccount($params)
    {
        $member_id = intval($params['member_id']);
        $bank_id = $params['bank_id'];
        $account_name = $params['account_name'];
        $account_no = $params['account_no'];
        if( $member_id <= 0 || $bank_id <= 0  || !$account_name || !$account_no ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_bank = new common_bank_listsModel();
        $bank_info = $m_bank->find(array(
            'uid' => $bank_id
        ));
        if( !$bank_info ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $contact_phone = null;
        if( $bank_info['bank_code'] == 'wing' ){
            $country_code = $params['country_code'];
            $phone_number = $params['phone_number'];
            if( !$country_code || !$phone_number ){
                return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
            }
            $phone_arr = tools::getFormatPhone($country_code,$phone_number);
            $contact_phone = $phone_arr['contact_phone'];
        }

        $m_handler = new member_account_handlerModel();

        // 重复检测
        $old = $m_handler->getRow(array(
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'handler_account' => $account_no,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE,
            'bank_code' => $bank_info['bank_code'],
        ));
        if( $old ){
            return new result(false,'Have bound',null,errorCodesEnum::BANK_ALREADY_BOUND);
        }


        /*if( $old ){
            $old_bank = @json_decode($old->handler_property,true);
            if( $old_bank['bank_code'] == $bank_info['bank_code']
                && $old_bank['currency'] == $bank_info['currency']
            ){
                return new result(false,'Have bound',null,errorCodesEnum::BANK_ALREADY_BOUND);
            }
        }*/


        $handler = $m_handler->newRow();
        $handler->member_id = intval($member_id);
        $handler->handler_type = memberAccountHandlerTypeEnum::BANK;
        $handler->handler_name = $account_name;
        $handler->handler_account = $account_no;
        $handler->handler_phone = $contact_phone;
        $handler->handler_property = json_encode($bank_info);
        $handler->is_verified = 1;
        $handler->bank_code = $bank_info['bank_code'];
        $handler->bank_name = $bank_info['bank_name'];
        $handler->state = accountHandlerStateEnum::ACTIVE;
        $handler->create_time = Now();
        $insert = $handler->insert();
        if( !$insert->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }


    public static function getMemberMortgagedGoodsList($member_id)
    {
        $m = new member_assetsModel();
        $list = $m->select(array(
            'member_id' => $member_id,
            'asset_state' => assetStateEnum::CERTIFIED,
            'mortgage_state' => 1
        ));
        if( count($list) < 1 ){
            return null;
        }
        $return = array();
        $m_image = new member_verify_cert_imageModel();
        foreach( $list as $value ){
            // 图片
            $images = $m_image->select(array(
                'cert_id' => $value['cert_id']
            ));
            $one = current($images);
            $value['main_image'] = $one['image_url'];
            $return[] = $value;
        }

        return $return;
    }


    public static function getMemberAssessment($member_id)
    {

        $r = new ormReader();

        // 资产估值
        $sql = "select sum(valuation) total from member_assets where member_id='$member_id' and asset_state='".assetStateEnum::CERTIFIED."' ";
        $asset_value = ($r->getOne($sql))?:0;

        // 业务盈利能力
        $business_profitability = 0;

        return array(
            'asset_evaluation' => $asset_value,
            'business_profitability' => $business_profitability
        );

    }


}