<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:44
 */

class memberControl extends bank_apiControl
{





    /** 弃用
     * app注册member
     * @return result
     */
    public function createOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        // 设置必须的参数
        if(
            empty($params['login_code']) || empty($params['family_name']) || empty($params['given_name']) || empty($params['password'])
            || empty($params['phone']) || empty($params['country_code']  )
        )
        {
            return new result(false,'Lack of param',null,errorCodesEnum::DATA_LACK);
        }

        // 验证短信验证码
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        $m_verify_code = new phone_verify_codeModel();
        $row = $m_verify_code->getRow(array(
            'uid' => $sms_id,
            'verify_code' => $sms_code
        ));
        if( !$row ){
            return new result(false,'SMS code error',null,errorCodesEnum::SMS_CODE_ERROR);
        }

        $params['is_verify_phone'] = 1;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = memberClass::addMember($params);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $member = $rt->DATA;
            $conn->submitTransaction();
            return new result(true,'Success',$member);

        }catch(Exception $e){
           $conn->rollback();
           return new result(false,$e->getMessage(),null,errorCodesEnum::DB_ERROR);
        }



    }


    /** 电话注册
     * @return result
     */
    public function phoneRegisterOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::phoneRegister($params);

    }

    public function phoneRegisterNewOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::phoneRegisterNew($params);
    }


    public function checkLoginAccountIsExistOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $login_code = $params['login_code'];
        $is = memberClass::checkLoginAccountIsExist($login_code);
        return new result(true,'success',array(
            'is_exist' => $is
        ));
    }


    /** 注册信息修改
     * @return result
     */
    public function editRegisterInfoOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::editRegisterMemberInfo($params);
    }


    public function verifyLoginPasswordOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $password = $params['password'];
        return memberClass::verifyLoginPassword($member_id,$password);
    }


    /** 密码登陆
     * @return result
     */
    public function passwordLoginOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::passwordLogin($params);
    }


    /** 手势密码登陆
     * @return result
     */
    public function gestureLoginOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::gestureLogin($params);

    }

    /** 指纹登陆
     * @return result
     */
    public function fingerprintLoginOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::fingerprintLogin($params);
    }



    /** 暂停使用
     * member app 登陆
     * @return result
     */
    public function appLoginOp()
    {

        return new result(false);
        $params = array_merge(array(),$_GET,$_POST);
        if( empty($params['login_code']) ){  // 第三方登陆的不会有密码  login_password
            return new result(false,'Invalid param',null,errorCodesEnum::DATA_LACK);
        }
        $m_member = new memberModel();

        $client_id = $params['client_id']?intval($params['client_id']):0;
        $client_type = $params['client_type'];

        // passport 通行的方式
        $pass_type = isset($params['passport_type'])?$params['passport_type']:0;
        $rt = memberClass::checkPassport(array(
            'login_code' => $params['login_code'],
            'login_password' => $params['login_password']
        ),$pass_type);

        if( !$rt->STS ){
            return new result(false,'Login fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $o_member = $rt->DATA;
        $member = $m_member->getRow($o_member['uid']);  // 防止错误返回
        if( !$member ){
            return new result(false,'Login fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        $login_ip = getIp();
        $member->last_login_time = Now();
        $member->last_login_ip = $login_ip;
        $member->update();

        // 创建token令牌
        $m_member_token = new member_tokenModel();
        $token_row = $m_member_token->newRow();
        $token_row->member_id = $member->uid;
        $token_row->login_code = $member->login_code;
        $token_row->token = md5($params['login_code'].time());
        $token_row->create_time = Now();
        $token_row->login_time = Now();
        $token_row->client_type = $params['client_type'];
        $insert = $token_row->insert();
        if( !$insert->STS ){
            return new result(false,'Create token fail',null,errorCodesEnum::DB_ERROR);
        }

        $m_member_login_log = new member_login_logModel();
        $log = $m_member_login_log->newRow();
        $log->member_id = $member->uid;
        $log->client_id = $client_id;
        $log->client_type = $client_type;
        $log->login_time = Now();
        $log->login_ip = $login_ip;
        $log->login_area = '';  // todo ip获取区域？
        $insert = $log->insert();
        if( !$insert->STS ){
            return new result(false,'Log error',null,errorCodesEnum::DB_ERROR);
        }

        $member_info = $member->toArray();
        unset($member_info['login_password']);

        $member_info['grade_code'] = '';
        $member_info['grade_caption'] = '';
        $m_member_grade = new member_gradeModel();
        $grade_info = $m_member_grade->getRow(array(
            'grade_code' => $member->member_grade,
        ));
        if( $grade_info ){
            $member_info['grade_code'] = $grade_info->grade_code;
            $member_info['grade_caption'] = $grade_info->grade_caption;
        }
        return new result(true,'Login success',array(
            'token' => $token_row->token,
            'member_info' => $member_info
        ));

    }


    /**
     * APP 退出登录
     * @return result
     */
    public function appLogoutOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        if( !isset($params['member_id']) || !isset($params['client_type']) ){
            return new result(false,'Param lack',null,errorCodesEnum::DATA_LACK);
        }
        if( !$member_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 销毁token
        $m_member_token = new member_tokenModel();
        $where = " member_id='$member_id' ";
        $m_member_token->deleteWhere($where);
        $m_login_log = new member_login_logModel();
        $login_log = $m_login_log->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'client_type' => $params['client_type'],
        ));
        if( $login_log ){
            $login_log->logout_time = Now();
            $login_log->update_time = Now();
        }

        return new result(true,'success');


    }

    /**
     * 忘记密码，重置密码
     * @return result
     */
    public function resetPwdOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['type'];
        switch( $type ){
            case 'sms':
                $rt = memberClass::resetPwdBySms($params);
                return $rt;
                break;
            default:
                $rt = new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                return $rt;
        }

    }


    public function getMemberBaseInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $member_info = memberClass::getMemberBaseInfo($member_id);
        return new result(true,'success',$member_info);
    }



    /** 身份证认证
     * @return result
     */
    public function idVerifyCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::idVerifyCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 户口本认证
     * @return result
     */
    public function familyBookCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::familyBookVerifyCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 居住证认证
     * @return result
     */
    public function residentBookCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::residentBookCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }



    /** 家庭关系认证
     * @return result
     */
    public function familyRelationshipCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::familyRelationshipCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 工作认证
     * @return result
     */
    public function workCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::workCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }

    /** 资产认证
     * @return result
     */
    public function assetCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::assetCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function bindAceAccountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $re = memberClass::bindLoanAceAccount($params);
        return $re;
    }

    public function editLoanAceAccountInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::editLoanBindAceAccountInfo($params);
        return $re;
    }

    /** 贷款业务的
     * @return result
     */
    public function getMemberAceAccountInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberLoanAceAccountInfo($member_id);
        return $re;
    }



    public function message_listOp() {
        $re = $this->checkToken();
        if (!$re->STS) return $re;

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if ($member_id <=0){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $page_num = $params['page_num'];
        $page_size = $params['page_size'];

        return member_messageClass::getReceivedMessages($member_id, $page_num, $page_size);
    }

    public function message_unread_countOp() {
        $re = $this->checkToken();
        if (!$re->STS) return $re;

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if ($member_id <=0){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        return member_messageClass::getUnreadMessagesCount($member_id);
    }

    public function message_readOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $message_id = $params['message_id'];
        return member_messageClass::readMessage($member_id,$message_id);
    }

    public function message_deleteOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $messages = $params['message_id_list'];
        return member_messageClass::deleteMessages($member_id,$messages);
    }

    public function loanContractListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $re = memberClass::getLoanContractList($params);
        return $re;

    }

    public function changePwdOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::changePassword($params);
        return $re;

    }



    public function getCertedResultOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberCertResult($params);
        return $re;
    }


    public function getAccountIndexInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberAccountSumInfo($member_id);
        return $re;
    }

    public function getInsuranceContractListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getInsuranceContractList($params);
        return $re;
    }





    public function getMemberLoanApplyListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberLoanApplyList($params);
        return $re;
    }

    public function getCreditHistoryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::getMemberCreditHistory($params);
    }


    public function editAvatorOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( empty($_FILES['avator']) ){
            return new result(false,'No upload image',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $default_dir = 'avator/'.$member_id;
        $upload = new UploadFile();
        $upload->set('save_path',null);
        $upload->set('default_dir',$default_dir);
        $re = $upload->server2upun('avator');
        if( $re == false ){
            return new result(false,'Upload photo fail',null,errorCodesEnum::API_FAILED);
        }
        $img_path = $upload->full_path;
        $member->member_image = $img_path;
        $member->member_icon = $img_path;
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Edit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,array(
            'member_image' => $member->member_image,
            'member_icon' => $member->member_icon
        ));

    }

    public function editLoginCodeOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $login_code = $params['login_code'];
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::editMemberLoginCode($member_id,$login_code);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function editNicknameOp()
    {
        return new result(false,'Function close',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $nickname = $params['nickname'];
        if( !$member_id || !$nickname ){
            return new result(false,'');
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->nickname = $nickname;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Edit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,$member);

    }

    public function getMemberQrcodeImageOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $url = ENTRY_API_SITE_URL.'/member.qrcode.image.php?member_id='.$member_id;
        return new result(true,'success',$url);
    }

    public function setGesturePasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $gesture_pwd = trim($params['gesture_password']);
        if( !$member_id || !$gesture_pwd ){
            return new result(false,'Invalid Param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->gesture_password = $gesture_pwd;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$member);
    }

    public function setFingerprintOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $fingerprint = trim($params['fingerprint']);
        if( !$member_id || !$fingerprint ){
            return new result(false,'Invalid Param',null,errorCodesEnum::INVALID_PARAM);
        }
        // url解码
        $fingerprint = urldecode($fingerprint);

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->fingerprint = $fingerprint;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'fingerprint' => $fingerprint
        ));

    }


    public function setTradingPasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $trading_password = $params['trading_password'];
        $login_password = $params['login_password'];
        $id_no = $params['id_no'];
        if( !$member_id || !$trading_password || !$login_password || !$id_no ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 验证登陆密码
        if( $member->login_password != md5($login_password) ){
            return new result(false,'Login password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        // 验证身份证号
        if( !$member->id_sn ){
            return new result(false,'Not certificate ID',null,errorCodesEnum::NOT_CERTIFICATE_ID);
        }
        $last_no = substr($member->id_sn,-4);
        if( $last_no != $id_no ){
            return new result(false,'ID sn error',null,errorCodesEnum::ID_SN_ERROR);
        }

        // 两次密码是否一致
        if( $member->trading_password ){
            if( $member->trading_password == md5($trading_password) ){
                return new result(false,'Same password',null,errorCodesEnum::SAME_PASSWORD);
            }
        }

        $member->trading_password = md5($trading_password);
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }

    public function isSetTradingPasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $return = memberClass::isSetTradingPassword($member_id);
        return new result(true,'success',$return);
    }


    public function setTradingPasswordVerifyAmountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        if( !$member_id || !$amount || !$currency ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);

        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if( !$member->trading_password ){
            return new result(false,'Not set trading password',null,errorCodesEnum::NOT_SET_TRADING_PASSWORD);
        }

        $member->trading_verify_amount = round($amount,2);
        $member->trading_verify_currency = $currency;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }



    public function getLoanBindAutoDeductionAccountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return memberClass::getLoanBindAutoDeductionAccount($member_id);

    }

    public function forgotGestureOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->gesture_password = null;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new  result(false,'Reset fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',array(
            'gesture_password' => $member['gesture_password']
        ));
    }


    public function assetDeleteOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $asset_id = $params['asset_id'];

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::deleteAsset($member_id,$asset_id);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function deleteFamilyRelationshipOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $relation_id = $params['relation_id'];

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::deleteFamilyRelationship($member_id,$relation_id);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    public function getMemberLoanSummaryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $own_re = memberClass::getMemberLoanSummary($member_id,1);
        if( !$own_re->STS ){
            return $own_re;
        }
        $own_loan_summary = $own_re->DATA;

        $guarantee_loan_re = memberClass::getMemberLoanSummary($member_id,2);
        if( !$guarantee_loan_re->STS ){
            return $guarantee_loan_re;
        }
        $guarantee_loan_summary = $guarantee_loan_re->DATA;

        return new result(true,'success',array(
            'own_loan_summary' => $own_loan_summary,
            'as_guarantee_loan_summary' => $guarantee_loan_summary
        ));

    }


    /** 添加担保人
     * @return result
     */
    public function addGuaranteeOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $relation_type = $params['relation_type'];
        $guarantee_member_account = trim($params['guarantee_member_account']);

        $m_member = new memberModel();

        $o_member = $m_member->getRow($member_id);
        if( !$o_member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $relate_member = $m_member->getRow(array(
            'login_code' => $guarantee_member_account
        ));

        if( !$relate_member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $m_guarantee = new member_guaranteeModel();
        $new_row = $m_guarantee->newRow();
        $new_row->member_id = $member_id;
        $new_row->relation_member_id = $relate_member->uid;
        $new_row->relation_type = $relation_type;
        $new_row->create_time = Now();
        $new_row->relation_state = memberGuaranteeStateEnum::CREATE;
        $insert  = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Add fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');


    }


    public function guaranteeConfirmOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $uid = $params['uid'];
        $state = $params['state'];
        $m_guarantee = new member_guaranteeModel();
        $row = $m_guarantee->getRow(array(
            'relation_member_id' => $member_id,
            'uid' => $uid
        ));
        if( !$row ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $state == 0 ){
            $row->relation_state = memberGuaranteeStateEnum::REJECT;
        }else{
            $row->relation_state = memberGuaranteeStateEnum::ACCEPT;
        }
        $row->update_time = Now();
        $up = $row->update();
        if( !$up->STS ){
            return new result(false,'Update fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }


    public function getMemberGuaranteeListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 担保人列表
        $r = new ormReader();
        $sql = "select g.*,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.relation_member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.member_id='$member_id' and g.relation_state='".memberGuaranteeStateEnum::ACCEPT."'  ";
        $list1 = $r->getRows($sql);

        // 作为担保人的（申请+通过的）
        $sql = "select g.*,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.relation_member_id='$member_id' and g.relation_state in('".memberGuaranteeStateEnum::CREATE."','".memberGuaranteeStateEnum::ACCEPT."') ";
        $list2 = $r->getRows($sql);


        return new result(true,'success',array(
            'guarantee_list' => $list1,
            'apply_list' => $list2
        ));

    }


    public function queryMemberCreditOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $credit = memberClass::getCreditBalance($member_id);
        return new result(true,'success',$credit);
    }


    public function getMemberCreditProcessOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id'])?:0;
        return memberClass::getMemberCreditProcess($member_id);
    }


    public function getMemberLoanReceivedRecordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $list = memberClass::getLoanReceivedRecord($member_id);
        return new result(true,'success',$list);
    }

    public function getMemberLoanRepaymentRecordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $list = memberClass::getLoanRepaymentRecord($member_id);
        return new result(true,'success',$list);
    }

    public function bindBankAccountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::memberBindBankAccount($params);
        return $re;

    }

    public function getBindBankListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $list = member_handlerClass::getMemberBindBankList($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function getMemberMortgageGoodsListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $list = memberClass::getMemberMortgagedGoodsList($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }

    public function deleteBindBankOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $bind_id = $params['bind_id'];
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'uid' => $bind_id,
            'member_id' => $member_id
        ));
        if( !$handler ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $handler->state = accountHandlerStateEnum::HISTORY;
        $handler->update_time = Now();
        $up = $handler->update();
        if( !$up->STS ){
            return new result(false,'Delete fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');

    }

    public function prepaymentApplyCancelOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $apply_id = $params['apply_id'];
        $m = new loan_prepayment_applyModel();
        $apply = $m->getRow($apply_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != prepaymentApplyStateEnum::CREATE ){
            return new result(false,'Handling...',null,errorCodesEnum::HANDLING_LOCKED);
        }
        $delete = $apply->delete();
        if( !$delete->STS ){
            return new result(false,'Cancel fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }

    public function getSavingsBalanceOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObject = new objectMemberClass($member_id);
        $cny_balance = $memberObject->getSavingsAccountBalance();

        return new result(true,'success',array(
            'savings_balance' => $cny_balance
        ));
    }


    /** 存取款绑定账户
     * @return result
     */
    public function getMemberBizAccountHandlerOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];

        // 暂时只支持ACE
        $m = new member_account_handlerModel();
        $handler_list = $m->select(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));

        $list = array();
        foreach( $handler_list as $handler ){
            $handler['handler_account'] = maskInfo($handler['handler_account']);
            $list[] = $handler;
        }


        return new result(true,'success',array(
            'handler_list' => $list
        ));
    }

    public function addMemberAddressOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $id1 = intval($params['id1']);
        $id2 = intval($params['id2']);
        $id3 = intval($params['id3']);
        $id4 = intval($params['id4']);
        $full_text = $params['full_text'];

        $m_address = new common_addressModel();
        $new_row = $m_address->newRow();
        $new_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
        $new_row->obj_guid = $member->obj_guid;
        $new_row->id1 = $id1;
        $new_row->id2 = $id2;
        $new_row->id3 = $id3;
        $new_row->id4 = $id4;
        $new_row->full_text = $full_text;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Add fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$new_row);

    }




}
