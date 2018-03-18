<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class loan_accountClass
{
    private $account_info;

    public function __construct($accountInfo)
    {
        $this->account_info = $accountInfo;
    }

    public function getSavingsGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                // 储蓄账户直接是loan_account的obj_guid
                return $this->account_info->obj_guid;
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getShortLoanGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getShortLoanGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getLongLoanGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getLongLoanGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getShortDepositGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getShortDepositGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getLongDepositGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getLongDepositGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }
}
