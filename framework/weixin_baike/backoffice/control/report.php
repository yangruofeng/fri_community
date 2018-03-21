<?php

class reportControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report");
    }

    /**
     * 会员列表
     */
    public function clientListOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('client');
    }

    /**
     * 会员列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $search_text = trim($p['search_text']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT cm.*,la.uid account_id,la.credit FROM " .
            "client_member cm INNER JOIN loan_account la ON la.obj_guid = cm.obj_guid " .
            "WHERE (cm.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($search_text) {
            $sql .= ' AND (cm.display_name like "%' . $search_text . '%" OR cm.obj_guid = "' . $search_text . '")';
        }
        $sql .= ' ORDER BY cm.uid DESC';
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $account_ids = array_column($rows, 'account_id');
            $sql = "SELECT account_id,COUNT(uid) loan_number,SUM(apply_amount) loan_amount FROM loan_contract WHERE account_id IN (" . implode(',', $account_ids) . ") AND state > " . loanContractStateEnum::CREATE . " GROUP BY account_id";
            $loan_statistics_1 = $r->getRows($sql);
            $loan_statistics_1 = resetArrayKey($loan_statistics_1, 'account_id');

            $state = '(' . implode(',', array(schemaStateTypeEnum::CREATE, schemaStateTypeEnum::GOING, schemaStateTypeEnum::FAILURE)) . ')';
            $sql = "SELECT lc.account_id,SUM(lis.receivable_interest + lis.receivable_operation_fee) total_interest," .
                "SUM(lis.receivable_admin_fee) total_admin_fee," .
                "SUM(CASE WHEN lis.state IN $state THEN (lis.receivable_principal) ELSE 0 END) receivable_principal," .
                "SUM(CASE WHEN lis.state IN $state THEN (lis.receivable_principal + lis.receivable_interest + lis.receivable_operation_fee) ELSE 0 END) unpaid_amount " .
                "FROM loan_installment_scheme lis INNER JOIN " .
                "loan_contract lc ON lis.contract_id = lc.uid " .
                "WHERE lc.account_id IN (" . implode(',', $account_ids) . ") GROUP BY lc.account_id";
            $loan_statistics_2 = $r->getRows($sql);
            $loan_statistics_2 = resetArrayKey($loan_statistics_2, 'account_id');

            foreach ($rows as $key => $row) {
                $account_id = $row['account_id'];
                $row['loan_number'] = $loan_statistics_1[$account_id]['loan_number'];
                $row['loan_amount'] = $loan_statistics_1[$account_id]['loan_amount'];
                $row['total_interest'] = $loan_statistics_2[$account_id]['total_interest'];
                $row['total_admin_fee'] = $loan_statistics_2[$account_id]['total_admin_fee'];
                $row['receivable_principal'] = $loan_statistics_2[$account_id]['receivable_principal'];
                $row['unpaid_amount'] = $loan_statistics_2[$account_id]['unpaid_amount'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            'statistics' => $this->getClientStatistics()
        );
    }

    /**
     * 会员统计
     * @return array|ormDataRow
     */
    private function getClientStatistics()
    {
        $r = new ormReader();
        $sql = "SELECT COUNT(cm.uid) client_quantity,SUM(la.credit) sum_credit FROM " .
            "client_member cm LEFT JOIN loan_account la ON la.obj_guid = cm.obj_guid ";
        $client_statistics = $r->getRow($sql);
        if ($client_statistics['client_quantity'] == 0) return array();
        $client_statistics['avg_credit'] = round($client_statistics['sum_credit'] / $client_statistics['client_quantity'], 2);

        $sql = "SELECT COUNT(uid) loan_number,SUM(apply_amount) loan_amount FROM loan_contract WHERE state > " . loanContractStateEnum::CREATE;
        $loan = $r->getRow($sql);
        $client_statistics['avg_loan_number'] = round($loan['loan_number'] / $client_statistics['client_quantity'], 2);
        $client_statistics['avg_loan_amount'] = round($loan['loan_amount'] / $client_statistics['client_quantity'], 2);

        $time = date('Y-m-d', time());
        $sql = "SELECT SUM(uid) new_client FROM client_member WHERE create_time >= '" . $time . "'";
        $new_client = $r->getOne($sql);
        $client_statistics['new_client'] = $new_client;

        return $client_statistics;
    }

    /**
     * client detail
     */
    public function clientDetailOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $uid = intval($_GET['uid']);
        Tpl::output("uid", $uid);
        Tpl::showpage('client.detail');
    }

    /**
     * Client Detail
     * @param $p
     * @return array
     */
    public function getClientDetailOp($p)
    {
        $uid = intval($p['uid']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();

        $sql = "SELECT cm.*,la.uid account_id,la.credit FROM " .
            "client_member cm INNER JOIN loan_account la ON la.obj_guid = cm.obj_guid " .
            "WHERE cm.uid = " . $uid;
        $client_info = $r->getRow($sql);

        $sql = "SELECT COUNT(uid) loan_number,SUM(apply_amount) loan_amount FROM loan_contract WHERE account_id = " . $client_info['account_id'] . " AND state > " . loanContractStateEnum::CREATE . " AND (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        $loan_statistics_1 = $r->getRow($sql);

        $state = '(' . implode(',', array(schemaStateTypeEnum::CREATE, schemaStateTypeEnum::GOING, schemaStateTypeEnum::FAILURE)) . ')';
        $sql = "SELECT SUM(lis.receivable_interest + lis.receivable_operation_fee) total_interest," .
            "SUM(lis.receivable_admin_fee) total_admin_fee," .
            "SUM(CASE WHEN lis.state IN $state THEN (lis.receivable_principal) ELSE 0 END) receivable_principal," .
            "SUM(CASE WHEN lis.state IN $state THEN (lis.receivable_principal + lis.receivable_interest + lis.receivable_operation_fee) ELSE 0 END) unpaid_amount " .
            "FROM loan_installment_scheme lis INNER JOIN " .
            "loan_contract lc ON lis.contract_id = lc.uid " .
            "WHERE lc.account_id = " . $client_info['account_id'] . " AND (lc.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        $loan_statistics_2 = $r->getRow($sql);
        $client_info['loan_number'] = $loan_statistics_1['loan_number'];
        $client_info['loan_amount'] = $loan_statistics_1['loan_amount'];
        $client_info['total_interest'] = $loan_statistics_2['total_interest'];
        $client_info['total_admin_fee'] = $loan_statistics_2['total_admin_fee'];
        $client_info['receivable_principal'] = $loan_statistics_2['receivable_principal'];
        $client_info['unpaid_amount'] = $loan_statistics_2['unpaid_amount'];

        $sql = "SELECT lc.*,lp.product_name"
            . " FROM loan_contract lc"
            . " LEFT JOIN loan_product lp ON lc.product_id = lp.uid"
            . " WHERE lc.account_id = " . $client_info['account_id'] . " AND lc.state > " . loanContractStateEnum::CANCEL . " AND (lc.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_ids = array_column($rows, 'uid');
            $contract_id_str = '(' . implode(',', $contract_ids) . ')';
            $sql = "SELECT contract_id,SUM(amount) disbursement_amount FROM loan_disbursement_scheme WHERE state = " . schemaStateTypeEnum::COMPLETE . " AND contract_id IN $contract_id_str GROUP BY contract_id";
            $disbursement_arr = $r->getRows($sql);
            $disbursement_arr = resetArrayKey($disbursement_arr, 'contract_id');

            $sql = "SELECT contract_id,SUM(amount) receive_amount,SUM(CASE WHEN state = " . schemaStateTypeEnum::COMPLETE . " THEN amount ELSE 0 END) received_amount,SUM(CASE WHEN state != " . schemaStateTypeEnum::COMPLETE . " THEN amount ELSE 0 END) unreceived_amount FROM loan_installment_scheme WHERE contract_id IN $contract_id_str GROUP BY contract_id";
            $installment_arr = $r->getRows($sql);
            $installment_arr = resetArrayKey($installment_arr, 'contract_id');

            foreach ($rows as $key => $row) {
                $contract_id = $row['uid'];
                $row['disbursement_amount'] = $disbursement_arr[$contract_id]['disbursement_amount'];
                $row['receive_amount'] = $installment_arr[$contract_id]['receive_amount'];
                $row['received_amount'] = $installment_arr[$contract_id]['received_amount'];
                $row['unreceived_amount'] = $installment_arr[$contract_id]['unreceived_amount'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "client_info" => $client_info
        );
    }

    /**
     * 合同列表
     */
    public function contractListOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('contract');
    }

    /**
     * 获取合同列表
     * @param $p
     * @return array
     */
    public function getContractListOp($p)
    {
        $r = new ormReader();
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $sql = "SELECT lc.*,cm.display_name,lp.product_name FROM loan_contract lc"
            . " LEFT JOIN loan_account la ON lc.account_id = la.uid"
            . " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " LEFT JOIN loan_product lp ON lc.product_id = lp.uid"
            . " WHERE lc.state != " . loanContractStateEnum::CANCEL . " AND (cm.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn = '" . trim($p['search_text']) . "'";
        }

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_ids = array_column($rows, 'uid');
            $contract_id_str = '(' . implode(',', $contract_ids) . ')';
            $sql = "SELECT contract_id,SUM(amount) disbursement_amount FROM loan_disbursement_scheme WHERE state = " . schemaStateTypeEnum::COMPLETE . " AND contract_id IN $contract_id_str GROUP BY contract_id";
            $disbursement_arr = $r->getRows($sql);
            $disbursement_arr = resetArrayKey($disbursement_arr, 'contract_id');

            $sql = "SELECT contract_id,SUM(amount) receive_amount,SUM(CASE WHEN state = " . schemaStateTypeEnum::COMPLETE . " THEN amount ELSE 0 END) received_amount,SUM(CASE WHEN state != " . schemaStateTypeEnum::COMPLETE . " THEN amount ELSE 0 END) unreceived_amount FROM loan_installment_scheme WHERE contract_id IN $contract_id_str GROUP BY contract_id";
            $installment_arr = $r->getRows($sql);
            $installment_arr = resetArrayKey($installment_arr, 'contract_id');

            $current_amount = array(
                'apply_amount' => 0,
                'disbursement_amount' => 0,
                'receive_amount' => 0,
                'received_amount' => 0,
                'unreceived_amount' => 0,
            );
            foreach ($rows as $key => $row) {
                $contract_id = $row['uid'];
                $row['disbursement_amount'] = $disbursement_arr[$contract_id]['disbursement_amount'];
                $row['receive_amount'] = $installment_arr[$contract_id]['receive_amount'];
                $row['received_amount'] = $installment_arr[$contract_id]['received_amount'];
                $row['unreceived_amount'] = $installment_arr[$contract_id]['unreceived_amount'];
                $current_amount['apply_amount'] += $row['apply_amount'];
                $current_amount['disbursement_amount'] += $row['disbursement_amount'];
                $current_amount['receive_amount'] += $row['receive_amount'];
                $current_amount['received_amount'] += $row['received_amount'];
                $current_amount['unreceived_amount'] += $row['unreceived_amount'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "current_amount" => $current_amount,
            "amount" => $this->getContractAmount($p)
        );
    }

    /**
     * 获取合同总额
     * @param $p
     * @return array
     */
    private function getContractAmount($p)
    {
        $r = new ormReader();
        $sql = "SELECT SUM(apply_amount) apply_amount FROM loan_contract WHERE state != " . loanContractStateEnum::CANCEL;
        if (trim($p['search_text'])) {
            $sql .= " AND contract_sn = '" . trim($p['search_text']) . "'";
        }
        $apply_amount = $r->getOne($sql);

        $sql = "SELECT SUM(lds.amount) disbursement_amount FROM loan_disbursement_scheme lds INNER JOIN loan_contract lc ON lds.contract_id = lc.uid WHERE lds.state = " . schemaStateTypeEnum::COMPLETE;
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn = '" . trim($p['search_text']) . "'";
        }
        $disbursement_amount = $r->getOne($sql);

        $sql = "SELECT SUM(lis.amount) receive_amount,SUM(CASE WHEN lis.state = " . schemaStateTypeEnum::COMPLETE . " THEN lis.amount ELSE 0 END) received_amount,SUM(CASE WHEN lis.state != " . schemaStateTypeEnum::COMPLETE . " THEN lis.amount ELSE 0 END) unreceived_amount FROM loan_installment_scheme lis INNER JOIN loan_contract lc ON lis.contract_id = lc.uid WHERE 1=1";
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn = '" . trim($p['search_text']) . "'";
        }
        $installment_arr = $r->getRow($sql);
        $data = array(
            'apply_amount' => $apply_amount,
            'disbursement_amount' => $disbursement_amount,
            'receive_amount' => $installment_arr['receive_amount'],
            'received_amount' => $installment_arr['received_amount'],
            'unreceived_amount' => $installment_arr['unreceived_amount'],
        );
        return $data;
    }

    /**
     * 授信列表
     */
    public function creditListOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('credit');
    }

    /**
     * 获取信用贷授权列表
     * @param $p
     * @return array
     */
    public function getCreditListOp($p)
    {
        $r = new ormReader();
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $sql = "SELECT la.*,cm.display_name,uu.user_name FROM loan_approval la"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " LEFT JOIN um_user uu ON la.operator_id = uu.uid"
            . " WHERE (la.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['search_text'])) {
            $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY la.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    /**
     * 贷款产品
     */
    public function loanListOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('loan');
    }

    /**
     * 贷款产品列表
     * @param $p
     * @return array
     */
    public function getLoanListOp($p)
    {
        $r = new ormReader();
        $m_loan_product = M('loan_product');
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $sql = "SELECT MAX(uid) uid FROM loan_product WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND state < 40";
        if (trim($p['search_text'])) {
            $sql .= " AND product_name LIKE '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " GROUP BY product_key ORDER BY uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $product_ids = array_column($rows, 'uid');
            $sql = "SELECT * FROM loan_product WHERE uid IN (" . implode(',', $product_ids) . ") ORDER BY uid DESC";
            $product_list = $r->getRows($sql);
            foreach ($product_list as $key => $product) {
                $product_key = $product['product_key'];
                $product_count = $m_loan_product->field('count(*) count')->find(array('product_key' => $product_key));
                $product_list[$key]['count'] = $product_count;
                $product_contract = $this->getProductContractByKey($product_key);
                $product_list[$key]['loan_contract'] = $product_contract['loan_count'] ?: 0;
                $product_list[$key]['loan_client'] = $product_contract['loan_client'] ?: 0;
                $product_list[$key]['loan_ceiling'] = $product_contract['loan_ceiling'] ?: 0;
                $product_list[$key]['loan_balance'] = $product_contract['loan_balance'] ?: 0;
            }
        } else {
            $product_list = array();
        };

        return array(
            "sts" => true,
            "data" => $product_list,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    /**
     * 获取系列产品合同信息
     * @param $key
     * @return ormDataRow
     */
    private function getProductContractByKey($key)
    {
        $r = new ormReader();
        $sql = "select uid from loan_product WHERE product_key = '" . $key . "'";
        $product_ids = $r->getRows($sql);
        if (!$product_ids) {
            return array();
        }
        $product_id_str = '(' . implode(',', array_column($product_ids, 'uid')) . ')';
        $sql = "SELECT COUNT(uid) loan_count,SUM(apply_amount) loan_ceiling,SUM(receivable_admin_fee-loss_admin_fee) admin_fee,SUM(receivable_interest+receivable_operation_fee+receivable_annual_fee+receivable_penalty-loss_principal-loss_interest-loss_operation_fee-loss_annual_fee-loss_penalty) loan_interest FROM loan_contract WHERE product_id IN " . $product_id_str;
        $product_contract = $r->getRow($sql);
        $sql = "SELECT SUM(lr.amount) repayment FROM loan_repayment AS lr INNER JOIN loan_contract AS lc ON lr.contract_id = lc.uid WHERE lr.state = 100 AND lc.product_id IN " . $product_id_str;
        $repayment = $r->getOne($sql);
        $loan_balance = $product_contract['loan_ceiling'] + $product_contract['loan_interest'] - $repayment;
        $sql = "SELECT COUNT(member.uid) loan_client FROM loan_contract AS contract"
            . " INNER JOIN loan_account AS account ON contract.account_id = account.uid"
            . " INNER JOIN client_member AS member ON account.obj_guid = member.obj_guid WHERE contract.product_id IN " . $product_id_str . " GROUP BY member.uid";
        $loan_client = $r->getOne($sql);
        $product_contract['loan_balance'] = $loan_balance;
        $product_contract['loan_client'] = $loan_client;
        return $product_contract;
    }

    public function repaymentListOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('repayment');
    }

    public function getRepaymentListOp($p)
    {
        $r = new ormReader();
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $sql = "SELECT lr.*,lc.contract_sn,lis.scheme_name,sb.branch_name FROM loan_repayment lr"
            . " INNER JOIN loan_contract lc ON lr.contract_id = lc.uid"
            . " INNER JOIN loan_installment_scheme lis ON lr.scheme_id = lis.uid"
            . " LEFT JOIN site_branch sb ON lr.branch_id = sb.uid"
            . " WHERE lr.state = 100 AND (lr.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['search_text'])) {
            $sql .= " AND (lr.payer_name LIKE '%" . trim($p['search_text']) . "%' OR lc.contract_sn LIKE '%" . trim($p['search_text']) . "%')";
        }
        $sql .= " ORDER BY lr.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }
}
