<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/28
 * Time: 14:36
 */
class loan_contractClass
{
    public function __construct()
    {
    }


    /** 获得贷款合同详细信息
     * @param $contract_id
     * @return result
     */
    public static function getLoanContractDetailInfo($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_loan_contract = new loan_contractModel();
        $contract = $m_loan_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        $m_product = new loan_productModel();
        $m_rate = new loan_product_size_rateModel();
        $m_installment = new loan_installment_schemeModel();
        $m_distribute = new loan_disbursement_schemeModel();
        $m_special_rate = new loan_product_special_rateModel();

        $product = $m_product->getRow($contract->product_id);

        $interest_info = $contract;
        $size_interest = $m_rate->getRow($contract->product_sub_id);
        $special_rate = null;
        if( $contract['product_special_rate_id']) {
            $special_rate = $m_special_rate->getRow($contract['product_special_rate_id']);
        }

        $distribute_schema = $m_distribute->select(array(
            'contract_id' => $contract_id
        ));

        foreach( $distribute_schema as $k=>$v ){
            $item = $v;
            $item['disbursable_date'] = date('Y-m-d',strtotime($v['disbursable_date']));
            $distribute_schema[$k] = $item;
        }


        $installment_schema = $m_installment->select(array(
            'contract_id' => $contract_id,
            'state' => array('neq',schemaStateTypeEnum::CANCEL)
        ));

        foreach( $installment_schema as $k=> $v ){
            $item = $v;
            $item['receivable_date'] = date('Y-m-d',strtotime($v['receivable_date']));
            $item['penalty_start_date'] = date('Y-m-d',strtotime($v['penalty_start_date']));
            $item['penalty'] = round(loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']),2);
            $installment_schema[$k] = $item;
        }

        // 保险合同
        $sql = "select c.*,i.item_code,i.item_name from insurance_contract c left join insurance_product_item i on c.product_item_id=i.uid where 
             c.loan_contract_id='$contract_id' ";
        $insurances = $m_loan_contract->reader->getRows($sql);

        $due_type = '';
        if( $contract->repayment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            $due_type = 'once';
        }else{
            switch( $contract->repayment_period ){
                case interestRatePeriodEnum::YEARLY:
                case interestRatePeriodEnum::SEMI_YEARLY:
                case interestRatePeriodEnum::QUARTER:
                    $due_type = interestRatePeriodEnum::YEARLY;
                    break;
                case interestRatePeriodEnum::MONTHLY:
                    $due_type = interestRatePeriodEnum::MONTHLY;
                    break;
                case interestRatePeriodEnum::WEEKLY:
                    $due_type = interestRatePeriodEnum::WEEKLY;
                    break;
                case interestRatePeriodEnum::DAILY:
                    $due_type = interestRatePeriodEnum::DAILY;
                    break;
                default:
                    $due_type = '';
            }
        }

        // 是否可还款
        $is_can_repay = 0;
        if( $contract->state >= loanContractStateEnum::PENDING_DISBURSE
            && $contract->state < loanContractStateEnum::COMPLETE
        ){
            $is_can_repay = 1;
        }


        $return = array(
            'contract_id' => $contract->uid,
            'is_can_repay' => $is_can_repay,
            'loan_amount' => $contract->apply_amount,
            'currency' => $contract->currency,
            'loan_period_value' => $contract->loan_period_value,
            'loan_period_unit' => $contract->loan_period_unit,
            'repayment_type' => $contract->repayment_type,
            'repayment_period' => $contract->repayment_period,
            'due_date' => $contract->due_date,
            'due_date_type' => $due_type,
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'total_admin_fee' => $contract->receivable_admin_fee,
            'total_loan_fee' => $contract->receivable_loan_fee,
            'total_insurance_fee' => $contract->receivable_insurance_fee,
            'total_operation_fee' => $contract->receivable_operation_fee,
            'total_interest' => $contract->receivable_interest,
            'actual_receive_amount' => $contract->apply_amount-$contract->receivable_admin_fee-$contract->receivable_loan_fee-$contract->receivable_insurance_fee,
            'total_repayment' => $contract->receivable_principal+$contract->receivable_interest+$contract->receivable_operation_fee,
            'lending_time' => $contract->create_time,
            'loan_product_info' => $product,
            'interest_info' => $interest_info, // 实际计算利率
            'size_rate' => $size_interest,
            'special_rate' => $special_rate?:null,
            'contract_info' => $contract,
            'loan_disbursement_scheme' => $distribute_schema,
            'loan_installment_scheme' => $installment_schema,
            'bind_insurance' => $insurances
        );

        return new result(true,'success',$return);

    }


    /** 获取合同未还清计划
     * @param $contract_id
     * @return ormCollection
     */
    public static function getContractUncompletedSchemas($contract_id)
    {
        $contract_id = intval($contract_id);
        $r = new ormReader();
        $sql = "select * from loan_installment_scheme where contract_id='$contract_id' and state!='".schemaStateTypeEnum::CANCEL."' 
        and state!='".schemaStateTypeEnum::COMPLETE."' order by receivable_date asc ";
        $rows = $r->getRows($sql);
        return $rows;
    }


    /** 合同是否全部还清
     * @param $contract_id
     * @return bool
     */
    public static function contractIsPaidOff($contract_id)
    {
        $contract_id = intval($contract_id);
        $r = new ormReader();
        $sql = "select count(*) from loan_installment_scheme where contract_id='$contract_id' and state!='".schemaStateTypeEnum::CANCEL."' and state!='".schemaStateTypeEnum::COMPLETE."' ";
        $count = $r->getOne($sql);
        if( $count <= 0 ){
            return true;
        }
        return false;
    }


    /** 获取贷款合同的利率信息
     * @param $contract_id
     * @return result
     */
    public static function getContractInterestInfo($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->
        find(array(
            'uid' => $contract_id
        ));
        if( !$contract ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        $interest_info = $contract;
        $interest_info['interest_payment'] = $contract['repayment_type'];
        $interest_info['interest_rate_period'] = $contract['repayment_period'];

        return new result(true,'success',$interest_info);





        /*$m_rate = new loan_product_size_rateModel();
        $interest_rate = $m_rate->find(array(
            'uid' => $contract->product_sub_id
        ));
        if( !$interest_rate ){
            return new result(false,'System error',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        if ($contract->product_special_rate_id) {

            $m_special_rate = new loan_product_special_rateModel();
            $special_rate = $m_special_rate->getRow($contract->product_special_rate_id);
            if( $special_rate ){
                $special_rate = $special_rate->toArray();
                unset($special_rate['uid']);
                $interest_rate = array_merge((array)$interest_rate,(array)$special_rate);
            }
        }*/

    }


    /** 获取计划的详细还款明细
     * @param $schema_id
     * @return ormCollection
     */
    public static function getSchemaRepaymentDetail($schema_id)
    {
        $r = new ormReader();
        $sql = "select * from loan_repayment where scheme_id='$schema_id' order by uid desc ";
        $list = $r->getRows($sql);
        return $list;
    }

    /** 获取计划的详细放款明细
     * @param $schema_id
     * @return ormCollection
     */
    public static function getSchemaDisbursementDetail($schema_id)
    {
        $r = new ormReader();
        $sql = "select * from loan_disbursement where scheme_id='$schema_id' order by uid desc ";
        $list = $r->getRows($sql);
        return $list;
    }


    /** 获取合同剩余应还信息
     * @param $contract_id
     * @return result
     */
    public static function getContractLeftPayableInfo($contract_id)
    {
        $today = date('Y-m-d');
        $current_schema = null;
        $left_schemas = self::getContractUncompletedSchemas($contract_id);
        // 计算欠款
        $total_penalty = $total_amount = $overdue_amount = 0;
        foreach( $left_schemas as $v ){

            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $amount = $v['amount']-$v['actual_payment_amount'] + $penalty;

            $v['left_amount'] = $amount;

            if( $v['receivable_date'] < $today ){
                $overdue_amount += $v['amount']-$v['actual_payment_amount'];  // 逾期本息
            }

            if( !$current_schema ){
                if( $v['receivable_date'] >= $today ){
                    $current_schema = $v;
                }
            }

            $total_penalty += $penalty;
            $total_amount += $amount;
        }

        if( $current_schema ){
            $next_repayment_date = date('Y-m-d',strtotime($current_schema['receivable_date']) );
            $next_repayment_amount = $current_schema['left_amount'];
        }else{
            $next_repayment_date = null;
            $next_repayment_amount = 0;
        }

        // 是否存在未处理完的请求
        $m_request = new loan_request_repaymentModel();
        $last_request_repayment_info = $m_request->orderBy('uid desc')->getRow(array(
            'contract_id' => $contract_id,
        ));
        $has_request = null;
        if( $last_request_repayment_info && $last_request_repayment_info->state != requestRepaymentStateEnum::SUCCESS ){
            $has_request = $last_request_repayment_info;
        }

        return new result(true,'success',array(
            'next_repayment_date' => $next_repayment_date,
            'next_repayment_amount' => $next_repayment_amount,
            'total_overdue_penalty' => $total_penalty,
            'total_overdue_amount' => $overdue_amount,
            'total_payable_amount' => $total_amount,
            'last_request_repayment_info' => $has_request
        ));
    }


    /** 还款申请
     * @param $params
     * @return result
     */
    public static function repaymentApply($params)
    {

        if( $params['request_id'] ){

            // 提前还款
            $type = requestRepaymentTypeEnum::BALANCE;
            $request_id = $params['request_id'];
            $m_apply = new loan_prepayment_applyModel();
            $apply = $m_apply->getRow($request_id);
            if( !$m_apply ){
                return new result(false,'No apply',null,errorCodesEnum::INVALID_PARAM);
            }

            $apply->state = prepaymentApplyStateEnum::PAID;
            $apply->update_time = Now();
            $up = $apply->update();
            if( !$up->STS ){
                return new result(false,'Add fail',null,errorCodesEnum::DB_ERROR);
            }
            $contract_id = $apply->contract_id;

        }else{

            // 正常还款
            $type = requestRepaymentTypeEnum::SCHEME;
            $contract_id = intval($params['contract_id']);
            $m_contract = new loan_contractModel();
            $contract = $m_contract->getRow($contract_id);
            if( !$contract ){
                return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
            }

            if( $contract->state < loanContractStateEnum::PENDING_DISBURSE ){
                return new result(false,'Contract did not execute',null,errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
            }

        }


        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $repayment_way = $params['repayment_way'];

        // 插入记录
        $m_request = new loan_request_repaymentModel();
        $request = $m_request->newRow();
        $request->contract_id = $contract_id;
        $request->type = $type;
        $request->amount = $amount;
        $request->currency = $currency;
        $request->request_remark = $params['remark'];

        if( !empty($_FILES['receipt_image']) ){

            $default_dir = 'loan/receipt';
            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path',null);
            $upload->set('default_dir',$default_dir);
            $re = $upload->server2upun('receipt_image');
            if( $re == false ){
                return new result(false,'Upload image fail',null,errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->full_path;
            unset($upload);
            $request->request_img = $img_path;
        }

        if( $repayment_way == repaymentWayEnum::BANK_TRANSFER  ){

            // 线下转账 bank
            $request->repayment_way = repaymentWayEnum::BANK_TRANSFER;
            $request->payer_id = 0;
            $request->payer_type = memberAccountHandlerTypeEnum::BANK;
            $request->payer_name = $params['name'];
            $country_code = $params['country_code'];
            $phone = $params['phone'];
            if( $phone ){
                $phone_arr = tools::getFormatPhone($country_code,$phone);
                $request->payer_phone = $phone_arr['contact_phone'];
            }
            $request->payer_account = $params['account'];

            $company_account_id = intval($params['company_account_id']);
            $m_bank_account = new site_bankModel();
            $bank_account = $m_bank_account->getRow($company_account_id);
            if( !$bank_account ){
                return new result(false,'Invalid bank account',null,errorCodesEnum::UNEXPECTED_DATA);
            }

            $request->bank_id = $company_account_id;
            $request->bank_code = $bank_account->bank_code;
            $request->bank_name = $bank_account->bank_name;
            $request->bank_account_no = $bank_account->bank_account_no;
            $request->bank_account_name = $bank_account->bank_account_name;

        }else{

            // 绑定的账户扣款 bank+passbook
            // todo 增加的其他方式
            $request->repayment_way = repaymentWayEnum::AUTO_DEDUCTION;

            $handler_id = intval($params['handler_id']);
            $m_handler = new member_account_handlerModel();
            $handler_info = $m_handler->getRow($handler_id);
            if( !$handler_info ){
                return new result(false,'Invalid handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
            }
            $request->payer_id = $handler_info->uid;
            $request->payer_type = $handler_info->handler_type;
            $request->payer_name = $handler_info->handler_name;
            $request->payer_phone = $handler_info->handler_phone;
            $request->payer_account = $handler_info->handler_account;

        }

        $request->create_time = Now();
        $request->state = requestRepaymentStateEnum::CREATE;
        $insert = $request->insert();
        if( !$insert->STS ){
            return new result(false,'Apply fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$request);

    }

    /** 获取可分配还款schema
     * @param $contract_id
     * @param $amount
     * @return result
     */
    public static function getRepaymentSchemaByAmount($contract_id, $amount,$currency, $penalty_end_date = null)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 汇率
        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract->currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'Not set currency exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }
        $amount = round($amount*$exchange_rate, 2);
        $repayment_amount = $amount;

        $return = array();

        // 剩余未还清计划
        $lists = self::getContractUncompletedSchemas($contract_id);

        if (count($lists) < 1) {
            return new result(true, 'All payed');
        }

        $today = date('Y-m-d 00:00:00');
        $receivable_amount = $total_amount = $amount;
        if (!$penalty_end_date) {
            $penalty_end_date = date('Y-m-d');
        }

        // 只考虑能还清的情况，剩余的钱放在储蓄账户中
        $left_amount = $amount;

        foreach ($lists as $schema) {


            if( $left_amount > 0 ){

                $schema['penalties'] = loan_baseClass::calculateSchemaRepaymentPenalties($schema['uid'], $penalty_end_date);
                $ap_amount = $schema['amount']-$schema['actual_payment_amount'];
                $ap_penalty = $schema['penalties'];
                $need_pay = $ap_amount+$ap_penalty;

                if( $left_amount >= $need_pay ){
                    $schema['ap_amount'] = $ap_amount;
                    $schema['ap_penalty'] = $ap_penalty;
                    $return[] = $schema;
                    $left_amount -= $need_pay;
                }

            }

        }


        return new result(true, 'success', array(
            'repayment_amount_balance' => $left_amount,
            'repayment_schema' => $return
        ));

    }


    /** 获取可分配还款schema
     * @param $contract_id
     * @param $amount
     * @return result
     */
    public static function getRepaymentSchemaByAmount_old($contract_id, $amount,$currency, $penalty_end_date = null)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 汇率
        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract->currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'Not set currency exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }
        $amount = round($amount*$exchange_rate, 2);
        $repayment_amount = $amount;

        $return = array();

        // 剩余未还清计划
        $lists = self::getContractUncompletedSchemas($contract_id);

        if (count($lists) < 1) {
            return new result(true, 'All payed');
        }

        $today = date('Y-m-d 00:00:00');
        $receivable_amount = $total_amount = $amount;
        if (!$penalty_end_date) {
            $penalty_end_date = date('Y-m-d');
        }

        foreach ($lists as $schema) {

            $schema['penalties'] = loan_baseClass::calculateSchemaRepaymentPenalties($schema['uid'], $penalty_end_date);

            $schema['cal_payment_amount'] = $schema['actual_payment_amount'];  // 计算用，不更改实际数据
            $schema['cal_paid_penalty'] = $schema['paid_penalty'];            // 计算用，不更改实际数据

            // 优先还应还时间内的本息，有剩余再还罚金
            if ($schema['receivable_date'] <= $today) {

                if ($receivable_amount > 0) {  // 还有剩余本息
                    $return[] = $schema;
                } else {
                    break;
                }

            } else {

                if ($total_amount > 0) {  // 还完本息+罚金还有剩余
                    $return[] = $schema;
                } else {
                    break;
                }
            }

            $receivable_amount = $receivable_amount + $schema['actual_payment_amount'] - $schema['amount'];
            $total_amount = $total_amount + $schema['actual_payment_amount'] + $schema['paid_penalty'] - $schema['amount'] - $schema['penalties'];

        }


        // 1. 先为还款分配本息
        reset($return);
        foreach ($return as $k => $v) {

            if ($v['receivable_date'] <= $today) {
                if ($repayment_amount > 0) {
                    $paid = $v['amount'] - $v['cal_payment_amount']; // 实际应还本息
                    $v['ap_amount'] = ($repayment_amount >= $paid) ? $paid : $repayment_amount;

                    $v['cal_payment_amount'] += $v['ap_amount'];
                    $repayment_amount = $repayment_amount - $v['ap_amount'];

                    $return[$k] = $v;
                } else {
                    break;
                }
            }

        }

        // 2.为还款分配罚金(有超出继续叠加到本金)
        reset($return);
        foreach ($return as $k => $v) {


            if ($repayment_amount > 0) {
                $paid = $v['penalties'] - $v['cal_paid_penalty']; // 实际还的罚金
                $v['ap_penalty'] = ($repayment_amount >= $paid) ? $paid : $repayment_amount;
                $v['cal_paid_penalty'] += $v['ap_penalty'];
                $repayment_amount = $repayment_amount - $v['ap_penalty'];

                if ($repayment_amount > 0) {
                    // 再回头分配本息(未到还款期限的计划)
                    $left_receivable = $v['amount'] - $v['cal_payment_amount'];
                    if ($left_receivable > 0) {
                        $ap_amount = ($repayment_amount >= $left_receivable) ? $left_receivable : $repayment_amount;
                        $v['ap_amount'] += $ap_amount;
                        $v['cal_payment_amount'] += $v['ap_amount'];
                        $repayment_amount = $repayment_amount - $ap_amount;
                    }
                }

                $return[$k] = $v;
            } else {
                break;
            }

        }

        // todo 还完合同还有剩余钱怎么处理？
        reset($return);

        return new result(true, 'success', array(
            'repayment_amount_balance' => $repayment_amount,
            'repayment_schema' => $return
        ));
    }


    /** 提前还款预览详情
     * @param $contract_id
     * @return result
     */
    public static function getPrepaymentDetail($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        if( $contract->state < loanContractStateEnum::PENDING_DISBURSE ){
            return new result(false,'Non execute contract',null,errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }

        $re = self::getContractInterestInfo($contract_id);
        if( !$re->STS ){
            return $re;
        }
        $interest_info = $re->DATA;


        // todo  一次性还款是否支持??
        if( $contract['repayment_type'] == interestPaymentEnum::SINGLE_REPAYMENT ){
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }

        // 全息
        if ($contract['is_full_interest'] == 1) {
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }


        $today = date('Y-m-d');
        $left_schema = self::getContractUncompletedSchemas($contract_id);

        $overdue_schema = $next_schema = $last_schema = array();

        $temp_schema = array();
        // 分割逾期的
        foreach( $left_schema as $v ){
            if( $v['receivable_date'] < $today ){
                $overdue_schema[] = $v;
            }else{
                $temp_schema[] = $v;
            }
        }

        // 当还的(就是算利息的)

        $next_repayment_date = null;
        if( $temp_schema[0] ){
            $next1 = $temp_schema[0];

            // 是否提前还清了
            // 最近应还一期的还款日期
            $sql = "select * from loan_installment_scheme where contract_id='$contract_id' and receivable_date>='$today' and 
            state!='".schemaStateTypeEnum::CANCEL."' order by receivable_date asc ";
            $lately = $m_contract->reader->getRow($sql);

            if( $next1['receivable_date'] > $lately['receivable_date'] ){
                    // 已还，没有必还本息
            }else{

                $next_schema[] = $next1;
                unset($temp_schema[0]);
                // 判断是否差异5天以上
                $next_day = date('Y-m-d',strtotime($next1['receivable_date']));
                $next_repayment_date = $next_day;
                $seconds = strtotime($next_day) - strtotime($today);
                if( ceil( $seconds/86400 ) < 5 ){

                    if( $temp_schema[1] ){
                        $next2 = $temp_schema[1];
                        $next_schema[] = $next2;
                        $next_repayment_date = date('Y-m-d',strtotime($next2['receivable_date']));
                        unset($temp_schema[1]);
                    }

                }

            }



        }

        if( count($temp_schema) >=1 ){
            $last_schema = array_values($temp_schema);  // 重置key
        }

        //统计
        $total_overdue = $total_next_repay = $total_left_principal = 0;
        $need_pay_principal = 0;
        $need_pay_penalty = 0;

        foreach( $overdue_schema as $v ){
            $v['penalty'] = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $total_overdue += $v['amount']-$v['actual_payment_amount'] + $v['penalty'];
            $need_pay_penalty += $v['penalty'];

            if( $v['actual_payment_amount'] >= $v['receivable_principal'] ){
                $principal = 0;
            }else{
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $need_pay_principal += $principal;
        }

        foreach( $next_schema as $v ){
            $total_next_repay += $v['amount']-$v['actual_payment_amount'];

            if( $v['actual_payment_amount'] >= $v['receivable_principal'] ){
                $principal = 0;
            }else{
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $need_pay_principal += $principal;

        }

        $need_pay_total = $total_overdue+$total_next_repay;
        $need_pay_interest = $need_pay_total-$need_pay_principal-$need_pay_penalty;


        if( empty($last_schema) ){
            $total_left_periods = 0;
        }else{
            $total_left_periods = count($last_schema);
        }

        foreach( $last_schema as $k=>$v ){

            if( $v['actual_payment_amount'] >= $v['receivable_principal'] ){
                $principal = 0;
            }else{
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $v['remaining_principal'] = $principal;
            $total_left_principal += $principal;

            $last_schema[$k] = $v;
        }



        return new result(true,'success',array(
            'total_overdue_amount' => $total_overdue,
            'next_repayment_date' => $next_repayment_date,
            'next_repayment_amount' => $total_next_repay,
            'total_left_principal' => $total_left_principal,
            'total_left_periods' => $total_left_periods,
            'total_need_pay' => array(
                'total' => $need_pay_total,
                'principal' => $need_pay_principal,
                'interest' => $need_pay_interest,
                'penalty' => $need_pay_penalty
            ),
            'schema_detail' => array(
                'overdue_schema' => $overdue_schema,
                'next_schema' => $next_schema,
                'last_schema' => $last_schema
            ),
            'contract_detail' => array(
                'contract_info' => $contract,
                'interest_detail' => $interest_info
            )
        ));

    }


    /** 获取最近的合同提前还款申请
     * @param $contract_id
     * @return null
     */
    public static function getContractLastPrepaymentRequest($contract_id)
    {
        $request = null;
        // 新申请
        $r = new ormReader();
        $sql = "select * from loan_prepayment_apply where contract_id='$contract_id'  order by uid desc ";
        $new = $r->getRow($sql);
        $request = $new?:null;

        return $request;
    }




    /** 计算实际还款本金金额还款计划和新的还款计划
     * @param $contract_id
     * @param $amount
     * @param $remain_schemas
     * @return result
     */
    public static function getPrepaymentNewSchemaByPaidPrincipal($contract_id,$amount,$remain_schemas)
    {
        $amount = round($amount,2);
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $re = self::getContractInterestInfo($contract_id);
        if( !$re->STS ){
            return $re;
        }
        $interest_info = $re->DATA;

        // 一次性还款
        if( $contract['repayment_type'] == interestPaymentEnum::SINGLE_REPAYMENT ){
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }

        // 全息
        if ($contract['is_full_interest'] == 1) {
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }

        // 分配本金
        if ($contract['prepayment_interest_type'] == 1) {
            $commission = round($contract['prepayment_interest'],2);
            $principal = $amount-$commission;
        } else {
            $rate = $contract['prepayment_interest'] / 100;
            $principal = round( $amount/(1+$rate),2 );
            $commission = $amount-$principal;
        }

        $detail_re = self::getPrepaymentDetail($contract_id);
        if( !$detail_re->STS ){
            return $detail_re;
        }

        /*$data = $detail_re->DATA;
        $remain_schema = $data['schema_detail']['last_schema'];
        $total_remain_principal = $data['total_left_principal'];*/

        $remain_schema = array();
        $total_remain_principal = 0;
        foreach( $remain_schemas as $k=>$v ){

            if( $v['actual_payment_amount'] >= $v['receivable_principal'] ){
                $v['remaining_principal'] = 0;
            }else{
                $v['remaining_principal'] = $v['receivable_principal']-$v['actual_payment_amount'];
            }
            $total_remain_principal = $v['remaining_principal'];
            $remain_schema[] = $v;
        }



        $left_schema = array();
        $allot_principal = $principal;
        foreach( $remain_schema as $v ){

            if( $allot_principal > 0 ){

                if( $allot_principal > $v['remaining_principal'] ){
                    $allot_principal -= $v['remaining_principal'];
                }else{

                    $v['remaining_principal'] = $v['remaining_principal']-$allot_principal;
                    $allot_principal = 0;
                    $left_schema[] = $v;
                }

            }else{
                $left_schema[] = $v;
            }

        }

        $left_principal = $total_remain_principal-$principal;
        if( $left_principal <= 0 ){
            $left_principal = 0;
        }



        if( $left_principal > 0 ){

            // 重新生成计划
            $left_schema_num = count($left_schema);
            if( $left_schema_num > 0 ){

                $new_interest = $interest_info;

                $end_time = strtotime( date('Y-m-d',strtotime($contract->end_date) ) );
                $today_time = strtotime( date('Y-m-d') );
                $loan_days = ceil( ( $end_time- $today_time)/86400 );

                if( $new_interest['interest_payment'] == interestPaymentEnum::SINGLE_REPAYMENT ){
                    // 一次还款累计到日计算利率
                    $interest_rt = loan_baseClass::interestRateConversion($new_interest['interest_rate'],$new_interest['interest_rate_unit'],interestRatePeriodEnum::DAILY);
                    if( !$interest_rt->STS ){
                        return $interest_rt;
                    }
                    $interest_rate = $interest_rt->DATA;
                    $operator_fee_rt = loan_baseClass::interestRateConversion($new_interest['operation_fee'],$new_interest['operation_fee_unit'],interestRatePeriodEnum::DAILY);
                    if( !$operator_fee_rt->STS ){
                        return $operator_fee_rt;
                    }

                    $operator_fee = $operator_fee_rt->DATA;
                    $new_interest['interest_rate'] = $interest_rate*$loan_days;  // 单利
                    $new_interest['operation_fee'] = $operator_fee*$loan_days;

                }else{

                    $interest_rt = loan_baseClass::interestRateConversion($new_interest['interest_rate'],$new_interest['interest_rate_unit'],$new_interest['interest_rate_period']);
                    if( !$interest_rt->STS ){
                        return $interest_rt;
                    }
                    $new_interest_rate = $interest_rt->DATA;

                    $operator_fee_rt = loan_baseClass::interestRateConversion($new_interest['operation_fee'],$new_interest['operation_fee_unit'],$new_interest['interest_rate_period']);
                    if( !$operator_fee_rt->STS ){
                        return $operator_fee_rt;
                    }
                    $new_operator_fee = $operator_fee_rt->DATA;
                    $new_interest['interest_rate'] = $new_interest_rate;
                    $new_interest['operation_fee'] = $new_operator_fee;
                }


                $new_loan_amount = $left_principal;
                $loan_base = new loan_baseClass();

                $re = $loan_base->getRepaymentSchemaOfAllType($new_interest['interest_payment'],$new_loan_amount,$loan_days,$new_interest,$left_schema_num);

                if( !$re->STS ){
                    return $re;
                }

                $data = $re->DATA;
                $payment_schema = $data['payment_schema'];

                foreach( $left_schema as $k=>$v ){
                    $temp = $v;
                    $temp['receivable_principal'] = $payment_schema[$k]['receivable_principal'];
                    $temp['receivable_interest'] = $payment_schema[$k]['receivable_interest'];
                    $temp['receivable_operation_fee'] = $payment_schema[$k]['receivable_operation_fee'];
                    $temp['amount'] = $payment_schema[$k]['amount'];
                    $temp['actual_payment_amount'] = 0;
                    $left_schema[$k] = $temp;
                }

            }

        }


        $return_left_period = $left_schema;

        return new result(true,'success',array(
            'paid_principal' => $principal,
            'paid_commission' => $commission,
            'left_principal' => $left_principal,
            'new_schema' => $return_left_period
        ));


    }


    /** 提前还款申请
     * @param $params
     * @return result
     */
    public static function prepaymentApply($params)
    {

        $contract_id = $params['contract_id'];
        $prepayment_type = intval($params['prepayment_type']);
        $repay_period = intval($params['repay_period']);

        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if( $contract->state < loanContractStateEnum::PENDING_DISBURSE ){
            return new result(false,'Invalid contract state',null,errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }

        if( $contract->sate == loanContractStateEnum::COMPLETE ){
            return new result(false,'Contract have been paid off!',null,errorCodesEnum::CONTRACT_BEEN_PAID_OFF);
        }

        if( $contract->sate == loanContractStateEnum::WRITE_OFF ){
            return new result(false,'Contract have been written off!',null,errorCodesEnum::CONTRACT_BEEN_WRITTEN_OFF);
        }

        $interest_re = self::getContractInterestInfo($contract_id);
        if( !$interest_re->STS ){
            return $interest_re;
        }

        $interest_info = $interest_re->DATA;

        // 一次性还款
        if( $interest_info['interest_payment'] == interestPaymentEnum::SINGLE_REPAYMENT ){
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }

        // 全息
        if ($interest_info['is_full_interest'] == 1) {
            return new result(false,'Not support',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }


        $p_re = self::prepaymentPreview($params);
        if( !$p_re->STS ){
            return $p_re;
        }
        $p_detail = $p_re->DATA;
        $need_pay = $p_detail['need_pay'];

        $prepayment_principal = $p_detail['prepayment_principal'];
        $prepayment_fee = $p_detail['prepayment_fee'];

        $total_amount = $need_pay['total_overdue_amount']+$need_pay['next_repayment_amount']+$prepayment_principal+$prepayment_fee;

        $m_apply = new loan_prepayment_applyModel();
        $request = $m_apply->newRow();
        $request->contract_id = $contract_id;
        $request->amount = $prepayment_principal+$prepayment_fee;
        $request->principal_amount = $prepayment_principal;
        $request->fee_amount = $prepayment_fee;
        $request->total_apply_amount = $total_amount;
        $request->currency = $contract->currency;
        $request->prepayment_type = $prepayment_type;
        $request->repay_period = $repay_period;
        $request->apply_time = Now();
        $in = $request->insert();

        if( !$in->STS ){
            return new result(false,'DB error',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$request);

    }



    /** 提前还款分方式应还金额详情
     *  如部分还款，全额还款等
     * @param $params
     * @return result
     */
    public static  function prepaymentPreview($params)
    {
        $contract_id = $params['contract_id'];
        $prepayment_type = intval($params['prepayment_type']);
        $repay_period = intval($params['repay_period']);

        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }
        if( $contract->state < loanContractStateEnum::PENDING_DISBURSE ){
            return new result(false,'Invalid contract state',null,errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }


        $detail = self::getPrepaymentDetail($contract_id);
        if( !$detail->STS ){
            return $detail;
        }
        $data_return = $detail->DATA;


        $total_need_pay = $data_return['total_overdue_amount']+$data_return['next_repayment_amount'];

        $data = $data_return;
        unset($data['schema_detail']);
        unset($data['contract_detail']);


        switch( $prepayment_type ){
            case prepaymentRequestTypeEnum::PARTLY:
                $amount = $params['amount'];
                if( $amount <= 0 ){
                    return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
                }

                $re = self::getContractPrepaymentDetailByAmount($contract_id,$amount);
                if( !$re->STS ){
                    return $re;
                }
                $d = $re->DATA;
                $prepayment_principal = $d['prepayment_principal'];
                $prepayment_fee = $d['prepayment_fee'];
                $left_schema = $d['left_schema'];
                break;
            case prepaymentRequestTypeEnum::FULL_AMOUNT :
                $re = self::calculateContractPrepaymentOffAmount($contract_id);
                if( !$re->STS ){
                    return $re;
                }
                $d = $re->DATA;
                $prepayment_principal = $d['prepayment_principal'];
                $prepayment_fee = $d['prepayment_fee'];
                $left_schema = $d['left_schema'];
                break;
            case prepaymentRequestTypeEnum::LEFT_PERIOD:
                if( $repay_period < 0 ){
                    return new result(false,'Invalid period',null,errorCodesEnum::INVALID_PERIOD_NUM);
                }
                $re = self::getPrepaymentDetailByRepayPeriod($contract_id,$repay_period);
                if( !$re->STS ){
                    return $re;
                }
                $d = $re->DATA;
                $prepayment_principal = $d['prepayment_principal'];
                $prepayment_fee = $d['prepayment_fee'];
                $left_schema = $d['left_schema'];
                break;
            default:
                return new result(false,'Un supported type',null,errorCodesEnum::NOT_SUPPORTED);
                break;
        }

        $total_prepayment_amount = $total_need_pay+$prepayment_principal+$prepayment_fee;

        return new result(true,'success',array(
            'need_pay' => $data,
            'prepayment_principal' => $prepayment_principal,
            'prepayment_fee' => $prepayment_fee,
            'total_prepayment_amount' => $total_prepayment_amount,
            'left_schema' => $left_schema
        ));
    }


    /** 全额提前还款详细
     * @param $contract_id
     * @return result
     */
    public static function calculateContractPrepaymentOffAmount($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }
        $detail_re = self::getPrepaymentDetail($contract_id);
        if( !$detail_re->STS ){
            return $detail_re;
        }

        $data = $detail_re->DATA;
        $need_pay = $data['total_need_pay'];
        $contract_detail = $data['contract_detail'];
        $interest_detail = $contract_detail['interest_detail'];
        $remain_schema = $data['schema_detail']['last_schema'];
        $total_remain_principal = $data['total_left_principal'];

        // 计算手续费
        if ($interest_detail['prepayment_interest_type'] == 1) {
            $commission = $interest_detail['prepayment_interest'];
        } else {
            $commission = round(($total_remain_principal) * $interest_detail['prepayment_interest'] / 100, 2);
        }

        $total_paid_principal = $need_pay['principal']+$total_remain_principal;
        $total_paid_interest = $need_pay['interest'];
        $total_paid_penalty = $need_pay['penalty'];
        $total_paid_commission = $commission;
        $total_paid = $total_paid_principal+$total_paid_interest+$total_paid_penalty+$total_paid_commission;

        return new result(true, 'success', array(

            'total_prepayment_amount' => $total_paid,
            'currency' => $contract->currency,
            'total_paid_principal' => $total_paid_principal,
            'total_paid_interest' => $total_paid_interest,
            'total_paid_penalty' => $total_paid_penalty,
            'total_paid_commission' => $total_paid_commission,
            'prepayment_principal' => $total_remain_principal,
            'prepayment_fee' => $total_paid_commission,
            'left_schema' => null

        ));

    }


    /** 计算部分提前还款详细
     * @param $contract_id
     * @param $amount
     * @param $currency
     * @return result
     */
    public static function getContractPrepaymentDetailByAmount($contract_id,$amount)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $new_amount = round($amount,2);
        if( $new_amount <=0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $detail_re = self::getPrepaymentDetail($contract_id);
        if( !$detail_re->STS ){
            return $detail_re;
        }

        $data = $detail_re->DATA;
        $need_pay = $data['total_need_pay'];
        $contract_detail = $data['contract_detail'];
        $interest_detail = $contract_detail['interest_detail'];
        $remain_schema = $data['schema_detail']['last_schema'];
        $total_remain_principal = $data['total_left_principal'];


        $left_schema = array();
        $paid_principal = 0;
        $cal_amount = $new_amount;
        foreach( $remain_schema as $v ){

            if( $cal_amount > 0 ){

                if( $cal_amount > $v['remaining_principal'] ){
                    $paid_principal += $v['remaining_principal'];
                    $cal_amount -= $v['remaining_principal'];
                }else{
                    $paid_principal += $cal_amount;
                    $cal_amount = 0;
                    $v['remaining_principal'] = $v['remaining_principal']-$cal_amount;
                    $left_schema[] = $v;
                }

            }else{
                $left_schema[] = $v;
            }

        }



        $left_principal = $total_remain_principal-$paid_principal;


        // 计算手续费
        if ($interest_detail['prepayment_interest_type'] == 1) {
            $commission = $interest_detail['prepayment_interest'];
        } else {
            $commission = round(($paid_principal) * $interest_detail['prepayment_interest'] / 100, 2);
        }



        $total_paid_principal = $need_pay['principal']+$paid_principal;
        $total_paid_interest = $need_pay['interest'];
        $total_paid_penalty = $need_pay['penalty'];
        $total_paid_commission = $commission;

        $total_paid = $total_paid_principal+$total_paid_interest+$total_paid_penalty+$total_paid_commission;



        // 重新生成计划
        $left_schema_num = count($left_schema);
        if( $left_schema_num > 0 ){

            $new_interest = $interest_detail;

            $end_time = strtotime( date('Y-m-d',strtotime($contract->end_date) ) );
            $today_time = strtotime( date('Y-m-d') );
            $loan_days = ceil( ( $end_time- $today_time)/86400 );

            if( $new_interest['interest_payment'] == interestPaymentEnum::SINGLE_REPAYMENT ){
                // 一次还款累计到日计算利率
                $interest_rt = loan_baseClass::interestRateConversion($new_interest['interest_rate'],$new_interest['interest_rate_unit'],interestRatePeriodEnum::DAILY);
                if( !$interest_rt->STS ){
                    return $interest_rt;
                }
                $interest_rate = $interest_rt->DATA;
                $operator_fee_rt = loan_baseClass::interestRateConversion($new_interest['operation_fee'],$new_interest['operation_fee_unit'],interestRatePeriodEnum::DAILY);
                if( !$operator_fee_rt->STS ){
                    return $operator_fee_rt;
                }

                $operator_fee = $operator_fee_rt->DATA;
                $new_interest['interest_rate'] = $interest_rate*$loan_days;  // 单利
                $new_interest['operation_fee'] = $operator_fee*$loan_days;

            }else{

                $interest_rt = loan_baseClass::interestRateConversion($new_interest['interest_rate'],$new_interest['interest_rate_unit'],$new_interest['interest_rate_period']);
                if( !$interest_rt->STS ){
                    return $interest_rt;
                }
                $new_interest_rate = $interest_rt->DATA;

                $operator_fee_rt = loan_baseClass::interestRateConversion($new_interest['operation_fee'],$new_interest['operation_fee_unit'],$new_interest['interest_rate_period']);
                if( !$operator_fee_rt->STS ){
                    return $operator_fee_rt;
                }
                $new_operator_fee = $operator_fee_rt->DATA;
                $new_interest['interest_rate'] = $new_interest_rate;
                $new_interest['operation_fee'] = $new_operator_fee;
            }


            $new_loan_amount = $left_principal;
            $loan_base = new loan_baseClass();

            $re = $loan_base->getRepaymentSchemaOfAllType($new_interest['interest_payment'],$new_loan_amount,$loan_days,$new_interest,$left_schema_num);

            if( !$re->STS ){
                return $re;
            }

            $data = $re->DATA;
            $payment_schema = $data['payment_schema'];

            foreach( $left_schema as $k=>$v ){
                $temp = $v;
                $temp['receivable_principal'] = $payment_schema[$k]['receivable_principal'];
                $temp['receivable_interest'] = $payment_schema[$k]['receivable_interest'];
                $temp['receivable_operation_fee'] = $payment_schema[$k]['receivable_operation_fee'];
                $temp['amount'] = $payment_schema[$k]['amount'];
                $temp['actual_payment_amount'] = 0;
                $left_schema[$k] = $temp;
            }

        }

        $return_left_period = $left_schema;

        return new result(true, 'success', array(
            'total_prepayment_amount' => $total_paid,
            'currency' => $contract->currency,
            'total_paid_principal' => $total_paid_principal,
            'total_paid_interest' => $total_paid_interest,
            'total_paid_penalty' => $total_paid_penalty,
            'total_paid_commission' => $total_paid_commission,
            'prepayment_principal' => $new_amount,
            'prepayment_fee' => $total_paid_commission,
            'left_schema' => $return_left_period
        ));

    }


    /** 计算固定偿还期数提前还款详细
     * @param $contract_id
     * @param $left_period
     * @return result
     */
    public static function getPrepaymentDetailByRepayPeriod($contract_id,$repay_period)
    {
        $contract_id = intval($contract_id);
        $repay_period = intval($repay_period);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }


        $detail_re = self::getPrepaymentDetail($contract_id);
        if( !$detail_re->STS ){
            return $detail_re;
        }

        $data = $detail_re->DATA;
        $need_pay = $data['total_need_pay'];
        $contract_detail = $data['contract_detail'];
        $interest_detail = $contract_detail['interest_detail'];
        $remain_schema = $data['schema_detail']['last_schema'];
        $total_remain_principal = $data['total_left_principal'];

        $left_num = count($remain_schema);

        if( $repay_period > $left_num ){
            $repay_period = $left_num;
        }

        if( $repay_period < 0  ){
            // 没有剩余应还
            return new result(true, 'success', array(
                'total_prepayment_amount' => $need_pay,
                'currency' => $contract->currency,
                'total_paid_principal' => $need_pay['principal'],
                'total_paid_interest' => $need_pay['interest'],
                'total_paid_penalty' => $need_pay['penalty'],
                'total_paid_commission' => 0,
                'prepayment_principal' => 0,
                'prepayment_fee' => 0,
                'left_schema' => null
            ));
        }

        $should_pay = array();
        $left_schema = array();
        $pay_num = $repay_period;
        $counter = 1;
        foreach( $remain_schema as $v ){
            if( $counter <= $pay_num ){
                $should_pay[] = $v;
            }else{
                $left_schema[] = $v;
            }
            $counter++;
        }


        $paid_principal = 0;
        foreach( $should_pay as $v ){
            $paid_principal += $v['remaining_principal'];
        }


        // 计算手续费
        if ($interest_detail['prepayment_interest_type'] == 1) {
            $commission = $interest_detail['prepayment_interest'];
        } else {
            $commission = round(($paid_principal) * $interest_detail['prepayment_interest'] / 100, 2);
        }

        $total_paid_principal = $need_pay['principal']+$paid_principal;
        $total_paid_interest = $need_pay['interest'];
        $total_paid_penalty = $need_pay['penalty'];
        $total_paid_commission = $commission;
        $total_paid = $total_paid_principal+$total_paid_interest+$total_paid_penalty+$total_paid_commission;

        return new result(true, 'success', array(
            'total_prepayment_amount' => $total_paid,
            'currency' => $contract->currency,
            'total_paid_principal' => $total_paid_principal,
            'total_paid_interest' => $total_paid_interest,
            'total_paid_penalty' => $total_paid_penalty,
            'total_paid_commission' => $total_paid_commission,
            'prepayment_principal' => $paid_principal,
            'prepayment_fee' => $total_paid_commission,
            'left_schema' => $left_schema
        ));



    }




    /** 还款请求查账确认
     * @param $request_id
     * @param $received_date
     * @param $extend_info
     * @return result
     */
    public static function requestRepaymentConfirmReceived($request_id,$received_date,$handler_info)
    {

        $m_request = new loan_request_repaymentModel();
        $request = $m_request->getRow($request_id);
        if( !$request ){
            return new result(false,'No request info',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $request->state == requestRepaymentStateEnum::SUCCESS ){
            return new result(true,'success');
        }

        $sql = "select m.* from loan_contract c left join loan_account a on a.uid=c.account_id left join client_member m 
        on m.obj_guid=a.obj_guid ";
        $member_info = $m_request->reader->getRow($sql);
        if( !$member_info ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $member_id = $member_info['uid'];

        // 先将钱转入savings
        if( $request->state != requestRepaymentStateEnum::RECEIVED ){

            switch( $request->repayment_way ){
                case repaymentWayEnum::CASH :
                    $rt = passbookWorkerClass::memberDepositByCash($member_id,$handler_info['handler_id'],$request->amount,$request->currency);
                    break;
                case repaymentWayEnum::BANK_TRANSFER :
                    $rt = passbookWorkerClass::memberDepositByBank($member_id,$request->bank_id,$request->amount,$request->currency);
                    break;
                case repaymentWayEnum::AUTO_DEDUCTION :
                    $rt = passbookWorkerClass::memberDepositByPartner($member_id,$request->payer_id,$request->amount,$request->currency);
                    break;
                default:
                    $rt = new result(false,'Un supported way',null,errorCodesEnum::NOT_SUPPORTED);
            }
            if( !$rt->STS ){
                return $rt;
            }

            $request->state = requestRepaymentStateEnum::RECEIVED;
            $up = $request->update();
            if( !$up->STS ){
                return new result(false,'Update state fail',null,errorCodesEnum::DB_ERROR);
            }
        }


        // 处理合同
        if( $request->type == requestRepaymentTypeEnum::SCHEME ){


            $payment_info = array(
                'payer_id' => $request->payer_id,
                'payer_type' => $request->payer_type,
                'payer_name' => $request->payer_name,
                'payer_phone' => $request->payer_phone,
                'payer_account' => $request->payer_account,
                'payer_property' => $request->payer_property,
                'payer_image' => null,
                'branch_id' => 0,
                'teller_id' => 0,
                'teller_name' => null,
                'creator_id' => 0,
                'creator_name' => 'System'
            );
            // 计划还款的
            $re = self::schemaRepaymentPaidHandle($request->contract_id,$request->amount,$request->currency,$payment_info,$received_date);
            if( !$re->STS ){
                return $re;
            }


        }else{
            // 提前还款
            $apply_id = $request->prepayment_apply_id;
            $re = self::confirmPrepaymentReceived($apply_id,$request->amount,$request->currency,$handler_info);
            if( !$re->STS ){
                return $re;
            }
            $sql = "update loan_request_repayment set state='".requestRepaymentStateEnum::SUCCESS."' where prepayment_apply_id='$apply_id' 
            and state='".requestRepaymentStateEnum::RECEIVED."' ";
            $up = $m_request->conn->execute($sql);
            if( !$up->STS ){
                return new result(false,'Receive money success,update state fail!',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success');


    }


    public static function schemaRepaymentPaidHandle($contract_id,$amount,$currency,$payment_info,$received_date)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $repayment_amount = round($amount,2);
        if ($repayment_amount <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $contract_currency = $contract->currency;

        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract_currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'No set exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }


        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid='".$contract->account_id."' ";
        $member = $m_contract->reader->getRow($sql);

        // 贷款产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow($contract->product_id);

        $m_schema = new loan_installment_schemeModel();
        $m_log = new loan_repaymentModel();

        // 获取金额分配还款详细
        $re = self::getRepaymentSchemaByAmount($contract_id, $repayment_amount,$currency, $received_date);
        if (!$re->STS) {
            return $re;
        }

        $data = $re->DATA;
        $repayment_amount_balance = $data['repayment_amount_balance'];
        $schema_list = $data['repayment_schema'];

        $repayment_schema = array();


        $total_paid_principal = 0;
        if (!empty($schema_list)) {

            foreach ($schema_list as $schema) {

                $schema_id = $schema['uid'];
                // 还的本息
                $ap_amount = $schema['ap_amount'] ?: 0;
                // 还的罚金
                $ap_penalty = $schema['ap_penalty'];

                $row = $m_schema->getRow($schema_id);

                if (!$row) {
                    return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
                }


                // 账户扣钱
                $rt = (new loanRepaymentTradingClass($row,$currency,$ap_penalty))->execute();
                if( !$rt->STS ){
                    return $rt;
                }


                // 计算分配到的本金
                if( ($row->actual_payment_amount+$ap_amount) <= $row->receivable_principal ){
                    $total_paid_principal += $ap_amount;
                }else{
                    $total_paid_principal += ($row->receivable_principal-$row->actual_payment_amount);
                }

                $row->actual_payment_amount += $ap_amount;
                $row->paid_penalty += $ap_penalty;
                $row->settle_penalty += $schema['penalties'];
                $row->execute_time = Now();
                $row->last_repayment_time = Now();

                if (($row->actual_payment_amount >= $row->amount) && ($row->paid_penalty >= $schema['penalties'])) {
                    // 还清
                    $row->state = schemaStateTypeEnum::COMPLETE;
                    $row->done_time = Now();

                } else {
                    $row->state = schemaStateTypeEnum::GOING;
                }

                $up = $row->update();
                if (!$up->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }

                $repayment_schema[] = $row;


                // 添加还款日志
                $log = $m_log->newRow();
                $log->scheme_id = $schema_id;
                $log->contract_id = $contract->uid;
                $log->currency = $contract_currency;
                $log->receivable_amount = $ap_amount;
                $log->penalty_amount = $ap_penalty;
                $log->amount = $log->receivable_amount + $log->penalty_amount;
                $log->payer_id = intval($payment_info['payer_id']);
                $log->payer_type = $payment_info['payer_type'];
                $log->payer_name = $payment_info['payer_name'];
                $log->payer_phone = $payment_info['payer_phone'];
                $log->payer_account = $payment_info['payer_account'];
                $log->payer_property = $payment_info['payer_property'];
                $log->payer_image = $payment_info['payer_image'];
                $log->payer_amount = $repayment_amount;
                $log->payer_currency = $currency;
                $log->payer_exchange_rate = $exchange_rate;
                $log->branch_id = intval($payment_info['branch_id']);
                $log->teller_id = intval($payment_info['teller_id']);
                $log->teller_name = $payment_info['teller_name'];
                $log->create_time = Now();
                $log->creator_id = intval($payment_info['creator_id']);
                $log->creator_name = $payment_info['creator_name'];
                $log->state = loanRepaymentStateEnum::SUCCESS;
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }

        if( $product_info['is_credit_loan'] == 1 && $total_paid_principal>0 ){
            // 信用贷还款增加信用余额
            $re = member_creditClass::addCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member['uid'],$total_paid_principal);
            if( !$re->STS ){
                return $re;
            }
        }

        $is_paid_off = self::contractIsPaidOff($contract_id);
        if( $is_paid_off ){
            // 合同已还清
            $re = self::contractComplete($contract_id);
            if (!$re->STS) {
                return $re;
            }
        }

        return new result(true, 'success', array(
            'repayment_amount_balance' => $repayment_amount_balance,
            'repayment_schema' => $repayment_schema
        ));
    }


    /** 现场现金还款
     * @param $contract_id
     * @param $amount
     * @param $currency
     * @param $teller_id
     * @param $teller_name
     * @return result
     */
    public static function schemaManualRepaymentByCash($contract_id,$amount,$currency,$branch_id,$teller_id,$teller_name)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if ($contract->state < loanContractStateEnum::PENDING_DISBURSE) {
            return new result(false, 'Contract state error', null, errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }

        $repayment_amount = round($amount,2);
        if ($repayment_amount <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $contract_currency = $contract->currency;

        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract_currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'No set exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }


        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid='".$contract->account_id."' ";
        $member = $m_contract->reader->getRow($sql);
        $member_id = $member['uid'];

        // 贷款产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow($contract->product_id);

        $m_schema = new loan_installment_schemeModel();
        $m_log = new loan_repaymentModel();


        // 先将钱转入账户
        $rt = (new memberDepositByCashTradingClass($member_id,$teller_id,$amount,$currency))->execute();
        if( !$rt->STS ){
            return $rt;
        }

        // 获取金额分配还款详细
        $re = self::getRepaymentSchemaByAmount($contract_id, $repayment_amount,$currency, Now());
        if (!$re->STS) {
            return $re;
        }

        $data = $re->DATA;
        $repayment_amount_balance = $data['repayment_amount_balance'];
        $schema_list = $data['repayment_schema'];

        $repayment_schema = array();


        $total_paid_principal = 0;
        if (!empty($schema_list)) {

            foreach ($schema_list as $schema) {

                $schema_id = $schema['uid'];
                // 还的本息
                $ap_amount = $schema['ap_amount'] ?: 0;
                // 还的罚金
                $ap_penalty = $schema['ap_penalty'];

                $row = $m_schema->getRow($schema_id);

                if (!$row) {
                    return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
                }


                // 账户扣钱
                $rt = (new loanRepaymentTradingClass($row,$currency,$ap_penalty))->execute();
                if( !$rt->STS ){
                    return $rt;
                }


                // 计算分配到的本金
                if( ($row->actual_payment_amount+$ap_amount) <= $row->receivable_principal ){
                    $total_paid_principal += $ap_amount;
                }else{
                    $total_paid_principal += ($row->receivable_principal-$row->actual_payment_amount);
                }

                $row->actual_payment_amount += $ap_amount;
                $row->paid_penalty += $ap_penalty;
                $row->settle_penalty += $schema['penalties'];
                $row->execute_time = Now();
                $row->last_repayment_time = Now();

                if (($row->actual_payment_amount >= $row->amount) && ($row->paid_penalty >= $schema['penalties'])) {
                    // 还清
                    $row->state = schemaStateTypeEnum::COMPLETE;
                    $row->done_time = Now();

                } else {
                    $row->state = schemaStateTypeEnum::GOING;
                }

                $up = $row->update();
                if (!$up->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }

                $repayment_schema[] = $row;


                // 添加还款日志
                $log = $m_log->newRow();
                $log->scheme_id = $schema_id;
                $log->contract_id = $contract->uid;
                $log->currency = $contract_currency;
                $log->receivable_amount = $ap_amount;
                $log->penalty_amount = $ap_penalty;
                $log->amount = $log->receivable_amount + $log->penalty_amount;
                $log->payer_amount = $repayment_amount;
                $log->payer_currency = $currency;
                $log->payer_exchange_rate = $exchange_rate;
                $log->branch_id = $branch_id;
                $log->teller_id = $teller_id;
                $log->teller_name = $teller_name;
                $log->create_time = Now();
                $log->creator_id = $teller_id;
                $log->creator_name = $$teller_name;
                $log->state = loanRepaymentStateEnum::SUCCESS;
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }

        if( $product_info['is_credit_loan'] == 1 && $total_paid_principal>0 ){
            // 信用贷还款增加信用余额
            $re = member_creditClass::addCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member['uid'],$total_paid_principal);
            if( !$re->STS ){
                return $re;
            }
        }

        $is_paid_off = self::contractIsPaidOff($contract_id);
        if( $is_paid_off ){
            // 合同已还清
            $re = self::contractComplete($contract_id);
            if (!$re->STS) {
                return $re;
            }
        }

        return new result(true, 'success', array(
            'repayment_amount_balance' => $repayment_amount_balance,
            'repayment_schema' => $repayment_schema
        ));

    }


    /**
     * 确认还款动作  手工执行还款计划
     * @param $contract_id
     * @param $repayment_amount
     * @param $repayment_way
     * @param $received_date
     * @param array $payment_info
     * @param array $refBiz
     * @return result
     */
    protected static function schemaManualRepayment($contract_id, $repayment_amount,$currency, $repayment_way, $received_date, $payment_info = array(),$refBiz=array())
    {

        if ($repayment_way == repaymentWayEnum::AUTO_DEDUCTION) {

            $conn = ormYo::Conn();
            try {
                $conn->startTransaction();
                $re = self::schemaManualRepayment_auto_deduction($contract_id,$repayment_amount,$currency,$received_date,$payment_info,$refBiz);
                if (!$re->STS) {
                    $conn->rollback();
                    return $re;
                }
                $conn->submitTransaction();
                return $re;
            } catch (Exception $e) {
                return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
            }

        } else {
            $conn = ormYo::Conn();
            try {
                $conn->startTransaction();
                $re = self::schemaManualRepayment_offline($contract_id, $repayment_amount,$currency, $received_date, $payment_info);
                if (!$re->STS) {
                    $conn->rollback();
                    return $re;
                }
                $conn->submitTransaction();
                return $re;
            } catch (Exception $e) {
                return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
            }
        }

    }


    /** 线下还款
     * @param $contract_id
     * @param $repayment_amount
     * @param $received_date
     * @param $payment_info
     * @return result
     */
    protected static function schemaManualRepayment_offline($contract_id, $repayment_amount,$currency, $received_date, $payment_info)
    {

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if ($contract->state < loanContractStateEnum::PENDING_DISBURSE) {
            return new result(false, 'Contract state error', null, errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }

        $repayment_amount = round($repayment_amount,2);
        if ($repayment_amount <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $contract_currency = $contract->currency;

        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract_currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'No set exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }

        $exchange_amount = round($repayment_amount*$exchange_rate,2);


        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid='".$contract->account_id."' ";
        $member = $m_contract->reader->getRow($sql);

        // 贷款产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow($contract->product_id);

        $m_schema = new loan_installment_schemeModel();
        $m_log = new loan_repaymentModel();

        // 获取金额分配还款详细
        $re = self::getRepaymentSchemaByAmount($contract_id, $repayment_amount,$currency, $received_date);
        if (!$re->STS) {
            return $re;
        }

        $data = $re->DATA;
        $repayment_amount_balance = $data['repayment_amount_balance'];
        $schema_list = $data['repayment_schema'];

        $repayment_schema = array();


        $total_paid_principal = 0;
        if (!empty($schema_list)) {

            foreach ($schema_list as $schema) {

                $schema_id = $schema['uid'];
                // 还的本息
                $ap_amount = $schema['ap_amount'] ?: 0;
                // 还的罚金
                $ap_penalty = $schema['ap_penalty'];

                $row = $m_schema->getRow($schema_id);

                if (!$row) {
                    return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
                }


                // 账户扣钱
                $rt = (new loanRepaymentTradingClass($row,$currency,$ap_penalty))->execute();
                if( !$rt->STS ){
                    return $rt;
                }


                // 计算分配到的本金
                if( ($row->actual_payment_amount+$ap_amount) <= $row->receivable_principal ){
                    $total_paid_principal += $ap_amount;
                }else{
                    $total_paid_principal += ($row->receivable_principal-$row->actual_payment_amount);
                }

                $row->actual_payment_amount += $ap_amount;
                $row->paid_penalty += $ap_penalty;
                $row->settle_penalty += $schema['penalties'];
                $row->execute_time = Now();
                $row->last_repayment_time = Now();

                if (($row->actual_payment_amount >= $row->amount) && ($row->paid_penalty >= $schema['penalties'])) {
                    // 还清
                    $row->state = schemaStateTypeEnum::COMPLETE;
                    $row->done_time = Now();

                } else {
                    $row->state = schemaStateTypeEnum::GOING;
                }

                $up = $row->update();
                if (!$up->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }

                $repayment_schema[] = $row;


                // 添加还款日志
                $log = $m_log->newRow();
                $log->scheme_id = $schema_id;
                $log->contract_id = $contract->uid;
                $log->currency = $contract_currency;
                $log->receivable_amount = $ap_amount;
                $log->penalty_amount = $ap_penalty;
                $log->amount = $log->receivable_amount + $log->penalty_amount;
                $log->payer_id = intval($payment_info['payer_id']);
                $log->payer_type = $payment_info['payer_type'];
                $log->payer_name = $payment_info['payer_name'];
                $log->payer_phone = $payment_info['payer_phone'];
                $log->payer_account = $payment_info['payer_account'];
                $log->payer_property = $payment_info['payer_property'];
                $log->payer_image = $payment_info['payer_image'];
                $log->payer_amount = $repayment_amount;
                $log->payer_currency = $currency;
                $log->payer_exchange_rate = $exchange_rate;
                $log->branch_id = intval($payment_info['branch_id']);
                $log->teller_id = intval($payment_info['teller_id']);
                $log->teller_name = $payment_info['teller_name'];
                $log->create_time = Now();
                $log->creator_id = intval($payment_info['creator_id']);
                $log->creator_name = $payment_info['creator_name'];
                $log->state = loanRepaymentStateEnum::SUCCESS;
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }

        if( $product_info['is_credit_loan'] == 1 && $total_paid_principal>0 ){
            // 信用贷还款增加信用余额
            $re = member_creditClass::addCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member['uid'],$total_paid_principal);
            if( !$re->STS ){
                return $re;
            }
        }

        $is_paid_off = self::contractIsPaidOff($contract_id);
        if( $is_paid_off ){
            // 合同已还清
            $re = self::contractComplete($contract_id);
            if (!$re->STS) {
                return $re;
            }
        }

        return new result(true, 'success', array(
            'repayment_amount_balance' => $repayment_amount_balance,
            'repayment_schema' => $repayment_schema
        ));

    }


    /** 自动扣款
     * @return result
     */
    protected static function schemaManualRepayment_auto_deduction($contract_id,$repayment_amount,$currency,$received_date,$payment_info,$refBiz)
    {
        $repayment_amount = round($repayment_amount,2);
        if ($repayment_amount <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if ($contract->state < loanContractStateEnum::PENDING_DISBURSE) {
            return new result(false, 'Contract state error', null, errorCodesEnum::LOAN_CONTRACT_CAN_NOT_REPAYMENT);
        }

        $contract_currency = $contract->currency;

        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency,$contract_currency);
        if( $exchange_rate <= 0 ){
            return new result(false,'No set exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }
        $exchange_amount = round($repayment_amount*$exchange_rate,2);

        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid='".$contract->account_id."' ";
        $member = $m_contract->reader->getRow($sql);

        // 贷款产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow($contract->product_id);

        $handler_id = $payment_info['payer_id'];
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow($handler_id);
        if( !$handler ){
            return new result(false,'Invalid handler',null,errorCodesEnum::UNEXPECTED_DATA);
        }



        /*  ****** 1.先尝试更新计划 *****   */
        $m_schema = new loan_installment_schemeModel();
        $m_log = new loan_repaymentModel();

        // 获取金额分配还款详细
        $re = self::getRepaymentSchemaByAmount($contract_id, $repayment_amount,$currency, $received_date);
        if (!$re->STS) {
            return $re;
        }

        $data = $re->DATA;
        $repayment_amount_balance = $data['repayment_amount_balance'];
        $schema_list = $data['repayment_schema'];

        $repayment_schema = array();

        $total_paid_principal = 0;
        if (!empty($schema_list)) {

            foreach ($schema_list as $schema) {

                $schema_id = $schema['uid'];
                // 还的本息
                $ap_amount = $schema['ap_amount'] ?: 0;
                // 还的罚金
                $ap_penalty = $schema['ap_penalty'];

                $row = $m_schema->getRow($schema_id);

                if (!$row) {
                    return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
                }

                // 账户扣钱
                $rt = (new loanRepaymentTradingClass($row,$currency,$ap_penalty))->execute();
                if( !$rt->STS ){
                    return $rt;
                }

                // 计算分配到的本金
                if( ($row->actual_payment_amount+$ap_amount) <= $row->receivable_principal ){
                    $total_paid_principal += $ap_amount;
                }else{
                    $total_paid_principal += ($row->receivable_principal-$row->actual_payment_amount);
                }

                $row->actual_payment_amount += $ap_amount;
                $row->paid_penalty += $ap_penalty;
                $row->settle_penalty += $schema['penalties'];
                $row->execute_time = Now();
                $row->last_repayment_time = Now();

                if (($row->actual_payment_amount >= $row->amount) && ($row->paid_penalty >= $schema['penalties'])) {
                    // 还清
                    $row->state = schemaStateTypeEnum::COMPLETE;
                    $row->done_time = Now();

                } else {
                    $row->state = schemaStateTypeEnum::GOING;
                }

                $up = $row->update();
                if (!$up->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }

                $repayment_schema[] = $row;


                // 添加还款日志
                $log = $m_log->newRow();
                $log->scheme_id = $schema_id;
                $log->contract_id = $contract->uid;
                $log->currency = $currency;
                $log->receivable_amount = $ap_amount;
                $log->penalty_amount = $ap_penalty;
                $log->amount = $log->receivable_amount + $log->penalty_amount;
                $log->payer_id = $handler->uid;
                $log->payer_type = $handler->handler_type;
                $log->payer_name = $handler->handler_name;
                $log->payer_phone = $handler->handler_phone;
                $log->payer_account = $handler->handler_account;
                $log->payer_property = $handler->handler_property;
                $log->payer_image = $payment_info['payer_image'];
                $log->payer_amount = $repayment_amount;
                $log->payer_currency = $currency;
                $log->payer_exchange_rate = $exchange_rate;
                $log->branch_id = intval($payment_info['branch_id']);
                $log->teller_id = intval($payment_info['teller_id']);
                $log->teller_name = $payment_info['teller_name'];
                $log->create_time = Now();
                $log->creator_id = intval($payment_info['creator_id']);
                $log->creator_name = $payment_info['creator_name'];
                $log->state = loanRepaymentStateEnum::SUCCESS;
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }

        if( $product_info['is_credit_loan'] == 1 && $total_paid_principal>0 ){
            // 信用贷还款增加信用余额
            $re = member_creditClass::addCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member['uid'],$total_paid_principal);
            if( !$re->STS ){
                return $re;
            }
        }

        $is_paid_off = self::contractIsPaidOff($contract_id);
        if( $is_paid_off ){
            // 合同已还清
            $re = self::contractComplete($contract_id);
            if (!$re->STS) {
                return $re;
            }
        }

        return new result(true, 'success', array(
            'repayment_amount_balance' => $repayment_amount_balance,
            'repayment_schema' => $repayment_schema
        ));

    }


    /** 合同还款完成处理（complete）
     * @param $contract_id
     * @return result
     */
    public static function contractComplete($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }


        // 计算合同最终罚金
        $sql = "select sum(settle_penalty) from loan_installment_scheme where contract_id='$contract_id' and state!='".schemaStateTypeEnum::CANCEL."' ";
        $penalty = $m_contract->reader->getOne($sql);
        $penalty = round($penalty,2);

        // 更新合同状态
        $contract->receivable_penalty = $penalty;
        $contract->state = loanContractStateEnum::COMPLETE;
        $contract->finish_time = Now();
        $up = $contract->update();
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }

        //更新相关保险的合同
        $sql = "update insurance_contract set state='".insuranceContractStateEnum::COMPLETE."' where loan_contract_id='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }


        // 发送消息通知 member
        $sql = "select m.uid member_id from loan_account a inner join  client_member m on a.obj_guid=m.obj_guid where a.uid='" . $contract->account_id . "' ";
        $member_id = $m_contract->reader->getOne($sql);
        $title = 'Loan Contract Completed';
        $body = "Congratulations! You have paid off all your loans for your loan contract(contract sn: " . $contract->contract_sn . ")!";
        $send = member_messageClass::sendSystemMessage($member_id, $title, $body);

        return new result(true, 'success', $contract);
    }


    /** 计算合同核销损失
     * @param $contract_id
     * @return result
     */
    public static function calculateContractWriteOffLoss($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if ($contract->state <= loanContractStateEnum::PENDING_APPROVAL) {
            return new result(false, 'Can not write off this contract', null, errorCodesEnum::NOT_PERMITTED);
        }


        // 计算损失
        $sql = "select sum(receivable_principal) loss_principal,sum(receivable_interest) loss_interest,sum(receivable_operation_fee) loss_operation_fee,
        sum(receivable_admin_fee) loss_admin_fee,sum(actual_payment_amount) total_repayment from loan_installment_scheme 
        where contract_id='$contract_id' and state!='" . schemaStateTypeEnum::COMPLETE . "' and state!='" . schemaStateTypeEnum::CANCEL . "' ";
        $loss_arr = $m_contract->reader->getRow($sql);

        // 损失本金
        $loss_amount = $loss_arr['loss_principal'] - $loss_arr['total_repayment'];
        if( $loss_amount < 0 ){
            $loss_amount = 0;
        }

        return new result(true,'success',array(
            'loss_principal' => $loss_amount
        ));


    }


    /** 合同核销（意外核销）
     * @param $contract_id
     * @return result
     */
    public static function contractWriteOff($off_id,$extend = array())
    {
        $m_off = new loan_writtenoffModel();
        $off_info = $m_off->getRow($off_id);
        if( !$off_info ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $contract_id = $off_info->contract_id;

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 计算损失
        $sql = "select sum(receivable_principal) loss_principal,sum(receivable_interest) loss_interest,sum(receivable_operation_fee) loss_operation_fee,
        sum(receivable_admin_fee) loss_admin_fee,sum(actual_payment_amount) total_repayment from loan_installment_scheme 
        where contract_id='$contract_id' and state!='" . schemaStateTypeEnum::COMPLETE . "' and state!='" . schemaStateTypeEnum::CANCEL . "' ";
        $loss_arr = $m_contract->reader->getRow($sql);

        // 损失本金
        $loss_amount = $loss_arr['loss_principal'] - $loss_arr['total_repayment'];
        if( $loss_amount < 0 ){
            $loss_amount = 0;
        }

        // 计算损失罚金
        $sql = "select * from loan_installment_scheme where contract_id='$contract_id' and state!='".schemaStateTypeEnum::CANCEL."' and state!='".schemaStateTypeEnum::COMPLETE."' ";
        $schemas = $m_contract->reader->getRows($sql);
        $loss_penalty = 0;
        foreach( $schemas as $schema){
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema['uid']);
            $loss_penalty += $penalty;
        }

        // 更新合同
        $contract->loss_principal = $loss_amount;
        $contract->loss_interest = $loss_arr['loss_interest'];
        $contract->loss_admin_fee = $loss_arr['loss_admin_fee'];
        $contract->loss_operation_fee = $loss_arr['loss_operation_fee'];
        $contract->loss_penalty = $loss_penalty;
        $contract->state = loanContractStateEnum::WRITE_OFF;
        $up = $contract->update();
        if (!$up->STS) {
            return new result(false, 'Write off fail', null, errorCodesEnum::DB_ERROR);
        }

        // 更新
        $off_info->auditor_id = intval($extend['auditor_id']);
        $off_info->auditor_name = $extend['auditor_name'];
        $off_info->close_date = Now();
        $off_info->state = writeOffStateEnum::COMPLETE;
        $up = $off_info->update();
        if( !$up->STS ){
            return new result(false, 'Write off fail', null, errorCodesEnum::DB_ERROR);
        }

        //更新相关保险的合同
        $sql = "update insurance_contract set state='".insuranceContractStateEnum::COMPLETE."' where loan_contract_id='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }

        // 发送消息通知 member
        $sql = "select m.uid member_id from loan_account a inner join  client_member m on a.obj_guid=m.obj_guid where a.uid='" . $contract->account_id . "' ";
        $member_id = $m_contract->reader->getOne($sql);
        $title = 'Loan Contract Written Off';
        $body = "Your loan contract(contract sn: " . $contract->contract_sn . ") has been written off!";
        $send = member_messageClass::sendSystemMessage($member_id, $title, $body);

        return new result(true, 'success', array(
            'contract_info' => $contract,
            'write_off_info' => $off_info
        ));

    }


    /** 客户贷款合同数统计
     * @param $account_id
     * @param int $type
     * @return int
     */
    public static function getLoanAccountContractNumSummary($account_id,$type=0)
    {
        $account_id = intval($account_id);
        $r = new ormReader();
        $num = 0;
        switch( $type)
        {
            case 0:
                // all
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state>='".loanContractStateEnum::PENDING_DISBURSE."' ";
                $num = $r->getOne($sql);
                break;
            case 1:
                // 正常执行的(含逾期合同)
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state in('".loanContractStateEnum::PENDING_DISBURSE."','".loanContractStateEnum::PROCESSING."')";
                $num = $r->getOne($sql);
                break;
            case 2:
                // 延期的
                $sql = "select count(DISTINCT s.contract_id) from loan_installment_scheme s inner join loan_contract c on c.uid=s.contract_id and c.account_id='$account_id' and 
                c.state in('".loanContractStateEnum::PENDING_DISBURSE."','".loanContractStateEnum::PROCESSING."') and s.state!='".schemaStateTypeEnum::CANCEL."' and  s.state!='".schemaStateTypeEnum::COMPLETE."' and s.receivable_date<'".date('Y-m-d')."' ";
                $num = $r->getOne($sql);
                break;
            case 3:
                // 被拒绝的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='".loanContractStateEnum::REFUSED."' ";
                $num = $r->getOne($sql);
                break;
            case 4:
                // write off
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='".loanContractStateEnum::WRITE_OFF."' ";
                $num = $r->getOne($sql);
                break;
            case 5:
                // 正常完成的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='".loanContractStateEnum::COMPLETE."' ";
                $num = $r->getOne($sql);
                break;
            case 6:
                // 待审核的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state in('".loanContractStateEnum::CREATE."','".loanContractStateEnum::PENDING_APPROVAL."') ";
                $num = $r->getOne($sql);
                break;
            default:
                break;
        }
        return $num;
    }



    /** 提前还款确认到账
     * @param $apply_id
     * @return result
     */
    public static function confirmPrepaymentReceived($apply_id,$amount,$currency,$extent_info)
    {
        $m_apply = new loan_prepayment_applyModel();
        $apply = $m_apply->getRow($apply_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $apply->state = prepaymentApplyStateEnum::RECEIVED;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }


        $m_contract = new loan_contractModel();
        $contract_id = $apply->contract_id;
        $contract = $m_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'Unexpected data',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $contract_currency = $contract->currency;


        // 贷款产品信息+会员信息
        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid='".$contract->account_id."' ";
        $member = $m_contract->reader->getRow($sql);
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow($contract->product_id);


        // 查询全部还款信息
        $m_request_payment = new loan_request_repaymentModel();
        $request_repayment = $m_request_payment->getRows(array(
            'prepayment_apply_id' => $apply_id,
            'state' => requestRepaymentStateEnum::RECEIVED
        ));
        $payment_amount = 0;
        foreach( $request_repayment as $k=>$v ){
            $rate = global_settingClass::getCurrencyRateBetween($v['currency'],$currency);
            if( $rate <= 0 ){
                return new result(false,'No exchange rate',null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $exchange_amount = round($v['amount']*$rate,2);
            $payment_amount += $exchange_amount;
            $v['exchange_rate'] = $rate;
            $v['exchange_amount'] = $exchange_amount;
            $request_repayment[$k] = $v;
        }

        $params = array(
            'contract_id' => $apply->contract_id,
            'prepayment_type' => $apply->prepayment_type,
            'amount' => $apply->principal_amount,
            'repay_period' => $apply->repay_period
        );
        $prepayment_re = self::prepaymentPreview($params);
        if( !$prepayment_re->STS ){
            return $prepayment_re;
        }

        $prepayment_data = $prepayment_re->DATA;
        $new_schema = $prepayment_data['left_schema'];  // 剩余的新计划
        $total_paid_principal = $prepayment_data['prepayment_principal']; // 实际偿还的本金部分



        // 账户中扣钱
        $total_amount = $prepayment_data['total_prepayment_amount'];
        $principal_amount = $prepayment_data['total_need_pay']['principal'];
        $total_interest = $prepayment_data['total_need_pay']['interest'];
        $penalty = $prepayment_data['total_need_pay']['penalty'];
        $prepayment_fee = $prepayment_data['prepayment_fee'];
        $rt = (new loanPrepaymentTradingClass($contract_id,$total_amount,$principal_amount,$total_interest,$penalty,$prepayment_fee,$apply->currency))->execute();
        if( !$rt->STS ){
            return $rt;
        }


            // 更新处理状态
            $apply->state = prepaymentApplyStateEnum::SUCCESS;
            $apply->update_time = Now();
            $apply->handler_id = intval($extent_info['handler_id']);
            $apply->handler_name = $extent_info['handler_name'];
            $apply->handle_time = Now();
            $up = $apply->update();
            if( !$up->STS ){
                return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
            }


            //插入还款记录
            $m_repayment = new loan_repaymentModel();
            foreach( $request_repayment as $v ){
                $new_row = $m_repayment->newRow();
                $new_row->contract_id = $contract_id;
                $new_row->amount = $v['exchange_amount'];
                $new_row->currency = $currency;
                $new_row->payer_id = intval($v['payer_id']);
                $new_row->payer_type = $v['payer_type'];
                $new_row->payer_name = $v['payer_name'];
                $new_row->payer_phone = $v['payer_phone'];
                $new_row->payer_account = $v['payer_account'];
                $new_row->payer_property = $v['payer_property'];
                $new_row->payer_amount = $v['amount'];
                $new_row->payer_currency = $v['currency'];
                $new_row->payer_exchange_rate = $v['exchange_rate'];
                $new_row->branch_id = intval($extent_info['branch_id']);
                $new_row->teller_id = intval($extent_info['teller_id']);
                $new_row->teller_name = $extent_info['teller_name'];
                $new_row->creator_id = intval($extent_info['creator_id']);
                $new_row->creator_name = $extent_info['creator_name'];
                $new_row->create_time = Now();
                $insert = $new_row->insert();
                if( !$insert->STS ){

                    return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
                }
            }

            $sql = "update loan_request_repayment set state='".requestRepaymentStateEnum::SUCCESS."' where prepayment_apply_id='$apply_id' 
                and state='".requestRepaymentStateEnum::RECEIVED."' ";
            $up = $m_contract->conn->execute($sql);
            if( !$up->STS ){
                return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
            }

            // 更改合同计划状态
            $sql = "update loan_installment_scheme set state='".schemaStateTypeEnum::CANCEL."',done_time='".Now()."' where contract_id='$contract_id' 
            and state!='".schemaStateTypeEnum::COMPLETE."'  ";
            $up = $m_contract->conn->execute($sql);
            if( !$up->STS ){

                return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
            }

            // 插入新的计划
            $new_num = count($new_schema);
            $m_schema = new loan_installment_schemeModel();
            if( $new_num > 0 ){

                // 修正小数
                $total_decimals = 0;
                $counter = 1;
                foreach( $new_schema as $schema ){

                    if( $counter == $new_num ){
                        $amount = $schema['amount']+$total_decimals;
                    }else{
                        $amount = floor($schema['amount']);
                        $decimals = $schema['amount'] - $amount;
                        $total_decimals += $decimals;
                    }
                    $counter++;

                    $schema_row = $m_schema->newRow();
                    $schema_row->contract_id = $contract_id;
                    $schema_row->scheme_idx = $schema['scheme_idx'];
                    $schema_row->scheme_name = $schema['scheme_name'];
                    $schema_row->receivable_date = $schema['receivable_date'];
                    $schema_row->penalty_start_date = $schema['penalty_start_date'];
                    $schema_row->receivable_principal = $schema['receivable_principal'];
                    $schema_row->receivable_interest = $schema['receivable_interest'];
                    $schema_row->receivable_operation_fee = $schema['receivable_operation_fee'];
                    $schema_row->receivable_admin_fee = $schema['receivable_admin_fee'];
                    $schema_row->amount = $amount;
                    $schema_row->state = schemaStateTypeEnum::CREATE;
                    $schema_row->create_time = Now();
                    $insert = $schema_row->insert();
                    if( !$insert->STS ){

                        return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
                    }

                }
            }

            if( $product_info['is_credit_loan'] == 1 && $total_paid_principal>0 ){
                // 信用贷还款增加信用余额
                $re = member_creditClass::addCreditBalance(creditEventTypeEnum::CREDIT_LOAN,$member['uid'],$total_paid_principal);
                if( !$re->STS ){
                    return $re;
                }
            }

            $is_complete = self::contractIsPaidOff($contract_id);
            if( $is_complete ){
                $rt = self::contractComplete($contract_id);
                if( !$rt->STS ){

                    return $rt;
                }
            }


            return new result(true,'success');

    }


    /** 贷款合同添加担保人
     * @param $contract_id
     * @param $guarantor_list
     * @return result
     */
    public static function contractAddGuarantor($contract_id,$guarantor_list)
    {
        $contract_id = intval($contract_id);
        if( empty($guarantor_list) || !is_array($guarantor_list) ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $values = array();
        $in_str = implode(',',$guarantor_list);
        $sql = "select * from client_member where uid in ($in_str)";
        $member_list = $m_member->reader->getRows($sql);

        $sql = "insert into loan_contract_guarantor(contract_id,guarantor_id,guarantor_name,update_time) values ";
        foreach( $member_list as $member ){
            $member_name = $member['display_name']?:$member['login_code'];
            $temp = "('$contract_id','".$member['uid']."','$member_name','".Now()."')";
            $values[] = $temp;
        }
        $sql .= implode(',',$values);
        $ret = $m_member->conn->execute($sql);
        if( !$ret->STS ){
            return new result(false,'Db error '.$ret->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }

    public static function contractAddMortgage($contract_id,$mortgage_list)
    {
        $contract_id = intval($contract_id);
        if( empty($mortgage_list) || !is_array($mortgage_list) ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m = new member_assetsModel();
        $in_str = implode(',',$mortgage_list);
        $sql = "select * from member_assets where uid in ($in_str) ";
        $asset_list = $m->reader->getRows($sql);

        $values = array();
        $sql = "insert into loan_contract_mortgage(contract_id,asset_id,update_time) values ";
        foreach( $asset_list as $asset ){
            $temp = "('$contract_id','".$asset['uid']."','".Now()."')";
            $values[] = $temp;
        }

        $sql .= implode(',',$values);
        $insert = $m->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Db error '.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $now = Now();
        $up_sql = "update member_assets set mortgage_state='1',mortgage_time='$now' where uid in ( $in_str ) ";
        $up = $m->conn->execute($up_sql);
        if( !$up->STS ){
            return new result(false,'Db error '.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');


    }

    public static function contractAddFiles($contract_id,$files_list)
    {
        $contract_id = intval($contract_id);
        if( empty($mortgage_list) || !is_array($mortgage_list) ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m = new loan_contract_filesModel();

        $sql = "insert into loan_contract_files(contract_id,file_type,file_name,file_path,create_time) values ";
        $values = array();
        foreach( $files_list as $file_path ){
            $extend_info = pathinfo($file_path);
            $file_type = $extend_info['extension'];
            $file_name = $extend_info['filename'];
            $time = Now();
            $temp = "('$contract_id','$file_type','$file_name','$file_path','$time')";
            $values[] = $temp;
        }
        $sql .= implode(',',$values);
        $insert = $m->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Db error '.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');
    }


    /** 合同进入执行状态逻辑
     * @param $contract_id
     */
    public static function contractConfirmToExecute($contract_id)
    {
        $m_contract = new loan_contractModel();
        $sql = "update loan_contract set state='".loanContractStateEnum::PENDING_DISBURSE."',update_time='".Now()."' where uid ='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Db error '.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }



}