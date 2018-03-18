<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 10:38
 */
class credit_loanClass extends loan_baseClass
{

    protected static $product_info=null;


    /** 获取信用贷产品信息
     * @return bool|mixed|null
     */
    public static function getProductInfo()
    {
        if( self::$product_info == null ){
            $m_product = new loan_productModel();
            $product = $m_product->orderBy('uid desc')->getRow(array(
                'is_credit_loan' => 1,
                'state' => loanProductStateEnum::ACTIVE
            ));
            if( $product ){
                self::$product_info = $product;
            }
        }
        return self::$product_info;
    }

    public static function getCertLevelCalValue()
    {
        return array(
            certificationTypeEnum::ID => certTypeCalculateValueEnum::ID,
            certificationTypeEnum::FAIMILYBOOK => certTypeCalculateValueEnum::FAMILY_BOOK,
            certificationTypeEnum::PASSPORT => certTypeCalculateValueEnum::PASSPORT,
            certificationTypeEnum::HOUSE => certTypeCalculateValueEnum::HOUSE_CERT,
            certificationTypeEnum::CAR => certTypeCalculateValueEnum::CAR_CERT,
            certificationTypeEnum::WORK_CERTIFICATION => certTypeCalculateValueEnum::WORK_CERT,
            //certificationTypeEnum::CIVIL_SERVANT => certTypeCalculateValueEnum::CIVIL_SERVANT,
            //certificationTypeEnum::FAMILY_RELATIONSHIP => certTypeCalculateValueEnum::FAMILY_RELATION,
            certificationTypeEnum::LAND => certTypeCalculateValueEnum::LAND_CERT,
            certificationTypeEnum::RESIDENT_BOOK => certTypeCalculateValueEnum::RESIDENT_BOOK,
            certificationTypeEnum::MOTORBIKE => certTypeCalculateValueEnum::MOTORBIKE
        );
    }


    public static function addCreditLevel($params)
    {
        $level_type = intval($params['level_type']);
        $min_amount = intval($params['min_amount']);
        $max_amount = intval($params['max_amount']);
        $disburse_time = intval($params['disburse_time'])?:0;
        $time_unit = $params['disburse_time_unit']?:1;
        $cert_list = $params['cert_list'];

        if( $min_amount<0 || $max_amount<=0 ){
            return new result(false,'Amount Invalid',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $min_amount >= $max_amount ){
            return new result(false,'Amount Invalid',null,errorCodesEnum::INVALID_PARAM);
        }
        if( empty($cert_list) ){
            return new result(false,'Did not select certification',null,errorCodesEnum::INVALID_PARAM);
        }

        // 首先计算匹配值
        $cert_cal_value = self::getCertLevelCalValue();
        $sum = 0;
        foreach( $cert_list as $type ){
            $sum = $sum | $cert_cal_value[$type];
        }

        $m_level = new loan_credit_cert_levelModel();
        $m_cert = new loan_credit_level_cert_listModel();

        $level = $m_level->newRow();
        $level->level_type = $level_type;
        $level->match_value = $sum;
        $level->min_amount = $min_amount;
        $level->max_amount = $max_amount;
        $level->disburse_time = $disburse_time;
        $level->disburse_time_unit = $time_unit;
        $level->create_time = Now();
        $in = $level->insert();
        if( !$in->STS ){
            return new result(false,'Add level fail',null,errorCodesEnum::DB_ERROR);
        }

        // 组装sql
        $values_arr = array();
        reset($cert_list);
        foreach( $cert_list as $type ){
            $str = "('".$level->uid."','".$type."','".$cert_cal_value[$type]."')";
            $values_arr[] = $str;
        }

        $values = trim(join(',',$values_arr),',');
        $sql = "insert into loan_credit_level_cert_list(cert_level_id,cert_type,cal_value) values  ".$values;
        $in = $m_level->conn->execute($sql);
        if( !$in->STS ){
            return new result(false,'Add cert list fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }

    public static function editCreditLevel($params)
    {
        $uid = $params['uid'];
        $level_type = intval($params['level_type']);
        $min_amount = intval($params['min_amount']);
        $max_amount = intval($params['max_amount']);
        $disburse_time = intval($params['disburse_time'])?:0;
        $time_unit = $params['disburse_time_unit']?:1;
        $cert_list = $params['cert_list'];

        if( $min_amount<0 || $max_amount<=0 ){
            return new result(false,'Amount Invalid',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $min_amount >= $max_amount ){
            return new result(false,'Amount Invalid',null,errorCodesEnum::INVALID_PARAM);
        }
        if( empty($cert_list) ){
            return new result(false,'Did not select certification',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_level = new loan_credit_cert_levelModel();
        $level = $m_level->getRow($uid);
        if( !$level ){
            return new result(false,'No data',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        // 首先计算匹配值
        $cert_cal_value = self::getCertLevelCalValue();
        $sum = 0;
        foreach( $cert_list as $type ){
            $sum = $sum | $cert_cal_value[$type];
        }

        // 更新level
        $level->match_value = $sum;
        $level->level_type = $level_type;
        $level->min_amount = $min_amount;
        $level->max_amount = $max_amount;
        $level->disburse_time = $disburse_time;
        $level->disburse_time_unit = $time_unit;
        $level->update_time = Now();
        $up = $level->update();
        if( !$up->STS ){
            return new result(false,'Update level fail',null,errorCodesEnum::DB_ERROR);
        }

        // 删除原list
        $sql = "delete from loan_credit_level_cert_list where cert_level_id='$uid' ";
        $del = $m_level->conn->execute($sql);
        if( !$del->STS ){
            return new result(false,'Delete old lost fail',null,errorCodesEnum::DB_ERROR);
        }

        // 添加新list
        $values_arr = array();
        reset($cert_list);
        foreach( $cert_list as $type ){
            $str = "('".$level->uid."','".$type."','".$cert_cal_value[$type]."')";
            $values_arr[] = $str;
        }

        $values = trim(join(',',$values_arr),',');
        $sql = "insert into loan_credit_level_cert_list(cert_level_id,cert_type,cal_value) values  ".$values;
        $in = $m_level->conn->execute($sql);
        if( !$in->STS ){
            return new result(false,'Add cert list fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');
    }

    public static function getCreditLevelList($type='all')
    {
        $m_level_cert = new loan_credit_level_cert_listModel();
        if( $type == 'all' ){
            $sql = "select * from loan_credit_cert_level order by level_type asc,max_amount asc ";
        }else{
            $type = intval($type);
            $sql = "select * from loan_credit_cert_level where level_type='$type' order by level_type asc,max_amount asc ";
        }

        $level = $m_level_cert->reader->getRows($sql);
        $return = array();
        if( count($level) > 0 ){
            foreach( $level as $k=>$v ){
                $item = $v;
                $lists = $m_level_cert->select(array(
                    'cert_level_id' => $v['uid']
                ));
                $item['cert_list'] = array_column($lists,'cert_type');
                $return[] = $item;
            }
        }
        return $return;
    }

    public static function deleteCreditLevel($id)
    {
        $id = intval($id);
        if( !$id ){
            return new result(false,'Invalid param',null,errorCodesEnum::DB_ERROR);
        }
        $m_level = new loan_credit_cert_levelModel();
        $sql = "delete from loan_credit_cert_level where uid='$id'";
        $d = $m_level->conn->execute($sql);
        if( !$d->STS ){
            return new result(false,'Delete fail',null,errorCodesEnum::DB_ERROR);
        }
        $sql = "delete from loan_credit_level_cert_list where cert_level_id='$id'";
        $d = $m_level->conn->execute($sql);
        if( !$d->STS ){
            return new result(false,'Delete fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }



    public function creditLoanMemberCertDetail($params)
    {
        $product_info = self::getProductInfo();
        $product_id = $product_info?$product_info['uid']:0;
        $member_id = intval($params['member_id']);
        $re = memberClass::getMemberCertStateOrCount($member_id);

        if( !$re->STS ){
            return $re;
        }
        $cert_list = $re->DATA;

        return new result(true,'success',array(
            'product_id' => $product_id,
            'product_info' => $product_info,
            'cert_list' => $cert_list
        ));

    }


    public function getBindInsuranceProduct($params)
    {
        $product_id = intval($params['loan_product_id']);
        $re = parent::getLoanProductBindInsuranceProduct($product_id);
        if( !$re->STS ){
            return $re;
        }
        $data = $re->DATA;
        return new result(true,'success',$data);
    }


    /** 信用贷提现
     * @param $params
     * @return result
     */
    public function withdraw($params)
    {
        // 检查功能是否开启
        $valid = global_settingClass::isCanCreditLoanWithdraw();
        if( !$valid ){
            return new result(false,'Function closed',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        // 获得信用贷产品
        $product = self::getProductInfo();
        if( !$product ){
            return new result(false,'No release product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }
        $member_id = $params['member_id'];
        if( !$member_id ){
            return new result(false,'Invalid member',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Invalid member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if( !$loan_account ){
            $loan_account = $m_loan_account->newRow();
            $loan_account->obj_guid = $member->obj_guid;
            $loan_account->account_type = loanAccountTypeEnum::MEMBER;
            $loan_account->update_time = Now();
            $insert = $loan_account->insert();
            if( !$insert->STS ){
                return new result(false,'No loan account',null,errorCodesEnum::NO_LOAN_ACCOUNT);
            }
        }

        // 获取信用信息
        $credit_info = memberClass::getCreditBalance($member_id);
        $credit = $credit_info['credit'];
        $balance = $credit_info['balance'];

        $withdraw_amount = round($params['amount']);
        if( $withdraw_amount <= 0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::WITHDRAW_AMOUNT_INVALID);
        }
        if( $withdraw_amount > $credit ){
            return new result(false,'Out of credit',null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
        }

        // 超出信用余额
        if( $withdraw_amount > $balance ){
            return new result(false,'Out of credit balance',null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }

        // 超出单次额度
        $global_setting = global_settingClass::getCommonSetting();
        $single_limit = intval($global_setting['withdrawal_single_limit']);
        if( $single_limit > 0 && $withdraw_amount > $single_limit ){
            return new result(false,'Out of single limit',null,errorCodesEnum::OUT_OF_PER_WITHDRAW);
        }

        // todo 单日累计额度检查

        // 固定的取现合作银行  partner-asiaweiluy
        /*$handler = member_handlerClass::getMemberDefaultAceHandlerInfo($member_id);
        if( !$handler ){
            return new result(false,'Did not bind ACE account',null,errorCodesEnum::NO_BIND_ACE_ACCOUNT);
        }*/

        $loan_period = intval($params['loan_period']);
        $currency = currencyEnum::USD;
        $loan_days = $loan_period*30;
        // 组装合同参数
        $data = array(
            'member_id' => $member_id,
            'product_id' => $product['uid'],
            'amount' => $withdraw_amount,
            'currency' => $currency,
            'loan_period' => $loan_period,
            'loan_period_unit' => loanPeriodUnitEnum::MONTH,
            'repayment_type' => interestPaymentEnum::ANNUITY_SCHEME,
            'repayment_period' => interestRatePeriodEnum::MONTHLY,
            'handle_account_id' => 0
        );

        // 匹配利率信息
        $interest_re = self::getLoanInterestDetail($product->uid,$withdraw_amount,$currency,$loan_days,$data['repayment_type'],$data['repayment_period'],$member);
        if( !$interest_re->STS ){
            return $interest_re;
        }
        $interest_data = $interest_re->DATA;
        $interest_info = $interest_data['interest_info'];
        $size_rate_id = $interest_data['size_rate']?$interest_data['size_rate']['uid']:0;
        $special_rate_id = $interest_data['special_rate']?$interest_data['special_rate']['uid']:0;

        $interest_info['product_size_rate_id'] = $size_rate_id;
        $interest_info['product_special_rate_id'] = $special_rate_id;

        $p = array_merge((array)$params,$data);

        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = $this->createContract($p,$interest_info,true);  // 创建合同
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }
            $conn->submitTransaction();
            return $re;

        }catch ( Exception $e){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 合同确认，信用贷直接把合同状态更新为待放款
     * @param $contract_id
     * @param array $extent
     * @return result
     */
    public function confirmContract($contract_id,$extent=array())
    {

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow(array(
            'uid' => $contract_id
        ));

        if( !$contract ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            // 更新贷款合同状态
            $contract->state = loanContractStateEnum::PENDING_DISBURSE;
            $up = $contract->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update contract fail',null,errorCodesEnum::DB_ERROR);
            }


            // 更新绑定的保险合同状态 （贷款扣款）
            $sql = "update insurance_contract set state='".insuranceContractStateEnum::PROCESSING."' where loan_contract_id='$contract_id' ";
            $up = $conn->execute($sql);
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update insurance contract fail',null,errorCodesEnum::DB_ERROR);
            }

            $sql = "select m.* from loan_account a left join client_member m on a.obj_guid=m.obj_guid where a.uid='".$contract->account_id."' ";
            $member = $m_contract->reader->getRow($sql);
            $member_id = $member['uid'];

            // 信用贷款扣减信用余额
            // todo 默认的USD
            $re = member_creditClass::minusCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member_id,$contract->receivable_principal);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }


            // 向用户发送消息
            $title = 'Confirm Contract Success';
            $body = 'Your loan contract has come into force,just wait for distributing!';
            member_messageClass::sendSystemMessage($member_id,$title,$body);

            $conn->submitTransaction();
            return new result(true,'Confirm contract success');

        }catch (Exception $e ){
            $conn->rollback();
            return new result(false,'Unknown error: '.$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }


    public static function getCreditLevelByAmount($amount,$currency='USD')
    {
        $rate = currency::getRateBetween($currency,currency::USD);
        $amount = round($amount*$rate,2);
        $level_list = self::getCreditLevelList();

        $match_level = null;
        $max_level = null;
        $max_amount = 0;
        foreach( $level_list as $level ){

            // 最高等级的
            if( $level['max_amount'] >= $max_amount ){
                $max_level = $level;
                $max_amount = $level['max_amount'];
            }

            if( $amount>=$level['min_amount'] && $amount<=$level['max_amount'] ){
                $match_level = $level;
            }
        }
        return $match_level?:$max_level;
    }


    /** 获取客户信用贷全部待还计划
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberAllCreditLoanUncompletedSchemas($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $sql = "select s.* from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid left join loan_product p on p.uid=c.product_id 
        where c.account_id='$account_id' and  p.is_credit_loan='1' and  c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' and s.state!='".schemaStateTypeEnum::CANCEL."' and s.state!='".schemaStateTypeEnum::COMPLETE."' 
        order by s.receivable_date asc ";

        $schemas = $r->getRows($sql);
        return $schemas;
    }

    /** 获取客户信用贷下期应还计划
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberCreditLoanNextRepaymentSchema($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $sql = "select s.* from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid left join loan_product p on p.uid=c.product_id 
        where c.account_id='$account_id' and  p.is_credit_loan='1' and  c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' and s.state!='".schemaStateTypeEnum::CANCEL."' and s.state!='".schemaStateTypeEnum::COMPLETE."' 
        and s.receivable_date>='".date('Y-m-d')."'  order by s.receivable_date asc ";

        $schemas = $r->getRow($sql);
        return $schemas;
    }


    public static function getLoanMaxMonthByDefaultWay($product_id)
    {
        $r = new ormReader();
        $sql = "select min(min_term_days) min_days,max(max_term_days) max_days from loan_product_size_rate where 
         product_id='$product_id' and currency='".currencyEnum::USD."' and interest_payment='".interestPaymentEnum::ANNUITY_SCHEME."' and interest_rate_period='".interestRatePeriodEnum::MONTHLY."' ";
        $row = $r->getRow($sql);
        if( !$row ){
            return new result(false,'No set rate',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }
        $min_days = $row['min_days'];
        $max_days = $row['max_days'];

        $min_month = floor($min_days/30);
        $max_month = floor($max_days/30);

        if( $max_month < 1 ){
            return new result(false,'No set rate',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if( $min_month < 1 ){
            $min_month = 1;
        }

        return new result(true,'success',array(
            'min_month' => $min_month,
            'max_month' => $max_month
        ));
    }







}