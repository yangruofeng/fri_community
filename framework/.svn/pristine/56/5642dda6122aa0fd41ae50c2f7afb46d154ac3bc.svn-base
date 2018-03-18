<?php

class bizMemberToMemberClass extends bizBaseClass
{


    public function __construct($scene_code)
    {
        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER;

        $is_open = $this->checkBizOpen($scene_code);
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
    }


    public function checkBizOpen($scene_code)
    {
        return new result(true);
    }

    public function getLimit()
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


    public function bizStart($from_member_id,$to_member_id,$amount,$currency,$remark)
    {

    }

    // 身份步骤检查
    public function checkAuthentication($param)
    {

    }


    public function bizSubmit($biz_id)
    {
        $from_member_id = 0;
        $to_member_id = 0;
        $amount = 0;
        $currency = null;
        $re = passbookWorkerClass::memberTransferToMember($from_member_id,$to_member_id,$amount,$currency);
        if( !$re->STS ){

        }else{

        }
    }


}