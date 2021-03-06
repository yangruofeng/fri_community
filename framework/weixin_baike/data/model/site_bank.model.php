<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class site_bankModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('site_bank');
    }

    /**
     * 添加收款账号
     * @param $p
     * @return result
     */
    public function addReceiveAccount($p)
    {
        $bank_code = trim($p['bank_code']);
        $currency = trim($p['currency']);
        $bank_account_no = trim($p['bank_account_no']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_phone = trim($p['bank_account_phone']);
        $account_state = intval($p['account_state']);
        $allow_client_deposit = intval($p['allow_client_deposit']);
        $branch_ids = $p['branch_id'];
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);

        $chk_account = $this->find(array('bank_code' => $bank_code, 'bank_account_no' => $bank_account_no));
        if ($chk_account) {
            return new result(false, 'The account already exists!');
        }

        $m_common_bank_lists = M('common_bank_lists');
        $bank_info = $m_common_bank_lists->find(array('bank_code' => $bank_code));
        $bank_name = $bank_info['bank_name'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->newRow();
            $row->bank_code = $bank_code;
            $row->bank_name = $bank_name;
            $row->currency = $currency;
            $row->bank_account_no = $bank_account_no;
            $row->bank_account_name = $bank_account_name;
            $row->bank_account_phone = $bank_account_phone;
            $row->account_state = $account_state;
            $row->allow_client_deposit = $allow_client_deposit;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed!' . $rt->MSG);
            }

            $row->obj_guid = generateGuid($rt->AUTO_ID, objGuidTypeEnum::BANK_ACCOUNT);
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $m_site_bank_branch = M('site_bank_branch');
            foreach ($branch_ids as $branch_id) {
                $row = $m_site_bank_branch->newRow();
                $row->bank_id = $rt->AUTO_ID;
                $row->branch_id = $branch_id;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed!' . $rt_2->MSG);
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 编辑收款账号
     * @param $p
     * @return result
     */
    public function editReceiveAccount($p)
    {
        $uid = intval($p['uid']);
        $bank_code = trim($p['bank_code']);
        $currency = trim($p['currency']);
        $bank_account_no = trim($p['bank_account_no']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_phone = trim($p['bank_account_phone']);
        $account_state = intval($p['account_state']);
        $allow_client_deposit = intval($p['allow_client_deposit']);
        $branch_ids = $p['branch_id'];

        $chk_account = $this->find(array('bank_code' => $bank_code, 'bank_account_no' => $bank_account_no, 'uid' => array('neq', $uid)));
        if ($chk_account) {
            return new result(false, 'The account already exists!');
        }

        $m_common_bank_lists = M('common_bank_lists');
        $bank_info = $m_common_bank_lists->find(array('bank_code' => $bank_code));
        $bank_name = $bank_info['bank_name'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->getRow($uid);
            $row->bank_code = $bank_code;
            $row->bank_name = $bank_name;
            $row->currency = $currency;
            $row->bank_account_no = $bank_account_no;
            $row->bank_account_name = $bank_account_name;
            $row->bank_account_phone = $bank_account_phone;
            $row->account_state = $account_state;
            $row->allow_client_deposit = $allow_client_deposit;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Update failed!' . $rt->MSG);
            }

            $m_site_bank_branch = M('site_bank_branch');
            $rt_1 = $m_site_bank_branch->delete(array('bank_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Update failed!' . $rt_1->MSG);
            }

            foreach ($branch_ids as $branch_id) {
                $row = $m_site_bank_branch->newRow();
                $row->bank_id = $uid;
                $row->branch_id = $branch_id;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Update failed!' . $rt_2->MSG);
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Update successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public function deleteReceiveAccount($uid)
    {
        $row = $this->getRow($uid);
        if (!$row) {
            return new result(true, 'Update successful!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $rt = $row->delete();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(true, 'Remove failed!');
            }

            $m_site_bank_branch = M('site_bank_branch');
            $rt_1 = $m_site_bank_branch->delete(array('bank_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Remove failed!' . $rt_1->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Remove successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

}