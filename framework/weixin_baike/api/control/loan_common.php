<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 10:17
 */
class loan_commonControl extends bank_apiControl
{

    public function newCalculatorOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $loan_amount = $params['loan_amount'];
        $loan_period = $params['loan_period'];
        $loan_period_unit = $params['loan_period_unit'];
        $repayment_type = $params['repayment_type'];
        $repayment_period = $params['repayment_period'];
        if( $loan_amount < 1 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $loan_period <= 0 ){
            return new result(false,'Invalid loan period',null,errorCodesEnum::INVALID_PARAM);
        }
        $re = (new loan_baseClass())->calculator($loan_amount,$loan_period,$loan_period_unit,$repayment_type,$repayment_period);
        return $re;
    }

    public function contractDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $re = loan_contractClass::getLoanContractDetailInfo($contract_id);
        return $re;
    }

    public function appLoanApplyOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);

        $amount = round($params['amount'],2);
        $propose = $params['loan_propose'];
        $loan_time = intval($params['loan_time']);
        $loan_time_unit = $params['loan_time_unit'];
        $mortgage = $params['mortgage'];  // 多个用,隔开
        $currency = $params['currency']?:currencyEnum::USD;

        if( $amount <= 0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_PARAM);
        }

        // 登陆会员
        if( $member_id ){
            $m_member = new memberModel();
            $member = $m_member->getRow($member_id);
            if( !$member ){
                return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
            }

            $applicant_name = $member->display_name?:($member->login_code?:'Unknown');
            $applicant_address = null;  // member 地址
            $contact_phone = $member->phone_id;

        }else{
            // 没登陆
            $applicant_name = $params['name'];
            $applicant_address = $params['address'];
            $country_code = $params['country_code'];
            $phone = $params['phone'];
            $sms_id = $params['sms_id'];
            $sms_code = $params['sms_code'];
            if( !$applicant_name || !$applicant_address || !$country_code || !$phone || !$sms_id || !$sms_code ){
                return new result(false,'Lack param',null,errorCodesEnum::DATA_LACK);
            }
            $phone_arr = tools::getFormatPhone($country_code,$phone);
            $contact_phone = $phone_arr['contact_phone'];
            if( !isPhoneNumber($contact_phone) ){
                return new result(false,'Invalid phone',null,errorCodesEnum::INVALID_PHONE_NUMBER);
            }
            // 验证码
            $m_sms = new phone_verify_codeModel();
            $row = $m_sms->getRow(array(
                'uid' => $sms_id,
                'verify_code' => $sms_code
            ));
            if( !$row ){
                return new result(false,'Code error',null,errorCodesEnum::SMS_CODE_ERROR);
            }

        }

        $m_apply = new loan_applyModel();

        $apply = $m_apply->newRow();
        $apply->member_id = $member_id;
        $apply->applicant_name = $applicant_name;
        $apply->applicant_address = $applicant_address;
        $apply->apply_amount = $amount;
        $apply->currency = $currency;
        $apply->loan_time = $loan_time;
        $apply->loan_time_unit = $loan_time_unit;
        $apply->mortgage = $mortgage;
        $apply->loan_purpose = $propose;
        $apply->contact_phone = $contact_phone;
        $apply->apply_time = Now();
        $apply->request_source = loanApplySourceEnum::MEMBER_APP;
        $insert = $apply->insert();
        if( !$insert->STS ){
            return new result(false,'Apply fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$apply);

    }

    public function contractCancelOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $re = loan_baseClass::cancelContract($contract_id);
        return $re;
    }

    /** 还款请求
     * @return result
     */
    public function repaymentApplyOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = loan_contractClass::repaymentApply($params);
        return $re;
    }

    /** 还款请求取消
     * @return result
     */
    public function repaymentApplyCancelOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $m = new loan_request_repaymentModel();
        $request = $m->getRow($request_id);
        if( !$request ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $request->state != requestRepaymentStateEnum::CREATE ){
            return new result(false,'Handling...',null,errorCodesEnum::HANDLING_LOCKED);
        }
        $delete = $request->delete();
        if( !$delete->STS ){
            return new result(false,'Cancel fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }


    public function getContractPayableInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        if( $contract_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $re = loan_contractClass::getContractLeftPayableInfo($contract_id);
        return $re;
    }


    public function calculateContractPayOffDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        if( $contract_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $re = loan_contractClass::calculateContractPrepaymentOffAmount($contract_id);
        return $re;
    }

    public function prepaymentPreviewOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = loan_contractClass::prepaymentPreview($params);

        return $re;
    }

    public function prepaymentApplyOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = loan_contractClass::prepaymentApply($params);
        return $re;
    }


    public function prepaymentAddPaymentInfoOp()
    {
        return new result(false,'Give up using',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = loan_contractClass::prepaymentAddPaymentInfo($params);
        return $re;
    }


    public function getPrepaymentDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $m_contract = new loan_contractModel();
        $contract_info = $m_contract->getRow($contract_id);
        if( !$contract_info ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        // todo 计算方法不应该区分合同是否被支持的
        $re = loan_contractClass::getPrepaymentDetail($contract_id);
        if( !$re->STS ){
            return $re;
        }
        $data_return  = $re->DATA;

        $data = array(
            'contract_info' => $contract_info,
            'total_overdue_amount' => $data_return['total_overdue_amount'],
            'next_repayment_date' => $data_return['next_repayment_date'],
            'next_repayment_amount' => $data_return['next_repayment_amount'],
            'total_left_periods' => $data_return['total_left_periods'],
            'total_left_principal' => $data_return['total_left_principal'],
            'total_need_pay' => $data_return['total_need_pay']
        );

        $r = new ormReader();
        // 查询最近申请
        $request = loan_contractClass::getContractLastPrepaymentRequest($contract_id);
        // 过滤掉处理完成的，其他的需要展示给客户
        if( $request && $request['state'] == prepaymentApplyStateEnum::SUCCESS  ){
            $request = null;
        }
        if( $request ){
            // 计算申请的方式应还的总额
            $re = loan_contractClass::prepaymentPreview(array(
                'contract_id' => $request['contract_id'],
                'prepayment_type' => $request['prepayment_type'],
                'amount' => $request['principal_amount'],
                'repay_period' => $request['repay_period']
            ));

            $total_amount = round($re->DATA['total_prepayment_amount'],2);
            $request['amount'] = $total_amount;
            $apply_id = $request['uid'];
            $sql = "select * from loan_request_repayment where prepayment_apply_id='$apply_id' order by create_time desc ";
            $prepayment_payment_record = $r->getRows($sql);

        }else{
            $prepayment_payment_record = null;
        }


        $data['last_prepayment_request'] = $request;
        $data['prepayment_payment_record'] = $prepayment_payment_record;

        return new result(true,'success',$data);

    }

    public function getSchemaRepaymentDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $schema_id = $params['schema_id'];
        $list = loan_contractClass::getSchemaRepaymentDetail($schema_id);

        return new result(true,'success',$list);
    }


    public function getSchemaDisbursementDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $schema_id = $params['schema_id'];
        $list = loan_contractClass::getSchemaDisbursementDetail($schema_id);

        return new result(true,'success',$list);
    }


    public function loanApplyPreviewOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $amount = intval($params['amount']);
        $currency = currencyEnum::USD;
        $loan_month = intval($params['loan_time']);
        $loan_days = $loan_month*30;
        if( $amount <=0 || $loan_days <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 默认取信用贷的利率
        $credit_product = credit_loanClass::getProductInfo();
        if( !$credit_product ){
            return new result(false,'No alive product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $product_id = $credit_product['uid'];

        $r = new ormReader();
        // 首先匹配选中条件的
        $sql = "select * from loan_product_size_rate where product_id='$product_id' and loan_size_min<='$amount' 
and loan_size_max>='$amount' and currency='$currency' and min_term_days<='$loan_days' and max_term_days>='$loan_days' 
and interest_payment='".interestPaymentEnum::ANNUITY_SCHEME."' and interest_rate_period='".interestRatePeriodEnum::MONTHLY."' ";
        $rate = $r->getRow($sql);
        if( !$rate ){
            $sql = "select * from loan_product_size_rate where product_id='$product_id' order by loan_size_max desc,loan_size_min desc ";
            $rate = $r->getRow($sql);
            if( !$rate ){
                return new result(false,'No match rate',null,errorCodesEnum::NO_LOAN_INTEREST);
            }
        }

        $interest_rate = $rate['interest_rate'];
        $rate_re = loan_baseClass::interestRateConversion($interest_rate,$rate['interest_rate_unit'],interestRatePeriodEnum::MONTHLY);
        if( $rate_re->STS ){
            $interest_rate = $rate_re->DATA;
        }

        $operation_rate = $rate['operation_fee'];
        $operate_re = loan_baseClass::interestRateConversion($operation_rate,$rate['operation_fee_unit'],interestRatePeriodEnum::MONTHLY);
        if( $operate_re->STS ){
            $operation_rate = $operate_re->DATA;
            $rate['operation_fee'] = $operation_rate;
        }

        $total_rate = round($interest_rate+$operation_rate,2);

        $preview_re = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterest($amount,$interest_rate/100,$loan_month,$rate,$rate['interest_min_value']);
        $preview_data = $preview_re->DATA;
        $repayment_total = $preview_data['payment_total'];
        return new result(true,'success',array(
            'total_interest_rate' => $total_rate.'%',
            'total_repayment_amount' => $repayment_total['total_payment'],
            //'repayment_schema' => $preview_data['payment_schema']
        ));


    }





}