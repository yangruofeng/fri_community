<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 13:41
 */
class member_creditClass
{
    function __construct()
    {

    }

    /** 客户授信
     * @param $member_id
     * @param $credit
     * @return result
     */
    public static function grantCredit($member_id,$credit,$expire_time=null)
    {
        $credit = ceil($credit);
        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        $now = Now();
        if( $member_credit ){

            // 信用增减
            $before_credit = $member_credit->credit;
            $before_credit_balance = $member_credit->credit_balance;
            if( $credit == $before_credit ){
                // 没改变信用值
                return new result(true,'success',$member_credit);
            }

            if( $credit > $before_credit ){

                // 提升信用
                // 自动增加信用余额
                $expend_credit = $before_credit-$before_credit_balance;  // 消耗的信用
                $after_credit_balance = $credit-$expend_credit;

                $member_credit->credit = $credit;
                $member_credit->credit_balance = $after_credit_balance;
                $member_credit->grant_time = $now;
                $member_credit->expire_time = $expire_time;
                $up = $member_credit->update();
                if( !$up->STS ){
                    return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
                }

                $flow_amount = $after_credit_balance-$before_credit_balance;
                $flow = $m_flow->newRow();
                $flow->member_id = $member_id;
                $flow->event_type = creditEventTypeEnum::GRANT;
                $flow->begin_balance = $before_credit_balance;
                $flow->flag = 1;
                $flow->amount = $flow_amount;
                $flow->after_balance = $after_credit_balance;
                $flow->remark = 'Grant add credit balance.';
                $flow->create_time = $now;
                $insert = $flow->insert();
                if( !$insert->STS ){
                    return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
                }

                return new result(true,'success',$member_credit);

            }else{

                // 降低信用
                // 扣减信用余额
                $expend_credit = $before_credit-$before_credit_balance;  // 消耗的信用
                if( $expend_credit >= $credit ){
                    $after_credit_balance = 0;
                }else{
                    $after_credit_balance = $credit-$expend_credit;
                }

                $flow_amount = $before_credit_balance-$after_credit_balance;

                $member_credit->credit = $credit;
                $member_credit->credit_balance = $after_credit_balance;
                $member_credit->grant_time = $now;
                $member_credit->expire_time = $expire_time;
                $up = $member_credit->update();
                if( !$up->STS ){
                    return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
                }

                $flow = $m_flow->newRow();
                $flow->member_id = $member_id;
                $flow->event_type = creditEventTypeEnum::GRANT;
                $flow->begin_balance = $before_credit_balance;
                $flow->flag = -1;
                $flow->amount = $flow_amount;
                $flow->after_balance = $after_credit_balance;
                $flow->remark = 'Grant minus credit balance.';
                $flow->create_time = $now;
                $insert = $flow->insert();
                if( !$insert->STS ){
                    return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
                }

                return new result(true,'success',$member_credit);

            }

        }else{
            // 初次授信
            $member_credit = $m_credit->newRow();
            $member_credit->member_id = $member_id;
            $member_credit->credit = $credit;
            $member_credit->credit_balance = $credit;
            $member_credit->grant_time = $now;
            $member_credit->expire_time = $expire_time;
            $insert = $member_credit->insert();
            if( !$insert->STS ){
                return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
            }

            $flow = $m_flow->newRow();
            $flow->member_id = $member_id;
            $flow->event_type = creditEventTypeEnum::GRANT;
            $flow->begin_balance = 0;
            $flow->flag = 1;
            $flow->amount = $credit;
            $flow->after_balance = $credit;
            $flow->remark = 'Grant add credit balance.';
            $flow->create_time = $now;
            $insert = $flow->insert();
            if( !$insert->STS ){
                return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
            }

            return new result(true,'success',$member_credit);

        }


    }

    /** 公共方法增加信用余额
     * @param $type
     * @param $member_id
     * @param $amount
     * @return result
     */
    public static function addCreditBalance($type,$member_id,$amount)
    {
        switch( $type ){

            case creditEventTypeEnum::CREDIT_LOAN:
                return self::creditLoanUpdateCreditBalance($member_id,$amount,1);
                break;
            default:
                return self::unknownEventUpdateCreditBalance($member_id,$amount,-1);
                break;
        }
    }

    /** 公共方法扣减信用余额
     * @param $type
     * @param $member_id
     * @param $amount
     * @return result
     */
    public static function minusCreditBalance($type,$member_id,$amount)
    {
        switch( $type ){
            case creditEventTypeEnum::CREDIT_LOAN:
                return self::creditLoanUpdateCreditBalance($member_id,$amount,-1);
                break;
            default:
                return self::unknownEventUpdateCreditBalance($member_id,$amount,-1);
                break;
        }
    }

    /** 未定义事件增减余额
     * @param $member_id
     * @param $amount
     * @param $flag
     * @return result
     */
    protected static function unknownEventUpdateCreditBalance($member_id,$amount,$flag)
    {
        $event_type = 'unknown';
        switch( $flag ){
            case 1:
                $remark = 'Unknown event add';
                $re = self::baseUpdateCreditBalanceAdd($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            case -1:
                $remark = 'Unknown event minus';
                $re = self::baseUpdateCreditBalanceMinus($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            case 0:
                $remark = 'Unknown event outstanding';
                $re = self::baseUpdateCreditBalanceOutstanding($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            default:
                return new result(false,'Unsupported',null,errorCodesEnum::NOT_SUPPORTED);
                break;
        }
    }

    /** 信用贷款增减余额
     * @param $member_id
     * @param $amount
     * @param $flag
     * @return result
     */
    protected static function creditLoanUpdateCreditBalance($member_id,$amount,$flag)
    {
        $event_type = creditEventTypeEnum::CREDIT_LOAN;
        switch( $flag ){
            case 1:
                $remark = 'Credit loan repayment';
                $re = self::baseUpdateCreditBalanceAdd($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            case -1:
                $remark = 'Credit loan';
                $re = self::baseUpdateCreditBalanceMinus($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            case 0:
                $remark = 'Credit loan outstanding';
                $re = self::baseUpdateCreditBalanceOutstanding($event_type,$member_id,$amount,$remark);
                return $re;
                break;
            default:
                return new result(false,'Unsupported',null,errorCodesEnum::NOT_SUPPORTED);
                break;
        }

    }


    /** 基础增加信用余额方法
     * @param $event_type
     * @param $member_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceAdd($event_type,$member_id,$amount,$remark)
    {
        $amount = ceil($amount);
        if( $amount <=0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if( !$member_credit ){
            return new result(false,'Un grant credit',null,errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $credit = $member_credit->credit;
        $before_credit_balance = $member_credit->credit_balance;
        $now = Now();

        // 不能超过信用值
        $after_credit_balance = $before_credit_balance+$amount;
        if( $after_credit_balance > $credit ){
            $after_credit_balance = $credit;
        }
        $member_credit->credit_balance = $after_credit_balance;
        $member_credit->update_time = $now;
        $up = $member_credit->update();
        if( !$up->STS ){
            return new result(false,'fail',null,errorCodesEnum::DB_ERROR);
        }

        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 1;
        $flow->amount = $amount;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if( !$insert->STS ){
            return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$member_credit);

    }


    /** 基础扣减信用余额方法
     * @param $event_type
     * @param $member_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceMinus($event_type,$member_id,$amount,$remark)
    {

        $amount = ceil($amount);
        if( $amount <=0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if( !$member_credit ){
            return new result(false,'Un grant credit',null,errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $credit = $member_credit->credit;
        $before_credit_balance = $member_credit->credit_balance;
        $now = Now();

        $after_credit_balance = $before_credit_balance-$amount;
        $member_credit->credit_balance = $after_credit_balance;
        $member_credit->update_time = $now;
        $up = $member_credit->update();
        if( !$up->STS ){
            return new result(false,'fail',null,errorCodesEnum::DB_ERROR);
        }

        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = -1;
        $flow->amount = $amount;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if( !$insert->STS ){
            return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$member_credit);

    }

    /** 待算信用余额基础方法
     * @param $event_type
     * @param $member_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceOutstanding($event_type,$member_id,$amount,$remark)
    {
        $amount = ceil($amount);
        if( $amount <=0 ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if( !$member_credit ){
            return new result(false,'Un grant credit',null,errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $credit = $member_credit->credit;
        $before_credit_balance = $member_credit->credit_balance;
        $now = Now();
        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 0;
        $flow->amount = $amount;
        $flow->after_balance = $before_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if( !$insert->STS ){
            return new result(false,'Grant fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$member_credit);
    }



}