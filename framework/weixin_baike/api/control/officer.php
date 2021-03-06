<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/1
 * Time: 15:18
 */
class officerControl extends bank_apiControl
{

    // operator app 操作类

    public function loginOp()
    {
        $params = array_merge($_GET,$_POST);
        $user_code = trim($params['user_code']);
        $password = trim($params['password']);
        $client_type = $params['client_type'];
        if( !$user_code || !$password ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow(array(
            'user_code' => $user_code
        ));
        if( !$user ){
            return new result(false,'No user',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        if( md5($password) != $user->password ){
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        if( $user->user_status != 1 ){
            return new result(false,'User locked',null,errorCodesEnum::USER_LOCKED);
        }

        // 检查职位
        $user_position = @json_decode($user->user_position,true);
        if( !in_array(userPositionEnum::CREDIT_OFFICER,$user_position) ){
            return new result(false,'No login access',null,errorCodesEnum::NO_LOGIN_ACCESS);
        }

        // 创建登陆日志
        $now = Now();
        $ip = getIp();
        $user->last_login_time = $now;
        $user->last_login_ip = $ip;
        $up = $user->update();
        if( !$up->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }

        $m_user_log = new um_user_logModel();
        $re = $m_user_log->recordLogin($user->uid,$client_type);
        if( !$re->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }

        // 创建token
        $token = md5($user_code.time());
        $m_user_token = new um_user_tokenModel();
        $user_token = $m_user_token->newRow();
        $user_token->user_id = $user->uid;
        $user_token->user_code = $user->user_code;
        $user_token->token = $token;
        $user_token->client_type = $client_type;
        $user_token->create_time = $now;
        $user_token->login_time = $now;
        $insert = $user_token->insert();
        if( !$insert->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }

        $user_info = $user->toArray();
        unset($user_info['password']);
        return new result(true,'success',array(
            'user_info' => $user_info,
            'token' => $token
        ));

    }

    public function logoutOp()
    {
        $params = array_merge($_GET,$_POST);
        $officer_id = $params['officer_id'];
        $token = $params['token'];
        $client_type = $params['client_type'];

        // 记录日志
        $m_log = new um_user_logModel();
        $log = $m_log->orderBy('uid desc ')->getRow(array(
            'user_id' => $officer_id,
            'client_type' => $client_type
        ));
        if( $log ){
            $log->logout_time = Now();
            $log->update_time = Now();
            $log->update();
        }

        //销毁token（所有，单设备支持）
        $sql = "delete from um_user_token where user_id='$officer_id' ";
        $del = $m_log->conn->execute($sql);
        if( !$del->STS ){
            return new result(false,'Logout fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }

    public function submitMemberCertIdOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::idVerifyCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertFamilyBookOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::familyBookVerifyCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertResidentBookOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::residentBookCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertWorkOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::workCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertAssetsOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = memberClass::assetCert($params,certSourceTypeEnum::OPERATOR);
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

    public function searchMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member = memberClass::searchMember($params);
        return new result(true,'success',$member);
    }



    public function getMemberAllCertResultOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberCertStateOrCount($member_id);
        return $re;
    }


    public function getMemberCertDetailInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberCertResult($params);
        return $re;
    }


    public function getCoFollowedMemberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $r = new ormReader();
        $sql = "select m.*,c.credit,c.credit_balance from member_follow_officer f left join client_member m on m.uid=f.member_id 
        left join member_credit c on c.member_id=m.uid where f.officer_id='$officer_id' 
        and f.is_active='1' ";
        $list = $r->getRows($sql);
        return new result(true,'success',$list);

    }


    /** 为客户提交贷款申请
     * @return result
     */
    public function addLoanRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $member_id = intval($params['member_id']);
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $loan_time = intval($params['loan_time']);
        $loan_time_unit = $params['loan_time_unit'];


        if( $amount<=0 || !$member_id || !$currency || $loan_time<=0 || !$loan_time_unit ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }


        $m_user = new um_userModel();
        $officer = $m_user->getRow($officer_id);
        if( !$officer ){
            return new result(false,'Invalid operator',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $applicant_name = $member->display_name?:($member->login_code?:'Unknown');
        $applicant_address = null;  // member 地址
        $contact_phone = $member->phone_id;

        $m_apply = new loan_applyModel();

        $apply = $m_apply->newRow();
        $apply->member_id = $member_id;
        $apply->applicant_name = $applicant_name;
        $apply->applicant_address = $applicant_address;
        $apply->apply_amount = $amount;
        $apply->currency = $currency;
        $apply->loan_time = $loan_time;
        $apply->loan_time_unit = $loan_time_unit;
        $apply->contact_phone = $contact_phone;
        $apply->apply_time = Now();
        $apply->request_source = loanApplySourceEnum::OPERATOR_APP;
        $apply->credit_officer_id = $officer_id;
        $apply->creator_id = $officer_id;
        $apply->creator_name = $officer->user_name;
        $insert = $apply->insert();
        if( !$insert->STS ){
            return new result(false,'Apply fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$apply);

    }


    public function addMemberGuaranteeRequestOp()
    {
        $re = $this->checkOperator();
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

        return new result(true,'success',$new_row);

    }


    public function getMemberGuaranteeListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $list = memberClass::getMemberPassedGuaranteeList($member_id);

        return new result(true,'success',$list);

    }

    public function getMemberLoanRequestListOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;

        $r = new ormReader();
        $sql = "select * from loan_apply where member_id='$member_id' order by apply_time desc  ";
        $list = $r->getPage($sql,$page_num,$page_size);

        return new result(true, 'success', array(
            'total_num' => $list->count,
            'total_pages' => $list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list->rows
        ));
    }


    public function getCoBoundLoanRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $state = $params['state'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;
        $r = new ormReader();

        $where_str = '';
        switch ( $state ){
            case 1:
                // 待处理
                $where_str .= " and state in('".loanApplyStateEnum::ALLOT_CO."','".loanApplyStateEnum::CO_HANDING."') ";
                break;
            case 2:
                // 拒绝的
                $where_str .= " and state='".loanApplyStateEnum::CO_CANCEL."' ";
                break;
            case 3:
                // 通过的
                $where_str .= " and state='".loanApplyStateEnum::CO_APPROVED."' ";
                break;
            default:
                break;
        }

        //  将CO该处理的排在前面
        $sql = "select * from loan_apply where credit_officer_id='$officer_id' $where_str order by 
        state not in ('".loanApplyStateEnum::ALLOT_CO."','".loanApplyStateEnum::CO_HANDING."'), apply_time desc ";


        $list = $r->getPage($sql,$page_num,$page_size);

        return new result(true, 'success', array(
            'total_num' => $list->count,
            'total_pages' => $list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list->rows
        ));

    }


    public function getLoanRequestDetailOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->find(array(
            'uid' => $request_id
        ));
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'request_detail' => $apply
        ));
    }


    public function getAllLoanProductOp()
    {
        $m = new loan_productModel();
        $sql = "select uid product_id,product_code,product_name from loan_product where state='".loanProductStateEnum::ACTIVE."' ";
        $list = $m->reader->getRows($sql);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function loanRequestBindMemberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $member_id = $params['member_id'];

        $m_loan_apply = new loan_applyModel();

        $apply = $m_loan_apply->getRow($request_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 绑定client
        $apply->member_id = $member_id;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Bind fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'request_detail' => $apply
        ));

    }


    public function loanRequestCheckOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $officer_id = $params['officer_id'];
        $check_result = intval($params['check_result']);
        $remark = $params['remark'];

        $m_user = new um_userModel();
        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->getRow($request_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $user = $m_user->getRow($officer_id);
        if( !$user ){
            return new result(false,'No user',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        if( $apply->state == loanApplyStateEnum::OPERATOR_REJECT
            || $apply->state == loanApplyStateEnum::CO_CANCEL
        ){
            return new result(false,'Have canceled',null,errorCodesEnum::HAVE_CANCELED);
        }

        // 处理过了
        if( $apply->state >= loanApplyStateEnum::CO_APPROVED ){
            return new result(false,'Handle yet',null,errorCodesEnum::HAVE_HANDLED);
        }

        if( $check_result == 1 ){
            $apply->state = loanApplyStateEnum::CO_HANDING;
        }else{
            $apply->state = loanApplyStateEnum::CO_CANCEL;
        }

        $apply->co_id = $user->uid;
        $apply->co_name = $user->user_name;
        $apply->co_remark = $remark;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'request_detail' => $apply
        ));

    }


    public function loanRequestBindProductOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $product_id = $params['product_id'];
        $repayment_type = $params['repayment_type'];
        $repayment_period = $params['repayment_period'];

        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->getRow($request_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != loanApplyStateEnum::CO_HANDING ){
            return new result(false,'Un-match operation',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => intval($apply->member_id)
        ));
        if( !$member ){
            return new result(false,'Un-match operation',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $m_loan_product = new loan_productModel();
        $product = $m_loan_product->getRow($product_id);
        if( !$product ){
            return new result(false,'No loan product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }


        // 检查是否支持的还款方式
        $repayment_type_arr = (new interestPaymentEnum())->toArray();
        if( !in_array($repayment_type,$repayment_type_arr) ){
            return new result(false,'Un-supported type',null,errorCodesEnum::NOT_SUPPORTED);
        }

        if( $repayment_type != interestPaymentEnum::SINGLE_REPAYMENT ){
            $repayment_period_arr = (new interestRatePeriodEnum())->toArray();
            if( !in_array($repayment_period,$repayment_period_arr) ){
                return new result(false,'Un-supported type',null,errorCodesEnum::NOT_SUPPORTED);
            }
        }

        // 先写入记录
        $apply->product_id = $product->uid;
        $apply->product_name = $product->product_name;
        $apply->repayment_type = $repayment_type;
        $apply->repayment_period = $repayment_period;
        $apply->state = loanApplyStateEnum::CO_HANDING;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        $return = array(
            'request_detail' => $apply,
            'interest_info' => null
        );



        // 计算贷款天数
        $loan_days_re = loan_baseClass::calLoanDays($apply->loan_time,$apply->loan_time_unit);
        if( !$loan_days_re->STS ){
            return new result(true,'success',$return);
        }
        $loan_days = $loan_days_re->DATA;

        $extend_info = $member;
        // 查询利率信息
        $re = loan_baseClass::getLoanInterestDetail($product_id,$apply->apply_amount,$apply->currency,$loan_days,$repayment_type,$repayment_period,$extend_info);
        if( !$re->STS ){
            return new result(true,'success',$return);
        }

        $data = $re->DATA;
        $interest_info = $data['interest_info'];
        $return['interest_info'] = $interest_info;
        $return['size_rate'] = $data['size_rate'];
        $return['special_rate'] = $data['special_rate'];

        // todo 是否在这步就写入利率信息等
        $apply->interest_rate = $interest_info['interest_rate'];
        $apply->interest_rate_type = $interest_info['interest_rate_type']?1:0;
        $apply->interest_rate_unit = $interest_info['interest_rate_unit'];
        $apply->interest_min_value = round($interest_info['interest_min_value'],2);
        $apply->operation_fee = $interest_info['operation_fee'];
        $apply->operation_fee_type = $interest_info['operation_fee_type']?1:0;
        $apply->operation_fee_unit = $interest_info['operation_fee_unit'];
        $apply->operation_min_value = round($interest_info['operation_min_value'],2);
        $apply->admin_fee = $interest_info['admin_fee']?:0;
        $apply->admin_fee_type = $interest_info['admin_fee_type']?:0;
        $apply->loan_fee = $interest_info['loan_fee']?:0;
        $apply->loan_fee_type = $interest_info['loan_fee_type']?:0;
        $apply->is_full_interest = $interest_info['is_full_interest']?:0;
        $apply->prepayment_interest = $interest_info['prepayment_interest']?:0;
        $apply->prepayment_interest_type = $interest_info['prepayment_interest_type']?:0;
        $apply->penalty_rate = $interest_info['penalty_rate']?:$product->penalty_rate;
        $apply->penalty_divisor_days = $interest_info['penalty_divisor_days']?:$product->penalty_divisor_days;
        $apply->grace_days = intval($interest_info['grace_days']);
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$return);

    }


    public function loanRequestCoApprovedOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $m_loan_apply = new loan_applyModel();

        $apply = $m_loan_apply->getRow($request_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != loanApplyStateEnum::CO_HANDING ){
            return new result(false,'Un-match operation',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        // 未完成全部步骤
        if( !$apply->member_id || !$apply->product_id || !$apply->interest_rate ){
            return new result(false,'Un-match operation',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $preview = (new loan_baseClass())->loanPreviewBeforeCreateContract($apply->apply_amount,$apply->currency,$apply->loan_time,$apply->loan_time_unit,$apply->repayment_type,$apply->repayment_period,$apply);
        if( !$preview->STS ){
            return new result(false,'Un-match operation',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $data = $preview->DATA;

        $apply->state = loanApplyStateEnum::CO_APPROVED;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        $return = array(
            'request_detail' => $apply,
            'preview_info' => $data
        );


        return new result(true,'success',$return);


    }


    public function getBoundMemberLoanContractListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $r = new ormReader();

        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;

        $sql = "select c.*,m.obj_guid member_guid,m.login_code,m.display_name,m.kh_display_name from member_follow_officer o inner join client_member m on m.uid=o.member_id 
        inner join loan_account a on a.obj_guid=m.obj_guid inner join loan_contract c on c.account_id=a.uid 
        where o.officer_id='$officer_id' and o.is_active='1' and c.state>='".loanContractStateEnum::PENDING_DISBURSE."' order by c.create_time desc ";

        $list = $r->getPage($sql,$page_num,$page_size);

        return new result(true, 'success', array(
            'total_num' => $list->count,
            'total_pages' => $list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list->rows
        ));
    }


    public function getLoanContractDetailOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $re = loan_contractClass::getLoanContractDetailInfo($contract_id);
        return $re;

    }


    /** 签到
     * @return result
     */
    public function signInOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $coord_x = $params['coord_x'];
        $coord_y = $params['coord_y'];
        if( !$officer_id || !$coord_x || !$coord_y ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_user = new um_userModel();
        $user = $m_user->getRow($officer_id);
        if( !$user ){
            return new result(false,'No user',null,errorCodesEnum::USER_NOT_EXISTS);
        }
        $location = $params['location'];
        $remark = $params['remark'];
        $m = new um_user_trackModel();
        $track = $m->newRow();
        $track->user_id = $user->uid;
        $track->user_name = $user->user_name;
        $track->coord_x = $coord_x;
        $track->coord_y = $coord_y;
        $track->location = $location;
        $track->remark = $remark;
        $track->sign_day = date('Y-m-d');
        $track->sign_time = Now();
        $in = $track->insert();
        if( !$in->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }

    public function getOfficerFootprintOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $date = trim($params['date']);
        $r = new ormReader();
        $sql = "select * from um_user_track where user_id='$officer_id' and sign_day='$date' order by sign_time asc";
        $lists = $r->getRows($sql);
        return new result(true,'success',$lists);
    }


    public function getOfficerFootprintListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $page_num = intval($params['page_num'])?:1;
        $page_size = intval($params['page_size'])?:100000;
        $r = new ormReader();
        $sql = "select  *,DATE_FORMAT(sign_time,'%Y-%m') sign_month from um_user_track where user_id='$officer_id' group by sign_day order by sign_time desc ";
        $re = $r->getPage($sql,$page_num,$page_size);
        return new result(true,'success',array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $re->rows
        ));
    }


    public function getOfficerBaseInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        if( $officer_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m = new um_userModel();
        $info = $m->getRow($officer_id);
        if( !$info ){
            return new result(false,'No user ',null,errorCodesEnum::USER_NOT_EXISTS);
        }
        $info = $info->toArray();
        unset($info['password']);
        return new result(true,'success',array(
            'user_info' => $info
        ));
    }

    public function getMemberAssetsEvaluateOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $r = new ormReader();
        $sql = "SELECT a.* from member_verify_cert as m RIGHT JOIN member_assets as a ON m.uid = a.cert_id
                WHERE m.member_id = $member_id AND a.asset_state = 100 ORDER BY a.create_time desc ";
        $list = $r->getRows($sql);

        $sql1 = "SELECT sum(a.valuation) as total from member_verify_cert as m RIGHT JOIN member_assets as a ON m.uid = a.cert_id
                WHERE m.member_id = $member_id AND a.asset_state = 100  ";
        $total = $r->getRow($sql1);
        return new result(true,'success',array(
            'total_amount' => $total['total'],
            'list' => $list
        ));

    }

    public function getMemberAssetDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $m = new member_assetsModel();
        $asset = $m->getRow($asset_id);
        if( !$asset ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'asset_detail' => $asset
        ));
    }

    public function submitMemberAssetsEvaluateOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $amount = $params['valuation'];
        $officer_id = $params['officer_id'];
        if( $amount <= 0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }
        $m_member_assets = new member_assetsModel();
        $assets = $m_member_assets->getRow($params['id']);
        if( !$assets ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $officer = (new um_userModel())->getRow($officer_id);
        if( !$officer ){
            return new result(false,'No user',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $assets->valuation = $amount;
        $assets->remark = $params['remark'];
        $assets->update_time = Now();
        $up = $assets->update();
        if( !$up->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        // 标记member
        $member = (new memberModel())->getRow($assets->member_id);
        if( $member ){
            $member->co_id = $officer_id;
            $member->co_name = $officer->user_name;
            $member->co_state = 1;
            $member->co_remark = 'Asset evaluation';
            $member->update_time = Now();
            $member->update();
        }


        // 推送消息
        $m_log = new member_cert_logModel();
        $m_log->insertCertLog($assets->uid,2);

        return new result(true,'success',array(
            'request_detail' => $assets
        ));
    }

    public function submitMemberSuggestCreditOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $officer_id = intval($params['officer_id']);
        $monthly_repayment_ability = round($params['monthly_repayment_ability'],2);
        $suggest_credit = round($params['suggest_credit']);
        $remark = $params['remark'];

        if( !$member_id || !$officer_id ||  $monthly_repayment_ability<=0 || $suggest_credit <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $officer = (new um_userModel())->getRow($officer_id);

        $m = new member_credit_suggestModel();
        $insert = $m->insert(array(
            'member_id' => $member_id,
            'user_id' => $officer_id,
            'user_name' => $officer['user_name'],
            'monthly_repayment_ability' => $monthly_repayment_ability,
            'suggest_credit' => $suggest_credit,
            'remark' => $remark,
            'create_time' => Now()
        ));
        if( !$insert->STS ){
            return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }


    public function getMemberWorkDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $m = new member_workModel();
        $work = $m->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id
        ));

        return new result(true,'success',array(
            'work_detail' => $work
        ));

    }

    public function getMemberAssessmentOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);

        // 会员评估
        $data = memberClass::getMemberAssessment($member_id);

        return new result(true,'success',$data);
    }


    public function getTaskSummaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = intval($params['officer_id']);

        $r = new ormReader();

        // 待处理贷款申请
        $sql = " select count(*) from loan_apply where credit_officer_id='$officer_id' and state 
        in('".loanApplyStateEnum::ALLOT_CO."','".loanApplyStateEnum::CO_HANDING."') ";
        $loan_apply_num =  $r->getOne($sql);

        return new result(true,'success',array(
            'task_loan_apply' => $loan_apply_num
        ));

    }


    public function getMemberResidencePlaceOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_address = new common_addressModel();
        $address = $m_address->getRow(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_guid' => $member->obj_guid,
            'address_category' => addressCategoryEnum::MEMBER_RESIDENCE_PLACE
        ));
        return new result(true,'success',array(
            'address_info' => $address
        ));
    }


    public function editMemberResidencePlaceOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = intval($params['officer_id']);
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $type = addressCategoryEnum::MEMBER_RESIDENCE_PLACE;
        $id1 = intval($params['id1']);
        $id2 = intval($params['id2']);
        $id3 = intval($params['id3']);
        $id4 = intval($params['id4']);
        $full_text = $params['full_text'];
        $cord_x = round($params['cord_x'],6);
        $cord_y = round($params['cord_y'],6);

        $m_address = new common_addressModel();

        if( $params['address_id'] ){

            $address = $m_address->getRow($params['address_id']);
            if( !$address ){
                return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
            }

            $address->id1 = $id1;
            $address->id2 = $id2;
            $address->id3 = $id3;
            $address->id4 = $id4;
            $address->coord_x = $cord_x;
            $address->coord_y = $cord_y;
            $address->full_text = $full_text;
            $address->create_time = Now();

            $up = $address->update();
            if( !$up->STS ){
                return new result(false,'Edit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }

            return new result(true,'success',$address);


        }else{
            $new_row = $m_address->newRow();
            $new_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
            $new_row->obj_guid = $member->obj_guid;
            $new_row->address_category = $type;
            $new_row->id1 = $id1;
            $new_row->id2 = $id2;
            $new_row->id3 = $id3;
            $new_row->id4 = $id4;
            $new_row->coord_x = $cord_x;
            $new_row->coord_y = $cord_y;
            $new_row->full_text = $full_text;
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if( !$insert->STS ){
                return new result(false,'Add fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
            return new result(true,'success',$new_row);
        }
    }





}