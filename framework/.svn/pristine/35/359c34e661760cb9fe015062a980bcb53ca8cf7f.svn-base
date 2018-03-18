<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class branchClass
{

    public function __construct()
    {
    }

    /**
     * 获取分部信息
     * @param $uid
     * @return array|bool|mixed|null
     */
    public function getBranchInfo($uid)
    {
        $m_site_branch = M('site_branch');
        $branch_info = $m_site_branch->find(array('uid' => $uid));
        if (!$branch_info) {
            return array();
        }

        $m_site_branch_limit = M('site_branch_limit');
        $limit_list = $m_site_branch_limit->select(array('branch_id' => $uid));
        $limit_arr = array();
        foreach ($limit_list as $val) {
            $limit_arr[$val['limit_key']] = array(
                'max_per_day' => $val['max_per_day'],
                'max_per_time' => $val['max_per_time']
            );
        }

        $branch_info['limit_arr'] = $limit_arr;
        $m_site_branch_bank = M('site_branch_bank');
        $bank = $m_site_branch_bank->find(array('branch_id' => $uid));
        $branch_info['bank_id'] = $bank['bank_id'];
        return $branch_info;
    }

    /**
     * 添加branch
     * @param $p
     * @return result
     */
    public function addBranch($p)
    {
        $branch_code = trim($p['branch_code']);
        $branch_name = trim($p['branch_name']);
        $address_id = intval($p['address_id']);
        $address_region = trim($p['address_region']);
        $address_detail = trim($p['address_detail']);
        $coord_x = $p['coord_x'];
        $coord_y = $p['coord_y'];
        $contact_phone = trim($p['contact_phone']);
        $manager = intval($p['manager']);
        $status = intval($p['status']);
        if (empty($branch_code) || empty($branch_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }

        $m_site_branch = M('site_branch');

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_site_branch->newRow();
            $row->branch_code = $branch_code;
            $row->branch_name = $branch_name;
            $row->address_id = $address_id;
            $row->address_region = $address_region;
            $row->address_detail = $address_detail;
            $row->coord_x = $coord_x;
            $row->coord_y = $coord_y;
            $row->contact_phone = $contact_phone;
            $row->manager = $manager;
            $row->status = $status;
            $row->creator_id = intval($p['creator_id']);
            $row->creator_name = trim($p['creator_name']);
            $row->create_time = Now();
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt->MSG);
            }

            $row->obj_guid = generateGuid($rt->AUTO_ID, objGuidTypeEnum::SITE_BRANCH);
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $limit_arr = array(
                limitKeyEnum::LIMIT_LOAN => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_LOAN]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_LOAN]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_DEPOSIT => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_DEPOSIT]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_DEPOSIT]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_EXCHANGE => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_EXCHANGE]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_EXCHANGE]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_WITHDRAW => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_WITHDRAW]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_WITHDRAW]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_TRANSFER => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_TRANSFER]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_TRANSFER]['max_per_time'],
                ),
            );

            $m_site_branch_limit = M('site_branch_limit');
            foreach ($limit_arr as $key => $limit) {
                $row = $m_site_branch_limit->newRow();
                $row->branch_id = $rt->AUTO_ID;
                $row->limit_key = $key;
                $row->max_per_day = round($limit['max_per_day'], 2);
                $row->max_per_time = round($limit['max_per_time'], 2);
                $row->creator_id = intval($p['creator_id']);
                $row->creator_name = trim($p['creator_name']);
                $row->create_time = Now();
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed--' . $rt_2->MSG);
                }
            }

            $bank_id = intval($p['bank_id']);
            if ($bank_id) {
                $m_site_branch_bank = M('site_branch_bank');
                $row = $m_site_branch_bank->newRow();
                $row->branch_id = $rt->AUTO_ID;
                $row->bank_id = $bank_id;
                $rt_3 = $row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed--' . $rt_3->MSG);
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
     * 编辑branch
     * @param $p
     * @return result
     * @throws Exception
     */
    public function editBranch($p)
    {
        $uid = intval($p['uid']);
        $branch_code = trim($p['branch_code']);
        $branch_name = trim($p['branch_name']);
        $address_id = intval($p['address_id']);
        $address_region = trim($p['address_region']);
        $address_detail = trim($p['address_detail']);
        $coord_x = $p['coord_x'];
        $coord_y = $p['coord_y'];
        $contact_phone = trim($p['contact_phone']);
        $manager = intval($p['manager']);
        $status = intval($p['status']);
        if (empty($branch_code) || empty($branch_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }
        $m_site_branch = M('site_branch');
        $row = $m_site_branch->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id');
        }
        $chk_code = $m_site_branch->find(array('branch_code' => $branch_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Branch Code Exist!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->branch_code = $branch_code;
            $row->branch_name = $branch_name;
            $row->address_id = $address_id;
            $row->address_region = $address_region;
            $row->address_detail = $address_detail;
            $row->coord_x = $coord_x;
            $row->coord_y = $coord_y;
            $row->contact_phone = $contact_phone;
            $row->manager = $manager;
            $row->status = $status;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt->MSG);
            }

            $limit_arr = array(
                limitKeyEnum::LIMIT_LOAN => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_LOAN]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_LOAN]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_DEPOSIT => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_DEPOSIT]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_DEPOSIT]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_EXCHANGE => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_EXCHANGE]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_EXCHANGE]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_WITHDRAW => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_WITHDRAW]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_WITHDRAW]['max_per_time'],
                ),
                limitKeyEnum::LIMIT_TRANSFER => array(
                    'max_per_day' => $p[limitKeyEnum::LIMIT_TRANSFER]['max_per_day'],
                    'max_per_time' => $p[limitKeyEnum::LIMIT_TRANSFER]['max_per_time'],
                ),
            );

            $m_site_branch_limit = M('site_branch_limit');
            foreach ($limit_arr as $key => $limit) {
                $row = $m_site_branch_limit->getRow(array('limit_key' => $key, 'branch_id' => $uid));
                if ($row) {
                    $row->max_per_day = round($limit['max_per_day'], 2);
                    $row->max_per_time = round($limit['max_per_time'], 2);
                    $row->update_time = Now();
                    $rt_2 = $row->update();
                } else {
                    $row = $m_site_branch_limit->newRow();
                    $row->branch_id = $uid;
                    $row->limit_key = $key;
                    $row->max_per_day = round($limit['max_per_day'], 2);
                    $row->max_per_time = round($limit['max_per_time'], 2);
                    $row->creator_id = intval($p['creator_id']);
                    $row->creator_name = trim($p['creator_name']);
                    $row->create_time = Now();
                    $rt_2 = $row->insert();
                }

                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit failed--' . $rt_2->MSG);
                }
            }

            $m_site_branch_bank = M('site_branch_bank');
            $rt_4 = $m_site_branch_bank->delete(array('branch_id' => $uid));
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_4->MSG);
            }
            $bank_id = intval($p['bank_id']);
            if ($bank_id) {
                $row = $m_site_branch_bank->newRow();
                $row->branch_id = $rt->AUTO_ID;
                $row->bank_id = $bank_id;
                $rt_3 = $row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit failed--' . $rt_3->MSG);
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 删除branch
     * @param $uid
     * @return result
     */
    public function deleteBranch($uid)
    {
        $m_site_branch = M('site_branch');
        $m_site_depart = M('site_depart');
        $chk_depart = $m_site_depart->getRow(array('branch_id' => $uid));
        if ($chk_depart) {
            return new result(false, 'The branch has departments');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $m_site_branch->delete(array('uid' => $uid));
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt->MSG);
            }

            $m_site_branch_limit = M('site_branch_limit');
            $rt_1 = $m_site_branch_limit->delete(array('branch_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_1->MSG);
            }

            $m_site_branch_bank = M('site_branch_bank');
            $rt_2 = $m_site_branch_bank->delete(array('branch_id' => $uid));
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_2->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Delete successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public static function getGUID($branchId) {
        $branch_model = new site_branchModel();
        $branch_info = $branch_model->getRow($branchId);
        if (!$branch_info) throw new Exception("Branch $branchId not found");

        if (!$branch_info->obj_guid) {
            $branch_info->obj_guid = generateGuid($branch_info->uid, objGuidTypeEnum::SITE_BRANCH);
            $ret = $branch_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for branch failed - " . $ret->MSG);
            }
        }

        return $branch_info->obj_guid;
    }
}