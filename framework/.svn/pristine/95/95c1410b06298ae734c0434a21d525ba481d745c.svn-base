<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 10:36
 */
class credit_loanControl extends bank_apiControl
{


    public function calculatorOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $loan_amount = $params['amount'];
        if( $loan_amount < 100 ){
            return new result(false,'Amount not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $loan_time = intval($params['loan_period']);  // 单位月
        if( $loan_time < 1 ){
            return new result(false,'Time not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $year_interest = $params['interest']/100;
        if( $year_interest <= 0 ){
            return new result(false,'Interest not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $payment_type = $params['repayment_type'];
        switch( $payment_type ){
            case interestPaymentEnum::FIXED_PRINCIPAL :
                $schema = loan_calculatorClass::fixed_principle_getPaymentSchemaByFixInterest($loan_amount,$year_interest/12,$loan_time,0);
                break;
            case interestPaymentEnum::ANNUITY_SCHEME :
                $schema = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterest($loan_amount,$year_interest/12,$loan_time,0);
                break;
            case interestPaymentEnum::SINGLE_REPAYMENT :
                $schema = loan_calculatorClass::single_repayment_getPaymentSchemaByFixInterest($loan_amount,$year_interest/12,$loan_time,0);
                break;
            case interestPaymentEnum::FLAT_INTEREST :
                $schema = loan_calculatorClass::flat_interest_getPaymentSchemaByFixInterest($loan_amount,$year_interest/12,$loan_time,0);
                break;
            case interestPaymentEnum::BALLOON_INTEREST :
                $schema = loan_calculatorClass::balloon_interest_getPaymentSchemaByFixInterest($loan_amount,$year_interest/12,$loan_time,0);
                break;
            default:
                return new result(false,'Not supported payment type',errorCodesEnum::NOT_SUPPORTED);

        }
        if( !$schema->STS ){
            return new result(false,'Calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $pay_schema = $schema->DATA;

        return new result(true,'success',array(
            'total_summation' => array(
                'payment_period' => ($pay_schema['payment_total']['total_period_pay']),
                'total_interest' => ($pay_schema['payment_total']['total_interest']),
                'payment_total' => ($pay_schema['payment_total']['total_payment']),
            ),
            'payment_schema' => $pay_schema['payment_schema']
        ));

    }


    public function getCreditAndCertListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);

        $credit = memberClass::getCreditBalance($member_id);

        $creditLoan = new credit_loanClass();
        $re = $creditLoan->creditLoanMemberCertDetail($params);
        if( !$re->STS ){
            return $re;
        }
        $product_id = $re->DATA['product_id'];
        $cert_list = $re->DATA['cert_list'];
        return new result(true,'success',array(
            'credit_info' => $credit,
            'product_id' => $product_id,
            'cert_list' => $cert_list
        ));

    }

    public function loanPreviewOp()
    {

        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->loanPreview($params);
        return $re;

    }

    public function getLoanProposeOp()
    {

        $m = new core_definitionModel();
        $rows = $m->select(array(
            'category' => 'loan_use'
        ));
        return new result(true,'success',$rows);
    }

    /** 信用贷提现(合同创建)
     * @return result
     */
    public function creditLoanWithdrawOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->withdraw($params);
        return $re;
    }

    /** 创建合同API 暂时不使用
     * @return result
     */
    public function createContractOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        set_time_limit(120);
        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->createContract($params);
        return $re;

    }

    /** 信用贷合同确认
     * @return result
     */
    public function contractConfirmOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
          return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        if( !$contract_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->confirmContract($contract_id);
        return $re;

    }

    /** 获取绑定的保险产品
     * @return result
     */
    public function getLoanBindInsuranceListOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->getBindInsuranceProduct($params);
        return $re;
    }


    public function getCreditLoanLevelOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $type = $params['level_type'];
        switch( $type ){
            case 0:
                $level_type = creditLevelTypeEnum::MEMBER;
                break;
            case 1:
                $level_type = creditLevelTypeEnum::MERCHANT;
                break;
            default:
                $level_type = 'all';
        }

        $list = credit_loanClass::getCreditLevelList($level_type);
        return new result(true,'success',$list);
    }


    public function creditLimitCalculatorOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $re = loan_baseClass::creditLimitCalculator($params);
        return $re;
    }


    public function creditLoanIndexOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $param = array_merge(array(),$_GET,$_POST);
        $member_id = $param['member_id'];

        $credit_balance = memberClass::getCreditBalance($member_id);


        $product_id = 0;
        $credit_loan_product = credit_loanClass::getProductInfo();
        if( $credit_loan_product ){
            $product_id = $credit_loan_product['uid'];
            //return new result(false,'No release product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        // 获取最低月利率
        $m_rate = new loan_product_size_rateModel();
        $rate_list = $m_rate->getRows(array(
            'product_id' => $product_id
        ));
        $new_value = array();
        foreach( $rate_list as $rate ){
            $interest_rate = $rate['interest_rate'];
            $interest_re = loan_baseClass::interestRateConversion($interest_rate,$rate['interest_rate_unit'],interestRatePeriodEnum::MONTHLY);
            if( $interest_re->STS ){
                $interest_rate = $interest_re->DATA;
            }

            $operate_rate = $rate['operation_fee'];
            $operate_re = loan_baseClass::interestRateConversion($operate_rate,$rate['operation_fee_unit'],interestRatePeriodEnum::MONTHLY);
            if( $operate_re->STS ){
                $operate_rate = $operate_re->DATA;
            }
            $new_value[] = $interest_rate+$operate_rate;

        }
        // 值升序
        asort($new_value);
        $monthly_min_rate = isset($new_value[0])?$new_value[0]:0.00;
        $monthly_min_rate = round($monthly_min_rate,2);
        $monthly_min_rate_desc = $monthly_min_rate.'%';

        // 获取会员下期应还款
        $next_schema = memberClass::getMemberLoanNextRepaymentSchema($member_id);
        if( $next_schema ){
            $next_repayment_amount = $next_schema['amount']-$next_schema['actual_payment_amount'];
            $next_schema['next_repayment_amount'] = $next_repayment_amount;
        }


        return new result(true,'success',array(
            'product_id' => $product_id,
            'credit_info' => $credit_balance,
            'monthly_min_rate' => $monthly_min_rate_desc,
            'next_repayment_schema' => $next_schema
        ));

    }

    public function getProductRateCreditLevelOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $rate_id = $param['rate_id'];
        $m_rate = new loan_product_size_rateModel();
        $rate = $m_rate->getRow($rate_id);
        if( !$rate ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $level = credit_loanClass::getCreditLevelByAmount($rate['loan_size_max'],$rate['currency']);
        return new result(true,'success',$level);
    }

    public function getLoanMaxMonthOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $re = credit_loanClass::getLoanMaxMonthByDefaultWay($product_id);
        return $re;
    }
}