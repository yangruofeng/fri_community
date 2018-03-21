<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/13
 * Time: 17:34
 */

// 提现
class bizMemberWithdrawToPartnerClass extends bizBaseClass
{

    public function __construct($scene_code)
    {
        $this->biz_code = bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER;
        $this->scene_code = $scene_code;

        $is_open = $this->checkBizOpen($scene_code);
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
    }

    public function checkBizOpen($scene_code)
    {
        return new result(true);
    }


    // 获取业务在场景下的限定值,没有限定返回0
    public function getLimit($scene_code)
    {
        return 0;
    }

    // 检查交易场景限制
    public  function checkLimit($amount,$currency,$scene_code)
    {
        switch($scene_code )
        {
            case bizSceneEnum::APP_MEMBER:
                break;
            case bizSceneEnum::APP_CO :
                break;
            case bizSceneEnum::COUNTER :
                break;
            case bizSceneEnum::BACK_OFFICE :
                break;
        }
        return new result(true);
    }

    public function getCheckStep()
    {
        return array(
            bizCheckTypeEnum::TRADING_PASSWORD
        );
    }


    public function getFee($amount)
    {
        return 0;
    }

    // 业务开始
    public  function bizStart($from_member_id,$amount,$currency,$member_handler_id,$remark,$scene_code)
    {

        // 检查场景限制
        $scene_limit = $this->getLimit($scene_code);
        if( $scene_limit && $amount > $scene_limit ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        // 检查业务限制
        $chk = $this->checkLimit($amount,$currency,$scene_code);
        if( !$chk->STS ){
            return $chk;
        }

        // 检查余额
        $objectMember = new objectMemberClass($from_member_id);
        $cny_balance = $objectMember->getSavingsAccountBalance();
        if( $cny_balance[$currency] < $amount ){
            return new result(false,'Balance not enough',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $m_biz = new biz_member_withdrawModel();
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $from_member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = $this->getFee($amount);
        $biz->member_handler_id = $member_handler_id;
        $biz->remark = $remark;
        $biz->scene_code = $scene_code;
        $biz->state = withdrawStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }

    // 身份步骤检查
    public function checkAuthentication($param)
    {

    }


    public function checkTradingPassword($biz_id,$password)
    {
        $m_biz = new biz_member_withdrawModel();
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $member_id = $biz->member_id;
        $objectMember = new objectMemberClass($member_id);
        $chk = $objectMember->checkTradingPassword($password);
        return $chk;
    }


    // 业务提交
    public  function bizSubmit($biz_id)
    {

        $m_biz = new biz_member_withdrawModel();
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $member_id = $biz->member_id;
        $amount = $biz->amount;
        $currency = $biz->currency;
        $account_handler_id = $biz->member_handler_id;

        $re = passbookWorkerClass::memberWithdrawToPartner($member_id,$account_handler_id,$amount,$currency);
        if( !$re->STS ){
            $biz->update_time = Now();
            $biz->state = withdrawStateEnum::FAIL;
            $biz->update();
            return $re;
        }else{
            $biz->state = withdrawStateEnum::DONE;
            $biz->update_time = Now();
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success');


    }










}