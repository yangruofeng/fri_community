<?php

class gl_accountClass {

    /**
     * 获得收入类的gl_account
     * @param $incomingType
     * @param $businessType
     * @return mixed
     * @throws Exception
     */
    public static function getIncomingAccount($incomingType, $businessType) {
        $account_model = new gl_accountModel();

        // 获得收入类型账户
        $parent_account = $account_model->getRow(array('account_code' => $incomingType));
        if (!$parent_account) {
            throw new Exception("Cannot found account of incoming type - " . $incomingType);
        }

        // 找收入类型账户下面具体的业务类型账户
        $leaf_account = $account_model->getRow(array(
            'account_code' => $businessType,
            'account_parent' => $parent_account->uid
        ));
        if (!$leaf_account) {
            throw new Exception("Cannot found account of business type -  $businessType/$incomingType");
        }

        // 如果业务类型账户没有obj_guid，创建
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }

        return $leaf_account;
    }

    /**
     * 获取系统账户
     * @param $systemAccountCode
     * @return mixed
     * @throws Exception
     */
    public static function getSystemAccount($systemAccountCode) {
        $account_model = new gl_accountModel();

        $account_info = $account_model->getRow(array('account_code' => $systemAccountCode));
        if (!$account_info) {
            throw new Exception("Cannot found system account - " . $systemAccountCode);
        }

        // 如果系统账户没有obj_guid
        if (!$account_info->obj_guid) {
            $account_info->obj_guid = generateGuid($account_info->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $account_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }

        return $account_info;
    }
}