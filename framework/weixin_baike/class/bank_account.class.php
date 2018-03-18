<?php

class bank_accountClass {
    public static function getGUID($accountId) {
        $account_model = new site_bankModel();
        $account_info = $account_model->getRow($accountId);
        if (!$account_info) throw new Exception("Bank account $accountId not found");

        if (!$account_info->obj_guid) {
            $account_info->obj_guid = generateGuid($account_info->uid, objGuidTypeEnum::BANK_ACCOUNT);
            $ret = $account_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for bank account failed - " . $ret->MSG);
            }
        }

        return $account_info->obj_guid;
    }
}