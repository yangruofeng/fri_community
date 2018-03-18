<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 17:04
 */
class bizMemberDepositByPartnerClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $this->biz_code = bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER;
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

    /**
     * @return result
     */
    protected function checkLimit()
    {
        switch( $this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:
                return new result(true);  // TODO: 具体限额检查
            case bizSceneEnum::APP_CO :
                return new result(false, 'Member to member is not supported by APP_CO', null, errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::COUNTER :
                return new result(true);   // 柜台无限
            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Member to member is not supported by BACKOFFICE', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }


    // 不需要检查密码
    public function execute($member_id,$amount,$currency,$account_handler_id,$remark)
    {

        $m_biz = new biz_member_depositModel();
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->member_handler_id = $account_handler_id;
        $biz->remark = $remark;
        $biz->state = depositStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        // 账本执行
        $rt = (new memberDepositByPartnerTradingClass($member_id,$account_handler_id,$amount,$currency))->execute();
        if( !$rt->STS ){
            $biz->state = depositStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $biz->state = depositStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }


}