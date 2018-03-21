<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/12
 * Time: 14:08
 */
class objectMemberClass extends objectBaseClass
{
    public $member_id = null;
    public $trading_password = null;

    public function __construct($member_id)
    {

        $this->_initObject($member_id);
    }

    public function checkValid()
    {
        // todo 检查合法性
        return new result(true);
    }


    protected function _initObject($member_id)
    {
        $m = new memberModel();
        $member = $m->getRow($member_id);
        if( !$member ){
            throw new Exception('Member not found');
        }
        $this->object_id = $member['obj_guid'];
        $this->object_type = objGuidTypeEnum::CLIENT_MEMBER;
        $this->object_info = $member;
        $this->trading_password = $member['trading_password'];
        $this->member_id = $member->uid;
    }


    /** 验证交易密码
     * @param $input_pwd
     * @return result
     */
    public function checkTradingPassword($input_pwd)
    {
        if( $this->trading_password && md5($input_pwd) != $this->trading_password  ){
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }
        return new result(true);
    }


    /** 获取储蓄账户
     * @return passbookClass
     */
    public function getSavingsPassbook()
    {
        return passbookClass::getSavingsPassbookOfMemberGUID($this->object_id);
    }

    /**
     * 获取储蓄账户余额
     * @return mixed
     */
    public function getSavingsAccountBalance()
    {
        $passbook = $this->getSavingsPassbook();
        $cny_balance = $passbook->getAccountBalance();
        return $cny_balance;
    }







}