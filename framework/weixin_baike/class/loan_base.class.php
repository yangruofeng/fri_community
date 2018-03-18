<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/11
 * Time: 15:01
 */

// 贷款基类
class loan_baseClass
{
    public function __construct()
    {

    }


    /** 信用额度计算器
     * @param $params
     * @return result
     */
    public static function creditLimitCalculator($params)
    {

        $level = array();
        $m = new loan_credit_cert_levelModel();
        $level_list = $m->getAll();
        foreach( $level_list as $value ){
            $item = array(
                'value' => $value['match_value'],
                'credit' => $value['max_amount']?:$value['min_amount'],
            );
            $level[] = $item;
        }

        // 先计算组合值
        $sum = 0;
        $values = trim(($params['values']));
        $values = trim($values,',');
        if( $values ){
            $arr = explode(',',$values);
            foreach( $arr as $v){
                $v = intval($v);
                $sum = $sum | $v;
            }
        }


        // 匹配设置的组合
        $re = array();
        foreach( $level as $k=>$v ){
            if( ($v['value'] & $sum) == $v['value'] ){
                $re[$k] = $v;
            }
        }

        // 取设置的最大信用值
        $credit = 0;
        if( !empty($re) ){
            foreach( $re as $item ){
                if( $item['credit'] > $credit ){
                    $credit = $item['credit'];
                }
            }
        }

        return new result(true,'success',$credit);
    }


    /** 贷款合同编号规则
     * @param int $member_guid
     */
    public static function generateLoanContractSn($member_guid=0)
    {
        $m_account = new loan_accountModel();
        $account = $m_account->getRow(array(
            'obj_guid' => $member_guid
        ));
        $account_id = $account?$account->uid:0;
        $sql = "select count(*) from loan_contract where account_id='$account_id' ";
        $num = $m_account->reader->getOne($sql);
        $c_num = $num+1;
        $prefix = contractPrefixSNEnum::LOAN;
        $code = ($prefix+$member_guid+$c_num)%10;  // 验证码 1位
        $sn = $prefix . '-'.$member_guid.'-'.str_pad($c_num,3,'0',STR_PAD_LEFT).'-'.$code;
        return $sn;
    }

    /** 保险合同编号规则
     * @param int $member_guid
     * @return string
     */
    public static function generateInsuranceContractSn($member_guid=0)
    {
        $m_account = new insurance_accountModel();
        $account = $m_account->getRow(array(
            'obj_guid' => $member_guid
        ));
        $account_id = $account?$account->uid:0;
        $sql = "select count(*) from insurance_contract where account_id='$account_id' ";
        $num = $m_account->reader->getOne($sql);
        $c_num = $num+1;
        $prefix = contractPrefixSNEnum::INSURANCE;
        $code = ($prefix+$member_guid+$c_num)%10;  // 验证码 1位
        $sn = $prefix . '-'.$member_guid.'-'.str_pad($c_num,3,'0',STR_PAD_LEFT).'-'.$code;
        return $sn;
    }


    /** 计算贷款天数
     * @param $value
     * @param $unit
     * @return result
     */
    public static function calLoanDays($value,$unit)
    {
        $value = intval($value);
        $loan_days = 0;
        switch($unit){
            case loanPeriodUnitEnum::DAY:
                $loan_days = $value;
                break;
            case loanPeriodUnitEnum::MONTH:
                $loan_days = $value*30;
                break;
            case loanPeriodUnitEnum::YEAR :
                $loan_days = $value*360;  // 按360天算
                break;
            default:
                return new result(false,'Non supported loan period type',null,errorCodesEnum::NOT_SUPPORTED);
        }
        return new result(true,'success',$loan_days);
    }


    /** 计算分期还款期数
     * @param $loan_time *贷款时间
     * @param $loan_time_unit  *贷款时间单位
     * @param $payment_type *还款方式
     * @param $payment_period *还款周期，比如一月还一次，一周还一次
     * @return result
     */
    public static function calPaymentPeriod($loan_time,$loan_time_unit,$payment_type,$payment_period)
    {
        // $loan_time,$loan_time_unit,$payment_type,$payment_period

        if( $loan_time < 1 ){
            return new result(false,'Time error',null,errorCodesEnum::NOT_SUPPORTED);
        }

        if( $payment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            // 一次性还款没有累计计算利息
            return new result(true,'success',1);
        }

        switch( $loan_time_unit ){
            case loanPeriodUnitEnum::YEAR :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time*2);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time*4);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time*12);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time*48);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time*365);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case loanPeriodUnitEnum::MONTH :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time/12);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time/6);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time/3);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time*4);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time*30);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case loanPeriodUnitEnum::DAY :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time/365);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time*2/365);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time/120);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time/30);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time/7);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            default:
                return new result(false,'Not supported loan time type',null,errorCodesEnum::NOT_SUPPORTED);
        }


        $total_period = ($total_period >=1 )?$total_period:1;
        return new result(true,'success',$total_period);
    }


    /** 获得贷款还款日
     * @param $loan_days
     * @param $repayment_type
     * @param $repayment_period
     * @param null $lending_time
     * @return array()
     */
    public static function getLoanDueDate($loan_days,$repayment_type,$repayment_period,$lending_time=null)
    {
        if( !$lending_time ){
            $lending_time = time();
        }else{
            $lending_time = strtotime($lending_time);
        }
        $due_date = '';
        $due_date_type = dueDateTypeEnum::FIXED_DATE;
        if( $repayment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            $due_date = date('Y-m-d',$lending_time+$loan_days*24*3600);
        }else{

            switch( $repayment_period ){
                case interestRatePeriodEnum::DAILY:
                    $due_date_type = dueDateTypeEnum::PER_DAY;
                    break;
                case interestRatePeriodEnum::WEEKLY:
                    $due_date = date('w',$lending_time);  // 每周几 0-6
                    $due_date_type = dueDateTypeEnum::PER_WEEK;
                    break;
                case interestRatePeriodEnum::MONTHLY :
                    $due_date = date('d',$lending_time);
                    $due_date_type = dueDateTypeEnum::PER_MONTH;
                    break;
                case interestRatePeriodEnum::QUARTER :
                    $first = date('m-d',strtotime('+3 month',$lending_time));
                    $second = date('m-d',strtotime('+6 month',$lending_time));
                    $third = date('m-d',strtotime('+9 month',$lending_time));
                    $fourth = date('m-d',strtotime('+12 month',$lending_time));
                    $due_date = $first.','.$second.','.$third.','.$fourth;
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                case interestRatePeriodEnum::SEMI_YEARLY :
                    $first = date('m-d',strtotime('+6 month',$lending_time));
                    $second = date('m-d',strtotime('+12 month',$lending_time));
                    $due_date = $first.','.$second;
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                case interestRatePeriodEnum::YEARLY :
                    $due_date = date('m-d',$lending_time);
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                default:
                    $due_date = '';
            }

        }
        return array(
            'due_date' => $due_date,
            'due_date_type' => $due_date_type
        );

    }




    /** 获得贷款详细产品和利率信息
     * @param $loan_amount     *贷款金额
     * @param $loan_days       *贷款天数
     * @param $payment_type    *还款方式
     * @param $payment_period  *还款周期
     * @return result
     */
    public static function getLoanInterestDetail($product_id,$loan_amount,$currency,$loan_days,$payment_type,$payment_period,$extend_info=array())
    {
        $m_product = new loan_productModel();
        $product_info = $m_product->find(array(
            'uid' => $product_id,
        ));
        if( !$product_info ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $loan_days = intval($loan_days);

        $reader = new ormReader();

        if( $payment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            // 贷款时长、金额、货币、还款方式满足
            $sql = "select * from loan_product_size_rate where product_id='".$product_info['uid']."' and loan_size_min<='$loan_amount' ";
            $sql .= " and loan_size_max>='$loan_amount' and interest_payment='$payment_type'  ";
            $sql .= " and min_term_days<='$loan_days' and max_term_days>='$loan_days' and currency='$currency' ";
        }else{
            // 贷款时长、金额、货币、还款方式、还款周期满足
            $sql = "select * from loan_product_size_rate where product_id='".$product_info['uid']."' and loan_size_min<='$loan_amount' ";
            $sql .= " and loan_size_max>='$loan_amount' and interest_payment='$payment_type' and interest_rate_period='$payment_period'  ";
            $sql .= " and min_term_days<='$loan_days' and max_term_days>='$loan_days' and currency='$currency' ";
        }

        $interest_array = $reader->getRows($sql);
        if( count($interest_array) < 1 ){
            return new result(false,'Did not set interest: '." $loan_amount --- ".$currency.' --- '.$loan_days.' --- '.$payment_type.' --- '.$payment_period,null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $interest_info = array();

        // 附加条件利率
        $guarantee_type = $extend_info['guarantee_type']?$extend_info['guarantee_type']:null;
        $mortgage_type = $extend_info['mortgage_type']?$extend_info['mortgage_type']:null;

        // 先取主利率
        foreach( $interest_array as $interest ){

            if( $interest['guarantee_type'] == $guarantee_type && $interest['mortgage_type'] == $mortgage_type  ){
                $interest_info = $interest;
                break;
            }else{

                // 高配到低配
                if( $guarantee_type && !$mortgage_type && $interest['guarantee_type'] == $guarantee_type ){
                    $interest_info = $interest;
                    break;
                }elseif( $mortgage_type && !$guarantee_type && $interest['mortgage_type'] == $mortgage_type ){
                    $interest_info = $interest;
                    break;
                }else{
                    $interest_info = $interest; // 取到最新设置的一个利率
                }

            }

        }


        if( !$interest_info || empty($interest_info) ){
            return new result(false,'No interest info',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $size_rate = $interest_info;

        // 再去优先匹配特殊利率
        $member_grade = $extend_info['member_grade'];
        $is_staff = $extend_info['is_staff']?1:0;
        $is_government = $extend_info['is_government']?1:0;
        $is_rival_client = $extend_info['is_rival_client']?1:0;

        // todo 是否改成联合设置利率，如政府员工+对手客户的利率？

        $special_rate = null;
        // 先处理特殊客户的利率，会员等级有可能有等级没设置利率的情况
        if( $is_staff || $is_government ||  $is_rival_client ){

            $sql = "select * from loan_product_special_rate where size_rate_id='".$interest_info['uid']."' and 1=1 ";
            // 多个条件满足只匹配一个 优先顺序是内部员工-对手客户-政府员工
            if( $is_staff ){
                $sql .= " and client_type='".clientTypeRateEnum::STAFF."' ";
            }elseif( $is_rival_client ){
                $sql .= " and client_type='".clientTypeRateEnum::RIVAL_CLIENT."' ";
            }else{
                $sql .= " and client_type='".clientTypeRateEnum::GOVERNMENT."' ";
            }
            $sql .= " order by interest_rate desc ";

            $rows = $reader->getRows($sql);
            if( count($rows) > 0 ){

                // 初始化一个基本利率
                $special_rate = reset($rows);
                // 同时有member_grade
                if( $member_grade ){
                    foreach( $rows as $v ){
                        if( $v['client_grade'] == $member_grade ){  // 匹配等级
                            $special_rate = $v;
                            break;
                        }
                    }
                }

            }

        }else{

            // 只有等级
            if( $member_grade ){
                $sql = "select * from loan_product_special_rate where size_rate_id='".$interest_info['uid']."' and client_grade='".$member_grade."' and ( client_type is null or client_type=0 )  order by interest_rate desc ";
                $special_rate = $reader->getRow($sql);
            }
        }

        // 存在特殊利率
        if( $special_rate ){

            unset($special_rate['uid']);
            $interest_info = array_merge((array)$interest_info,(array)$special_rate);

            /*$interest_info['interest_rate'] = $special_rate['interest_rate'];
            $interest_info['interest_rate_type'] = $special_rate['interest_rate_type'];
            $interest_info['interest_min_value'] = $special_rate['interest_min_value'];
            $interest_info['admin_fee'] = $special_rate['admin_fee'];
            $interest_info['admin_fee_type'] = $special_rate['admin_fee_type'];
            $interest_info['operation_fee'] = $special_rate['operation_fee'];
            $interest_info['operation_fee_type'] = $special_rate['operation_fee_type'];
            $interest_info['operation_min_value'] = $special_rate['operation_min_value'];*/
        }


        return new result(true,'success',array(
            'product_info' => $product_info,
            'interest_info' => $interest_info,  // 计算用利率
            'size_rate' => $size_rate,
            'special_rate' => $special_rate
        ));
    }


    /** 利率转换
     * @param $value
     * @param $from_type @当前利率周期
     * @param $to_type @目标利率周期
     * @return result
     */
    public static function interestRateConversion($value,$from_type,$to_type)
    {
        $new_value = $value;
        switch( $from_type ){
            case interestRatePeriodEnum::YEARLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value/2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value/4;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/12;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value/48;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/360;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::SEMI_YEARLY:
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*2;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value/2;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/6;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value/24;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value*2/360;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::QUARTER:
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*4;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/3;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value/12;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/90;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::MONTHLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*12;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*6;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value*3;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value/4;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/30;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::WEEKLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*48;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*24;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value*12;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value*4;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/7;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::DAILY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*360;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*360/2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value*3*30;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value*30;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            default:
                return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
        }

        return new result(true,'success',$new_value);
    }


    /** 获得分期的时间间隔
     * @param $payment_period
     * @return result
     */
    public static function getInstalmentPaymentTimeInterval($payment_period)
    {
        // 分期
        switch ( $payment_period ) {
            case interestRatePeriodEnum::YEARLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'year'
                );
                break;
            case interestRatePeriodEnum::SEMI_YEARLY :
                $arr = array(
                    'value' => 6,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::QUARTER :
                $arr = array(
                    'value' => 3,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::MONTHLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::WEEKLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'week'
                );
                break;
            case interestRatePeriodEnum::DAILY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'day'
                );
                break;
            default:
                return new result(false, 'Not supported payment period', null, errorCodesEnum::NOT_SUPPORTED);
        }
        return new result(true,'success',$arr);
    }


    /** 验证贷款时间和还款周期是否匹配
     * @return bool
     */
    public static function verifyLoanTimeAndRepaymentPeriod($loan_time_unit,$repayment_type,$repayment_period)
    {
        if( $repayment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            return true;
        }else{

            switch ($loan_time_unit){
                case loanPeriodUnitEnum::YEAR :
                    return true;
                    break;
                case loanPeriodUnitEnum::MONTH :
                    if( $repayment_period == interestRatePeriodEnum::MONTHLY
                        || $repayment_period == interestRatePeriodEnum::WEEKLY
                        || $repayment_period == interestRatePeriodEnum::DAILY
                    ){
                        return true;
                    }
                    break;
                case loanPeriodUnitEnum::DAY :
                    if( $repayment_period == interestRatePeriodEnum::DAILY ){
                        return true;
                    }
                    break;
                default:
                    return false;
            }
        }
        return false;
    }


    /**  获得还款详细
     * @param $loan_amount
     * @param $loan_days *具体贷款天数
     * @param $loan_time *贷款时间
     * @$loan_time_unit *贷款时间单位，如year month day
     * @param $interest_info
     * @param $payment_type
     * @param $payment_period
     * @return mixed|null|result
     */
    public function getPaymentDetail($loan_amount,$loan_days,$loan_time,$loan_time_unit,$interest_info,$payment_type,$payment_period)
    {
        $total_period = self::calPaymentPeriod($loan_time,$loan_time_unit,$payment_type,$payment_period);
        if( !$total_period->STS ){
            return $total_period;
        }
        $total_period = $total_period->DATA;


        $new_interest = $interest_info;
        if( $payment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            // 一次还款累计到日计算利率
            $interest_rt = self::interestRateConversion($interest_info['interest_rate'],$interest_info['interest_rate_unit'],interestRatePeriodEnum::DAILY);
            if( !$interest_rt->STS ){
                return $interest_rt;
            }
            $interest_rate = $interest_rt->DATA;
            $operator_fee_rt = self::interestRateConversion($interest_info['operation_fee'],$interest_info['operation_fee_unit'],interestRatePeriodEnum::DAILY);
            if( !$operator_fee_rt->STS ){
                return $operator_fee_rt;
            }
            $operator_fee = $operator_fee_rt->DATA;
            $new_interest['interest_rate'] = $interest_rate*$loan_days;  // 单利
            $new_interest['operation_fee'] = $operator_fee*$loan_days;

        }else{
            $interest_rt = self::interestRateConversion($interest_info['interest_rate'],$interest_info['interest_rate_unit'],$payment_period);
            if( !$interest_rt->STS ){
                return $interest_rt;
            }
            $interest_rate = $interest_rt->DATA;
            $operator_fee_rt = self::interestRateConversion($interest_info['operation_fee'],$interest_info['operation_fee_unit'],$payment_period);
            if( !$operator_fee_rt->STS ){
                return $operator_fee_rt;
            }
            $operator_fee = $operator_fee_rt->DATA;
            $new_interest['interest_rate'] = $interest_rate;
            $new_interest['operation_fee'] = $operator_fee;
        }


        return $this->getRepaymentSchemaOfAllType($payment_type,$loan_amount,$loan_days,$new_interest,$total_period);

    }


    public  function getRepaymentSchemaOfAllType($payment_type,$loan_amount,$loan_days,$interest_info,$total_period)
    {

        switch( $payment_type ){
            case interestPaymentEnum::SINGLE_REPAYMENT :
                $re = $this->getPaymentDetailOfSingleRepayment($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::FIXED_PRINCIPAL :
                $re = $this->getPaymentDetailOfFixedPrincipal($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::ANNUITY_SCHEME :
                $re = $this->getPaymentDetailOfAnnuitySchema($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::FLAT_INTEREST :
                $re = $this->getPaymentDetailOfFlatInterest($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::BALLOON_INTEREST :
                $re = $this->getPaymentDetailOfBalloonInterest($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            default:
                return new result(false,'Not supported payment type',null,errorCodesEnum::NOT_SUPPORTED);
        }

        return $re;
    }

    /** 一次还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info  ***利率信息是换算过的期利率（如设置的年利率，按月还，利率是换算过的月利率）
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfSingleRepayment($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::single_repayment_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::single_repayment_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 等额本金还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfFixedPrincipal($loan_amount,$loan_period,$interest_info,$payment_period)
    {


        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::fixed_principle_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::fixed_principle_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }


    /**  等额本息还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfAnnuitySchema($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 固定期息还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfFlatInterest($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::flat_interest_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::flat_interest_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 先利息后本金还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfBalloonInterest($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::balloon_interest_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::balloon_interest_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }


    public function calculator($loan_amount,$loan_period,$loan_period_unit,$repayment_type,$repayment_period,$currency='USD',$extend=array())
    {
        $re = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$re->STS ){
            return $re;
        }
        $loan_days = $re->DATA;
        $reader = new ormReader();

        if( $repayment_type == interestPaymentEnum::SINGLE_REPAYMENT ){
            // 贷款时长、金额、还款方式满足
            $sql = "select r.*,p.product_code,p.product_name,r.currency from loan_product_size_rate r inner join loan_product p on r.product_id=p.uid  where  r.loan_size_min<='$loan_amount' ";
            $sql .= " and r.loan_size_max>='$loan_amount' and r.interest_payment='$repayment_type'  ";
            $sql .= " and r.min_term_days<='$loan_days' and r.max_term_days>='$loan_days' and p.state='".loanProductStateEnum::ACTIVE."' ";
        }else{
            // 贷款时长、金额、还款方式、还款周期满足
            $sql = "select r.*,p.product_code,p.product_name,r.currency from loan_product_size_rate r inner join loan_product p on r.product_id=p.uid where  r.loan_size_min<='$loan_amount' ";
            $sql .= " and r.loan_size_max>='$loan_amount' and r.interest_payment='$repayment_type' and r.interest_rate_period='$repayment_period'  ";
            $sql .= " and r.min_term_days<='$loan_days' and r.max_term_days>='$loan_days' and p.state='".loanProductStateEnum::ACTIVE."' ";
        }

        $products = $reader->getRows($sql);
        if( count($products) <1 ){
            return new result(false,'No matched product',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $params = array(
            'amount' => $loan_amount,
            'loan_period' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $repayment_type,
            'repayment_period' => $repayment_period
        );
        $list = array();
        foreach( $products as $product ){
            $product_id = $product['product_id'];
            $params['product_id'] = $product_id;
            $re = self::loanPreview($params);
            if( $re->STS ){
                $data = $re->DATA;
                $data['product_info'] = array(
                    'product_id' => $product_id,
                    'product_name' => $product['product_name'],
                    'product_code' => $product['product_code'],
                );
                $list[] = $data;
            }
        }

        return new result(true,'success',$list);

    }


    /** 创建贷款绑定保险产品合同
     * @param $loan_amount * 贷款金额
     * @param $loan_contract_id
     * @param $insurance_item_id
     * @param $member_id
     * @param int $amount
     * @param $is_temp bool 是否临时合同
     * @param array $extent
     * @return result
     */
    public function createLoanInsuranceContract($loan_amount,$loan_contract_id,$insurance_item_id,$member_id,$currency='USD',$extent=array())
    {

        $m_item = new insurance_product_itemModel();
        $m_member = new memberModel();
        $m_insurance = new insurance_productModel();
        $insurance_item = $m_item->getRow($insurance_item_id);
        if( !$insurance_item_id ){
            return new result(false,'Unknown insurance item',null,errorCodesEnum::NO_INSURANCE_ITEM);
        }
        $insurance_product_id = $insurance_item->product_id;
        $insurance_product = $m_insurance->getRow($insurance_product_id);
        if( !$insurance_product ){
            return new result(false,'No insurance product',null,errorCodesEnum::NO_INSURANCE_PRODUCT);
        }

        if( $insurance_product->state != insuranceProductStateEnum::ACTIVE ){
            return new result(false,'Insurance non execute product',null,errorCodesEnum::INSURANCE_PRODUCT_NX);
        }

        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::INVALID_PARAM);
        }


        $m_account = new insurance_accountModel();
        $insurance_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if( !$insurance_account){
            $insurance_account = $m_account->newRow();
            $insurance_account->obj_guid = $member->obj_guid;
            $insurance_account->account_type = insuranceAccountTypeEnum::MEMBER;
            $insurance_account->update_time = Now();
            $in = $insurance_account->insert();
            if( !$in->STS ){
                return new result(false,'Create insurance account fail',null,errorCodesEnum::DB_ERROR);
            }
        }
        $insurance_account_id = $insurance_account->uid;
        $m_contract = new insurance_contractModel();
        $contract_sn = self::generateInsuranceContractSn($member->obj_guid);

        $contract = $m_contract->newRow();
        $contract->account_id = $insurance_account_id;
        $contract->contract_sn = $contract_sn;
        $contract->create_time = Now();
        $contract->creator_id = 0;
        $contract->creator_name = 'System';
        $contract->product_id = $insurance_product_id;
        $contract->product_item_id = $insurance_item_id;
        $contract->start_date = Now();
        if( $insurance_item->is_fixed_valid_days ){
            $days = intval($insurance_item->fixed_valid_days);
            $contract->end_date = date('Y-m-d H:i:s',time()+$days*24*3600);
        }

        $loan_amount = round($loan_amount,2);
        // 保额 价格
        if( $insurance_item->is_fixed_amount ){
            $insurance_amount = $insurance_item->fixed_amount;  // 保额
            $insurance_price = $insurance_item->fixed_price;
        }else{
            $insurance_amount = $loan_amount;
            $insurance_price = $loan_amount*($insurance_item['price_rate']); // todo 具体百分比还是计算值

        }

        $contract->currency = $currency;
        $contract->start_insured_amount = $insurance_amount;
        $contract->price = $insurance_price;
        $contract->state = insuranceContractStateEnum::CREATE;
        $contract->loan_contract_id = $loan_contract_id;
        $insert = $contract->insert();
        if( !$insert->STS ){
            return new result(false,' Create insurance contract fail',null,errorCodesEnum::DB_ERROR);
        }

        // 创建计划,一次缴费
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_LOAN,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        $handler_id = $handler?$handler['uid']:0;
        $m_schema = new insurance_payment_schemeModel();
        $payment_schema = $m_schema->newRow();
        $payment_schema->contract_id = $contract->uid;
        $payment_schema->scheme_idx = 1;
        $payment_schema->scheme_name = 'Period 1';
        $payment_schema->payable_date = date('Y-m-d');
        $payment_schema->amount = $contract->price;
        $payment_schema->account_handler_id = $handler_id;
        $payment_schema->state = insuranceContractStateEnum::CREATE;
        $payment_schema->create_time = Now();
        $in = $payment_schema->insert();
        if( !$in->STS ){
            return new result(false,'Create insurance payment schema fail',null,errorCodesEnum::DB_ERROR);
        }

        // 受益人
        $m_benefit = new insurance_contract_beneficiaryModel();
        $new_row = $m_benefit->newRow();
        $new_row->contract_id = $contract->uid;
        $new_row->benefit_index = 1;
        $new_row->benefit_name = $member->display_name?:$member->login_code;
        $new_row->benefit_phone = $member->phone_id;
        $new_row->benefit_addr = '';
        $insert2 = $new_row->insert();
        if( !$insert2->STS ){
            return new result(false,'Create insurance contract benefit fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',$contract);

    }


    public function loanPreviewBeforeCreateContract($loan_amount,$currency,$loan_time,$loan_time_unit,$repayment_type,$repayment_period,$interest_info)
    {

        // 计算目标天数
        $rt = self::calLoanDays($loan_time,$loan_time_unit);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;
        if( $loan_days <= 0 ){
            return new result(false,'Invalid loan days',null,errorCodesEnum::INVALID_AMOUNT);
        }

        // 获得还款计划
        $payment_re = $this->getPaymentDetail($loan_amount,$loan_days,$loan_time,$loan_time_unit,$interest_info,$repayment_type,$repayment_period);
        if( !$payment_re->STS ){
            return new result(false,'Create installment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];

        // 管理费
        $admin_fee = 0;
        if( $interest_info['admin_fee'] ){

            if( $interest_info['admin_fee_type'] == 1 ){
                $admin_fee = $interest_info['admin_fee']+0;
            }else{
                $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
            }
        }

        // 贷款手续费
        $loan_fee = 0;
        if( $interest_info['loan_fee'] > 0 ){

            if( $interest_info['loan_fee_type'] == 1 ){
                $loan_fee = $interest_info['loan_fee'];
            }else{
                $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
            }
        }

        $return = array(
            'loan_amount' => $loan_amount,
            'currency' => $currency,
            'loan_time' => $loan_time,
            'loan_time_unit' => $loan_time_unit,
            'repayment_type' => $repayment_type,
            'repayment_period' => $repayment_period,
            'admin_fee' => $admin_fee,
            'loan_fee' => $loan_fee,
            'disbursement_amount' => $loan_amount-$admin_fee-$loan_fee,
            'total_interest' => $total_payment['total_interest'],
            'total_operation_fee' => $total_payment['total_operator_fee'],
            'interest_info' => $interest_info,
            'installment_schema' => $payment_schema,
            'total_repayment_detail' => $total_payment
        );

       return new result(true,'success',$return);


    }


    /** 贷款预览（创建合同前）
     * @param $params
     * @return result
     */
    public function loanPreview($params)
    {
        $product_id = $params['product_id']?:0;
        $loan_amount = $params['amount'];
        $loan_period = intval($params['loan_period']);  // 贷款周期
        $loan_period_unit = $params['loan_period_unit'];

        $re = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$re->STS ){
            return $re;
        }
        $loan_days = $re->DATA;

        $payment_type = $params['repayment_type'];
        $payment_period = $params['repayment_period'];



        if( $loan_amount <0 || $loan_period<0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return new result(false,'No this product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        if( $product_info->state == loanProductStateEnum::HISTORY ){
            return new result(false,'Product is un-shelve',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if( $product_info->state != loanProductStateEnum::ACTIVE ){
            return new result(false,'Non execute product version ',null,errorCodesEnum::LOAN_PRODUCT_NX);
        }

        $interest_re = self::getLoanInterestDetail($product_id,$loan_amount,'USD',$loan_days,$payment_type,$payment_period,null);

        if( !$interest_re->STS ){
            return $interest_re;
        }
        $interest_data = $interest_re->DATA;

        $interest_info = $interest_data['interest_info'];

        $admin_fee = $operator_fee = 0;
        if( $interest_info['admin_fee'] ){

            if( $interest_info['admin_fee_type'] == 1 ){
                $admin_fee = $interest_info['admin_fee'];
            }else{
                $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
            }
        }

        // 贷款手续费
        $loan_fee = 0;
        if( $interest_info['loan_fee'] > 0 ){

            if( $interest_info['loan_fee_type'] == 1 ){
                $loan_fee = $interest_info['loan_fee'];
            }else{
                $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
            }
        }

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
        }

        $payment_re = self::getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$payment_type,$payment_period);
        if( !$payment_re->STS ){
            return new result(false,'Calculate fail',null,errorCodesEnum::DB_ERROR);
        }

        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];
        $return = array(
            'loan_amount' => $loan_amount,
            'loan_period_value' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $payment_type,
            'repayment_period' => $payment_period,
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'admin_fee' => $admin_fee,
            'loan_fee' => $loan_fee,
            'arrival_amount' => $loan_amount-$admin_fee-$loan_fee,
            'product_info' => $product_info,
            'interest_info' => $interest_info,
            'period_repayment_amount' => $total_payment['total_period_pay'],
            'total_repayment'=> $total_payment,
            'repayment_schema' => $payment_schema
        );
        return new result(true,'success',$return);

    }



    /**
     * @param $loan_params  **贷款参数
     *  array(
     *   member_id   会员ID
     *   product_id   贷款产品ID
     *   amount       贷款金额
     *   currency     币种
     *   loan_period   贷款周期
     *   loan_period_unit  贷款周期单位（年、月等）
     *   repayment_type    还款方式
     *   repayment_period  还款周期
     *   handle_account_id  绑定的操作账户ID
     *   insurance_item_id 绑定的保险项目 如2,3,5
     *   application_id  申请ID
     *   mortgage_type  抵押类型
     *   guarantee_type  担保类型
     *   creator_id
     *   creator_name
     * )
     * @param $interest_info  **利率信息
     * *  array(
     *   product_size_rate_id  有的话就传
     *   product_special_rate_id  有的话就传
     *   interest_rate
     *   interest_rate_type
     *   interest_rate_unit
     *   interest_min_value
     *   operation_fee
     *   operation_fee_unit
     *   operation_fee_type
     *   operation_min_value
     *   admin_fee
     *   admin_fee_type
     *   loan_fee
     *   loan_fee_type
     *   is_full_interest
     *   prepayment_interest
     *   prepayment_interest_type
     *   penalty_rate
     *   penalty_divisor_days
     *   grace_days
     * )
     * @param bool $is_period_limit  是否检查周期还款能力
     * @return bool|ormResult|result
     */
    public function createContract($loan_params,$interest_info,$is_period_limit=false){

        $params = $loan_params;
        $member_id = $params['member_id'];
        $product_id = $params['product_id'];
        $loan_amount = intval($params['amount']);
        $loan_period = intval($params['loan_period']);
        $loan_period_unit = $params['loan_period_unit'];
        $currency = $params['currency']?:currency::USD;
        $payment_type = trim($params['repayment_type']);
        $payment_period = trim($params['repayment_period']);
        $handle_account_id = intval($params['handle_account_id']);

        // 计算目标天数
        $rt = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;
        if( $loan_days <= 0 ){
            return new result(false,'Invalid loan days',null,errorCodesEnum::INVALID_AMOUNT);
        }


        // 首先检查贷款时间和还款方式、周期是否合理
        $re = self::verifyLoanTimeAndRepaymentPeriod($loan_period_unit,$payment_type,$payment_period);
        if( !$re ){
            return new result(false,'Not supported repayment type',null,errorCodesEnum::REPAYMENT_UN_MATCH_LOAN_TIME);
        }

        // 检查member  todo 检查member状态
        $m_member = new memberModel();
        $member_info = $m_member->getRow($member_id);
        if( !$member_info ){
            return new result(false,'No client member',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        // 贷款账户
        $m_account = new loan_accountModel();
        $loan_account_info = $m_account->getRow(array(
            'obj_guid' => $member_info->obj_guid,
            'account_type' => loanAccountTypeEnum::MEMBER
        ));
        if( !$loan_account_info ){
            $loan_account_info = $m_account->newRow();
            $loan_account_info->obj_guid = $member_info->obj_guid;
            $loan_account_info->account_type = loanAccountTypeEnum::MEMBER;
            $insert = $loan_account_info->insert();
            if( !$insert->STS ){
                return new result(false,'Loan account error: '.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }


        $m_account_handler = new member_account_handlerModel();
        // 有选择操作账户
        $account_handler = null;
        if( $handle_account_id ){
            $account_handler = $m_account_handler->getRow(array(
                'uid' => $handle_account_id,
            ));
            if( !$account_handler ){
                return new result(false,'No account handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
            }
        }else{
            // 没有选择账户默认使用储蓄账户
            $account_handler = member_handlerClass::getMemberDefaultPassbookHandlerInfo($member_id);
        }


        // 产品信息
        $m_product = new loan_productModel();
        $product_info = $m_product->getRow(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return new result(false,'No this product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        if( $product_info->state == loanProductStateEnum::HISTORY ){
            return new result(false,'Product is un-shelve',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if( $product_info->state != loanProductStateEnum::ACTIVE ){
            return new result(false,'Non execute product version ',null,errorCodesEnum::LOAN_PRODUCT_NX);
        }


        // 利率信息由外部传进来
        // 还款计划
        $payment_re = $this->getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$payment_type,$payment_period);
        if( !$payment_re->STS ){
            return new result(false,'Create installment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];
        if( empty($payment_schema) ){
            return new result(false,'Cal payment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        if( $is_period_limit ){
            //检查member的月还款能力
            if( $payment_type != interestPaymentEnum::SINGLE_REPAYMENT ){
                $first_period = reset($payment_schema);
                $max_period_amount = $first_period['amount'];
                $month_ability = $loan_account_info['repayment_ability'];
                // 货币转换
                $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD,$currency);
                if( $exchange_rate > 0 ){
                    $month_ability = round($month_ability*$exchange_rate,2);
                }
                $transfer_ability = self::interestRateConversion($month_ability,interestRatePeriodEnum::MONTHLY,$payment_period);
                if( !$transfer_ability->STS ){
                    return $transfer_ability;
                }
                $transfer_ability = $transfer_ability->DATA;
                if( $transfer_ability < $max_period_amount ){
                    return new result(false,'Insufficient repayment capacity',null,errorCodesEnum::INSUFFICIENT_REPAYMENT_CAPACITY);
                }
            }
        }




            $m_contract = new loan_contractModel();
            $contract_sn = self::generateLoanContractSn($member_info->obj_guid);

            // 贷款次数 有合同就算
            $sql = "select count(*) total  from loan_contract where account_id='".$loan_account_info->uid."' ";
            $loan_cycle = $m_contract->reader->getOne($sql);

            // 管理费
            $admin_fee = 0;
            if( $interest_info['admin_fee'] ){

                if( $interest_info['admin_fee_type'] == 1 ){
                    $admin_fee = $interest_info['admin_fee']+0;
                }else{
                    $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
                }
            }

            // 贷款手续费
            $loan_fee = 0;
            if( $interest_info['loan_fee'] > 0 ){

                if( $interest_info['loan_fee_type'] == 1 ){
                    $loan_fee = $interest_info['loan_fee'];
                }else{
                    $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
                }
            }


            //  计算还款日
            $due_date_array = self::getLoanDueDate($loan_days,$payment_type,$payment_period,time());
            $due_date = $due_date_array['due_date'];
            $due_date_type = intval($due_date_array['due_date_type']);


            // 创建贷款合同
            $new_contract = $m_contract->newRow();
            $new_contract->account_id = $loan_account_info->uid;
            $new_contract->contract_sn = $contract_sn;
            $new_contract->product_id = $product_id;

            if( isset($interest_info['product_size_rate_id']) ){
                $new_contract->product_sub_id = intval($interest_info['product_size_rate_id']);
            }

            if( isset($interest_info['product_special_rate_id']) ){
                $new_contract->product_special_rate_id = intval($interest_info['product_special_rate_id']);
            }

            $new_contract->currency = $interest_info['currency'];
            $new_contract->apply_amount = $loan_amount;
            $new_contract->application_id = intval($params['application_id']);
            $new_contract->propose = $params['propose'];
            $new_contract->due_date = $due_date;
            $new_contract->due_date_type = $due_date_type;
            $new_contract->repayment_type = $payment_type;
            $new_contract->repayment_period = $payment_period;
            $new_contract->loan_cycle = $loan_cycle+1;
            $new_contract->loan_term_day = $loan_days;
            $new_contract->loan_period_value = $loan_period;
            $new_contract->loan_period_unit = $loan_period_unit;
            $new_contract->mortgage_type = $params['mortgage_type'];
            $new_contract->guarantee_type = $params['guarantee_type'];
            $new_contract->installment_frequencies = count($payment_schema);
            $new_contract->interest_rate = $interest_info['interest_rate'];
            $new_contract->interest_rate_type = $interest_info['interest_rate_type']?1:0;
            $new_contract->interest_rate_unit = $interest_info['interest_rate_unit'];
            $new_contract->interest_min_value = round($interest_info['interest_min_value'],2);
            $new_contract->operation_fee = $interest_info['operation_fee'];
            $new_contract->operation_fee_type = $interest_info['operation_fee_type']?1:0;
            $new_contract->operation_fee_unit = $interest_info['operation_fee_unit'];
            $new_contract->operation_min_value = round($interest_info['operation_min_value'],2);
            $new_contract->admin_fee = $interest_info['admin_fee']?:0;
            $new_contract->admin_fee_type = $interest_info['admin_fee_type']?:0;
            $new_contract->loan_fee = $interest_info['loan_fee']?:0;
            $new_contract->loan_fee_type = $interest_info['loan_fee_type']?:0;
            $new_contract->is_full_interest = $interest_info['is_full_interest']?:0;
            $new_contract->prepayment_interest = $interest_info['prepayment_interest']?:0;
            $new_contract->prepayment_interest_type = $interest_info['prepayment_interest_type']?:0;
            $new_contract->penalty_rate = $interest_info['penalty_rate']?:$product_info->penalty_rate;
            $new_contract->penalty_divisor_days = $interest_info['penalty_divisor_days']?:$product_info->penalty_divisor_days;
            $new_contract->grace_days = intval($interest_info['grace_days']);
            if( $payment_type == interestPaymentEnum::BALLOON_INTEREST ){
                $new_contract->is_balloon_payment = 1;
            }
            $new_contract->is_advance_interest = intval($params['is_advance_interest']);
            $new_contract->ref_interest = $interest_info['interest_rate'];
            $new_contract->ref_admin_fee = $admin_fee;
            $new_contract->ref_loan_fee = $loan_fee;
            $new_contract->ref_operation_fee = $total_payment['total_operator_fee']+0;
            $new_contract->receivable_principal = $loan_amount;
            $new_contract->receivable_interest = $total_payment['total_interest']+0;
            $new_contract->receivable_admin_fee = $admin_fee;
            $new_contract->receivable_loan_fee = $loan_fee;
            $new_contract->receivable_operation_fee = $total_payment['total_operator_fee']+0;
            $new_contract->receivable_annual_fee = 0; // 暂时没有
            $contract_s_time = time();
            $contract_e_time = $contract_s_time+$loan_days*24*3600;
            $new_contract->start_date = date('Y-m-d H:i:s',$contract_s_time);
            $new_contract->end_date = date('Y-m-d H:i:s',$contract_e_time);
            if( $params['creator_id'] ){
                $new_contract->creator_id = intval($params['creator_id']);
                $new_contract->creator_name = $params['creator_name'];
            }else{
                $new_contract->creator_id = 0;
                $new_contract->creator_name = 'System';
            }
            $new_contract->create_time = Now();

            $new_contract->state = loanContractStateEnum::CREATE;
            $insert1 = $new_contract->insert();
            if( !$insert1->STS ){

                return new result(false,'Create contract fail '.$insert1->MSG,null,errorCodesEnum::DB_ERROR);
            }


            $loan_contract_id = $new_contract->uid;
            // 处理绑定的保险产品
            $insurance_item_id = trim($params['insurance_item_id'],',');
            if( $insurance_item_id ){

                $insurance_items = explode(',',$insurance_item_id);
                $insurance_contract_list = array();
                if( count($insurance_items) > 0 ){

                    $insurance_total_amount = 0;
                    foreach( $insurance_items as $item_id){
                        $item_id = intval($item_id);
                        if( $item_id ){
                            $re = $this->createLoanInsuranceContract($loan_amount,$loan_contract_id,$item_id,$member_id,$new_contract->currency,array());
                            if( !$re->STS ){

                                return new result(false,$re->MSG,null,errorCodesEnum::CREATE_INSURANCE_CONTRACT_FAIL);
                            }
                            $insurance_contract = $re->DATA;
                            $insurance_total_amount += $insurance_contract['price'];
                            $insurance_contract_list[] = $insurance_contract;
                        }
                    }

                    // 更新到贷款产品
                    $new_contract->is_insured = 1;
                    $new_contract->receivable_insurance_fee = $insurance_total_amount;
                    $up = $new_contract->update();
                    if( !$up->STS ){

                        return new result(false,'Update loan contract fail',null,errorCodesEnum::DB_ERROR);
                    }

                }

            }


            // 所有计划状态
            $schema_state = schemaStateTypeEnum::CREATE;


            // 插入放款计划表  一次性放款 todo 分期放款
            $new_distribute_schema = array();
            $m_distribute_schema = new loan_disbursement_schemeModel();
            $distribute_schema = $m_distribute_schema->newRow();
            $distribute_schema->contract_id = $new_contract->uid;
            $distribute_schema->scheme_idx = 1;
            $distribute_schema->disbursable_date = date('Y-m-d');
            $distribute_schema->create_time = Now();
            $distribute_schema->principal = $loan_amount;
            $distribute_schema->deduct_annual_fee = 0;
            $distribute_schema->deduct_interest = 0;
            $distribute_schema->deduct_admin_fee = $admin_fee;
            $distribute_schema->deduct_loan_fee = $loan_fee;
            $distribute_schema->deduct_operation_fee = 0;
            // 保险费
            $insurance_fee = 0;
            if( $new_contract->receivable_insurance_fee > 0 ){
                $insurance_fee = $new_contract->receivable_insurance_fee;
            }
            $distribute_schema->deduct_insurance_fee = $insurance_fee;
            $distribute_schema->amount = $loan_amount-$admin_fee-$loan_fee-$insurance_fee;
            $distribute_schema->account_handler_id = $account_handler?$account_handler->uid:0;
            $distribute_schema->disbursement_org = '';
            $distribute_schema->state = $schema_state;
            $insert = $distribute_schema->insert();
            if( !$insert->STS ){

                return new result(false,'Insert distribute schema fail',null,errorCodesEnum::DB_ERROR);
            }
            $new_distribute_schema[] = $distribute_schema;



            // 插入还款计划表
            $m_payment_schema = new loan_installment_schemeModel();

            if( $payment_type == interestPaymentEnum::SINGLE_REPAYMENT ){  // 一次还款
                $instalment_schema = current($payment_schema);
                if( !$instalment_schema || !is_array($instalment_schema)){

                    return new result(false,'Unknown error',null,errorCodesEnum::UNEXPECTED_DATA);
                }

                $receive_date = date('Y-m-d',$contract_e_time);
                $schema_row = $m_payment_schema->newRow();
                $schema_row->contract_id = $new_contract->uid;
                $schema_row->scheme_idx = $instalment_schema['scheme_index'];
                $schema_row->scheme_name = 'Period '.$instalment_schema['scheme_index'];
                $schema_row->receivable_date = $receive_date;
                $schema_row->penalty_start_date = date('Y-m-d',$contract_e_time+$interest_info['grace_days']*24*3600);
                $schema_row->receivable_principal = $instalment_schema['receivable_principal'];
                $schema_row->receivable_interest = $instalment_schema['receivable_interest'];
                $schema_row->receivable_operation_fee = $instalment_schema['receivable_operation_fee'];
                $schema_row->receivable_admin_fee = 0;
                $schema_row->amount = $instalment_schema['amount'];
                $schema_row->account_handler_id = $account_handler?$account_handler['uid']:0;
                $schema_row->state = $schema_state;
                $schema_row->create_time = date('Y-m-d H:i:s');
                $insert = $schema_row->insert();
                if( !$insert->STS ){

                    return new result(false,'Insert schema fail',null,errorCodesEnum::DB_ERROR);
                }
                $new_payment_schema[] = $schema_row;

            }else{  // 分期还款

                $re = self::getInstalmentPaymentTimeInterval($payment_period);
                if( !$re->STS ){

                    return $re;
                }
                $time_interval_arr = $re->DATA;  // 'value' => 1,'unit' => 'year'
                $time_interval_value = $time_interval_arr['value'];
                $time_interval_unit = $time_interval_arr['unit'];


                $create_time = date('Y-m-d H:i:s');
                $new_payment_schema = array();

                $total_period = count($payment_schema);

                /*foreach( $payment_schema as $instalment_schema ){

                    $new_pay_time = strtotime($time_interval,$new_pay_time);

                    // 处理每期还款的小数问题,取整，小数部分累计到最后一期
                    if( $counter == $total_period ){
                        $pay_amount = round($instalment_schema['amount']+$total_mantissa,3);
                    }else{
                        $pay_amount = floor($instalment_schema['amount']);
                        $left = round($instalment_schema['amount']-$pay_amount,3);
                        $total_mantissa = round($total_mantissa+$left,3);
                    }
                    $counter++;

                    $schema_row = $m_payment_schema->newRow();
                    $schema_row->contract_id = $new_contract->uid;
                    $schema_row->scheme_idx = $instalment_schema['scheme_index'];
                    $schema_row->scheme_name = 'Period '.$instalment_schema['scheme_index'];
                    $schema_row->receivable_date = date('Y-m-d H:i:s',$new_pay_time);
                    $schema_row->penalty_start_date = date('Y-m-d H:i:s',$new_pay_time+$interest_info['grace_days']*24*3600);
                    $schema_row->receivable_principal = $instalment_schema['receivable_principal'];
                    $schema_row->receivable_interest = $instalment_schema['receivable_interest'];
                    $schema_row->receivable_operation_fee = $instalment_schema['receivable_operation_fee'];
                    $schema_row->receivable_admin_fee = 0;
                    $schema_row->amount = $pay_amount;
                    $schema_row->account_handler_id = $account_handler?$account_handler['uid']:0;
                    $schema_row->state = $schema_state;
                    $schema_row->create_time = $create_time;
                    $insert = $schema_row->insert();
                    if( !$insert->STS ){
                        $conn->rollback();
                        return new result(false,'Insert schema fail',null,errorCodesEnum::DB_ERROR);
                    }

                    $new_payment_schema[] = $schema_row;
                }*/

                // 单一语句执行，循环执行速度超鸡慢
                $field_array = array(
                    'contract_id',
                    'scheme_idx',
                    'scheme_name',
                    'receivable_date',
                    'penalty_start_date',
                    'receivable_principal',
                    'receivable_interest',
                    'receivable_operation_fee',
                    'receivable_admin_fee',
                    'amount',
                    'account_handler_id',
                    'state',
                    'create_time'
                );
                //$insert_sql = "insert into loan_installment_scheme(contract_id,scheme_idx,scheme_name,receivable_date,penalty_start_date,receivable_principal,receivable_interest,receivable_operation_fee,receivable_admin_fee,amount,account_handler_id,state,create_time) values ";
                $insert_sql = "insert into loan_installment_scheme(".join(',',$field_array).") values  ";
                $sql_array = array();
                $lending_time = time();
                $new_pay_time = $lending_time;  // 每一期的还款时间
                $counter = 1;
                $total_mantissa = 0;
                reset($payment_schema);
                foreach( $payment_schema as $instalment_schema ){

                    // 用初始累计的方式，放弃上期增加的方式
                    $new_pay_time = strtotime('+'.$counter*$time_interval_value.' '.$time_interval_unit,$lending_time);

                    // 处理每期还款的小数问题,取整，小数部分累计到最后一期
                    if( $counter == $total_period ){
                        // 将本金调整到和贷款总额一样

                        $pay_amount = round($instalment_schema['amount']+$total_mantissa,3);
                    }else{
                        $pay_amount = floor($instalment_schema['amount']);
                        $left = round($instalment_schema['amount']-$pay_amount,3);
                        $total_mantissa = round($total_mantissa+$left,3);
                    }
                    $counter++;

                    // 严格按照上面定义的字段插入顺序
                    $temp = array(
                        'contract_id' => $new_contract->uid,
                        'scheme_idx' => $instalment_schema['scheme_index'],
                        'scheme_name' => 'Period '.$instalment_schema['scheme_index'],
                        'receivable_date' => date('Y-m-d',$new_pay_time),
                        'penalty_start_date' => date('Y-m-d',$new_pay_time+$interest_info['grace_days']*24*3600),
                        'receivable_principal' => $instalment_schema['receivable_principal'],
                        'receivable_interest' => $instalment_schema['receivable_interest'],
                        'receivable_operation_fee' => $instalment_schema['receivable_operation_fee'],
                        'receivable_admin_fee' => 0,
                        'amount' => $pay_amount,
                        'account_handler_id' => $account_handler?$account_handler['uid']:0,
                        'state' => $schema_state,
                        'create_time' => $create_time
                    );
                    $str = "( '".$temp['contract_id']."',";
                    $str .= "'".$temp['scheme_idx']."',";
                    $str .= "'".$temp['scheme_name']."',";
                    $str .= "'".$temp['receivable_date']."',";
                    $str .= "'".$temp['penalty_start_date']."',";
                    $str .= "'".$temp['receivable_principal']."',";
                    $str .= "'".$temp['receivable_interest']."',";
                    $str .= "'".$temp['receivable_operation_fee']."',";
                    $str .= "'".$temp['receivable_admin_fee']."',";
                    $str .= "'".$temp['amount']."',";
                    $str .= "'".$temp['account_handler_id']."',";
                    $str .= "'".$temp['state']."',";
                    $str .= "'".$temp['create_time']."' )";

                    $sql_array[] = $str;
                    $new_payment_schema[] = $temp;
                }



                // 拼接sql
                $insert_sql .= trim(join(',',$sql_array),',');

                $re = $m_payment_schema->conn->execute($insert_sql);
                if( !$re->STS ){

                    return new result(false,'Insert payment schema fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
                }


                // 将合同时间更新到还款最后时间
                /*$new_contract->end_date = date('Y-m-d H:i:s',$new_pay_time);
                $up = $new_contract->update();*/
            }


            $re = loan_contractClass::getLoanContractDetailInfo($new_contract->uid);
            if( !$re->STS ){
                return $re;
            }
            $return_info = $re->DATA;

            return new result(true,'success',$return_info);


    }




    /** 取消合同
     * @param $contract_id
     * @return result
     */
    public static function cancelContract($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $contract->state == loanContractStateEnum::CANCEL ){
            return new result(true,'success');
        }

        if( $contract->state >= loanContractStateEnum::PENDING_DISBURSE ){
            return new result(false,'Can not cancel',null,errorCodesEnum::CAN_NOT_CANCEL_CONTRACT);
        }

        // 更新贷款合同状态
        $contract->state = loanContractStateEnum::CANCEL;
        $up = $contract->update();
        if( !$up->STS ){
            return new result(false,'DB error',null,errorCodesEnum::DB_ERROR);
        }

        // 更新关联的保险合同状态
        $sql = "update insurance_contract set state='".insuranceContractStateEnum::CANCEL."' where loan_contract_id='".$contract->uid."' ";
        $up = $m_contract->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'DB error',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');
    }


    /** 获取贷款产品绑定的保险产品
     * @param $loan_product_id
     * @return result
     */
    public function getLoanProductBindInsuranceProduct($loan_product_id)
    {
        if( !$loan_product_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $sql = "select i.* from insurance_product_relationship r inner join insurance_product_item i on r.insurance_product_item_id=i.uid left join insurance_product p on i.product_id=p.uid where r.loan_product_id='".$loan_product_id."' and  p.state='".insuranceProductStateEnum::ACTIVE."' ";
        $reader = new ormReader();
        $list = $reader->getRows($sql);
        return new result(true,'success',$list);
    }



    /** 计算逾期还款计划的罚金
     * @param $scheme_id
     * @return int
     */
    public static function calculateSchemaRepaymentPenalties($scheme_id,$term_date=null)
    {

        $penalties = 0;
        $scheme_id = intval($scheme_id);

        $r = new ormReader();
        $sql = "select s.*,c.penalty_rate,c.penalty_divisor_days,c.apply_amount from loan_installment_scheme s inner join loan_contract c on s.contract_id=c.uid  where s.uid='$scheme_id' ";
        $schema = $r->getRow($sql);
        if( !$schema ){
            return $penalties;
        }

        $ref_penalty = $schema['settle_penalty'] - $schema['deduction_penalty'] - $schema['paid_penalty'];
        if( $ref_penalty < 0 ){
            $ref_penalty = 0;
        }

        if( !$schema['penalty_rate'] || $schema['penalty_rate'] <= 0 || $schema['state'] == schemaStateTypeEnum::COMPLETE ){
            return $ref_penalty;
        }

        // 年-月-日
        if( $schema['last_repayment_time'] ){
            // 已还过部分本金
            $penalty_timestamp = strtotime($schema['last_repayment_time']);
            $penalty_day_time = strtotime(date('Y-m-d',$penalty_timestamp));
        }else{
            $penalty_timestamp = $schema['penalty_start_date']?strtotime($schema['penalty_start_date']):0;
            $penalty_day_time = strtotime(date('Y-m-d',$penalty_timestamp));
        }


        if( !$term_date ){
            $today_time = strtotime(date('Y-m-d'));
        }else{
            $today_time = strtotime($term_date);
        }


        if( $penalty_day_time >= $today_time ){
            return $ref_penalty;
        }

        // 计算相差天数
        $days = ceil(($today_time-$penalty_day_time)/96400);
        if( $days <= 0 ){
            return $ref_penalty;
        }

        // 日罚息
        if( $schema['penalty_divisor_days'] <= 0 ){
            $day_rate = $schema['penalty_rate']/100;
        }else{
            $day_rate = $schema['penalty_rate']/$schema['penalty_divisor_days']/100;  // 百分比
        }


        // 合计罚息
        $total_rate = $day_rate*$days;

        // todo 计算各种类型的罚金
        /*switch( $schema['penalty_on'] ){
            case penaltyOnEnum::OVERDUE_PRINCIPAL:
                break;
            case penaltyOnEnum::PRINCIPAL_INTEREST :
                break;
            case penaltyOnEnum::TOTAL :
                break;
            default:
                return $penalties;
        }*/

        // 只有一种罚金基数
        $new_penalty = ($schema['amount']-$schema['actual_payment_amount'])*$total_rate;  // 未还本息产生的罚金
        $penalties = $ref_penalty + $new_penalty;

        if( $penalties < 0 ){
            $penalties = 0;
        }

        return round($penalties,2);

    }


    public function createContractByApply($apply_id,$user_id,$user_name)
    {
        $m_apply = new loan_applyModel();
        $apply = $m_apply->getRow($apply_id);
        if( !$apply->STS ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != loanApplyStateEnum::ALL_APPROVED ){
            return new result(false,'Un-expect handle',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $interest_info = $apply->toArray();

        $loan_params = array(
            'member_id' => $apply->member_id,
            'product_id' => $apply->product_id,
            'amount' => $apply->apply_amount,
            'currency' => $apply->currency,
            'loan_period' => $apply->loan_time,
            'loan_period_unit' => $apply->loan_time_unit,
            'repayment_type' => $apply->repayment_type,
            'repayment_period' => $apply->repayment_period,
            'handle_account_id' => 0,
            'insurance_item_id' => null,
            'application_id' => $apply_id,
            'mortgage_type' => null,
            'guarantee_type' => null,
            'creator_id' => $user_id,
            'creator_name' => $user_name
        );

        $re = self::createContract($loan_params,$interest_info);
        if( !$re->STS ){
            return $re;
        }

        $re_data = $re->DATA;

        $apply->state = loanApplyStateEnum::DONE;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Db error '.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$re_data);

    }


    public static function loanDisbursementSchemaExecute($schema_id)
    {
        $m = new loan_disbursement_schemeModel();
        $schema = $m->getRow($schema_id);
        if( !$schema ){
            return new result(false,'No schema',null,errorCodesEnum::INVALID_PARAM);
        }

        // 检查合同状态
        $contract_info = (new loan_contractModel())->getRow($schema->contract_id);
        if( !$contract_info ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        if(
            $contract_info->state < loanContractStateEnum::PENDING_DISBURSE
            ||  $contract_info->state == loanContractStateEnum::COMPLETE
            || $contract_info->state == loanContractStateEnum::WRITE_OFF
        ){
            return new result(false, 'Invalid contract state', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        // 检查计划状态
        if( $schema->state == schemaStateTypeEnum::COMPLETE || $schema->state == schemaStateTypeEnum::CANCEL ){
            return new result(false,'Invalid state',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if (strtotime($schema->disbursable_date) > time()) {
            return new result(false, 'The planned time has not been reached', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        if( $contract_info->state != loanContractStateEnum::PROCESSING ){
            $contract_info->state = loanContractStateEnum::PROCESSING;
            $contract_info->update_time = Now();
            $rt = $contract_info->update();
            if (!$rt->STS) {
                return new result(false, 'Update contract state failed', null, errorCodesEnum::DB_ERROR);
            }
        }


        $schema->state = schemaStateTypeEnum::GOING;
        $schema->execute_time = Now();
        $rt = $schema->update();
        if (!$rt->STS) {
            return new result(false, 'Update schema state failed', null, errorCodesEnum::DB_ERROR);
        }

        $handler_info = (new member_account_handlerModel())->find(array(
            'uid' => $schema->account_handler_id
        ));


        $disbursement_model = new loan_disbursementModel();
        $disbursement_log = $disbursement_model->newRow();
        $disbursement_log->scheme_id = $schema->uid;
        $disbursement_log->contract_id = $schema->contract_id;
        $disbursement_log->currency = $contract_info->currency;
        $disbursement_log->amount = $schema->amount;
        $disbursement_log->receiver_id = $handler_info?$handler_info['uid']:0;
        $disbursement_log->receiver_type = $handler_info?$handler_info['handler_type']:null;
        $disbursement_log->receiver_name = $handler_info?$handler_info['handler_name']:null;
        $disbursement_log->receiver_phone = $handler_info?$handler_info['handler_phone']:null;
        $disbursement_log->receiver_account = $handler_info?$handler_info['handler_account']:null;
        $disbursement_log->receiver_property = $handler_info?$handler_info['handler_property']:null;
        $disbursement_log->create_time = date("Y-m-d H:i:s");
        $disbursement_log->branch_id = 0;
        $disbursement_log->teller_id = 0;
        $disbursement_log->teller_name = null;
        $disbursement_log->creator_id = 0;
        $disbursement_log->creator_name = 'System';
        $disbursement_log->gl_invoice_id = 0;

        $disbursement_log->state = disbursementStateEnum::GOING;
        $rt = $disbursement_log->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert disbursement log failed', null, errorCodesEnum::DB_ERROR);
        }

        $ret = passbookWorkerClass::disburseLoan($schema_id);  // 转账业务处理

        if ($ret->STS) {
            $disbursement_log->state = disbursementStateEnum::DONE;
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }

            $schema->state = schemaStateTypeEnum::COMPLETE;
            $schema->done_time = date("Y-m-d H:i:s");
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }
            return new result(true);

        } else {

            $disbursement_log->state = disbursementStateEnum::FAILED;
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            $schema->state = schemaStateTypeEnum::FAILURE;
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            return new result(false, 'API failed:'.$ret->MSG, null, errorCodesEnum::API_FAILED, $ret);
        }

    }









}