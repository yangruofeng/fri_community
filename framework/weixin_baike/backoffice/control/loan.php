<?php

class loanControl extends baseControl
{

    public function __construct()
    {
        parent::__construct();
        Language::read('enum,loan,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "User List");
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output("verify_field", $verify_field);

        // 客户类型语言包
        $client_type_lang = enum_langClass::getClientTypeLang();
        Tpl::output('client_type_lang', $client_type_lang);
        Tpl::setDir("loan");
    }

    public function creditOp()
    {
        Tpl::showPage("credit");
    }

    public function getCreditListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT client.*,loan.uid as load_uid,loan.allow_multi_contract,c.credit,c.credit_balance FROM client_member as client left join loan_account as loan on loan.obj_guid = client.obj_guid left join member_credit c on c.member_id=client.uid where 1 = 1 ";
        if ($p['item']) {
            $sql .= " and loan.obj_guid = " . $p['item'];
        }
        if ($p['name']) {
            $sql .= ' and client.display_name like "%' . $p['name'] . '%"';
        }
        $sql .= " ORDER BY client.create_time desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        /*$sql1 = "select uid from (select * from loan_approval order by uid desc) loan_approval group by obj_guid";
        $ids = $r->getRows($sql1);
        $ids = array_column($ids, 'uid');
        $ids =  implode(',', $ids);
        $sql2 = "select * from loan_approval where uid in (".$ids.")";
        $applys = $r->getRows($sql2);
        $new_applys = array();
        foreach ($applys as $key => $value) {
          $new_applys[$value['obj_guid']] = $applys[$key];
        }*/
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("credit.list");
    }

    public function editCreditOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $m_member = new memberModel();
        $m_core_dictionary = M('core_dictionary');
        $member = $m_member->getRow(array(
            'obj_guid' => intval($p['obj_guid'])
        ));
        if (!$member) {
            showMessage('No member');
        }
        $setting = $m_core_dictionary->getDictionary('global_settings');
        $setting = my_json_decode($setting['dict_value']);
        if ($p['credit'] > $setting['operator_credit_maximum']) {
            showMessage('Credit limit.');
        }
        $m_loan_account = new loan_accountModel();
        $rt = $m_loan_account->getCreditInfo(intval($p['obj_guid']));
        $data = $rt->DATA;

        $m_credit = new member_creditModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member['uid']
        ));


        $credit_reference = credit_loanClass::getCreditLevelList();
        $cert_lang = enum_langClass::getCertificationTypeEnumLang();
        foreach ($credit_reference as $k => $v) {
            $item = $v;
            $cert_list = $item['cert_list'];
            foreach ($cert_list as $key => $value) {
                $cert_list[$key] = $cert_lang[$value];
            }
            $item['cert_list'] = $cert_list;
            $credit_reference[$k] = $item;
        }
        Tpl::output('credit_reference_value', $credit_reference);

        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $p['before_credit'] = $member_credit['credit'];
            $rt = $m_loan_account->editCredit($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('loan', 'credit', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'editCredit', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_loan_approval = M('loan_approval');
            $approvaling = $m_loan_approval->getRow(array('obj_guid' => intval($p['obj_guid']), 'state' => 0));//申请中
            if ($approvaling) {
                Tpl::output('approval_info', $approvaling);
            }
            $sql = "SELECT loan.*,client.uid as member_id,client.display_name,client.alias_name,client.phone_id,client.email FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.obj_guid = '" . intval($p['obj_guid']) . "'";
            $info = $r->getRow($sql);
            Tpl::output('info', $info);

            /*$sql1 = "SELECT * FROM member_verify_cert where member_id = " . $info['member_id'];
            $rows = $r->getRows($sql1);*/
            $member_id = $member['uid'];
            $re = memberClass::getMemberSimpleCertResult($member_id);
            if (!$re->STS) {
                showMessage('Error: ' . $re->MSG);
            }
            $verifys = $re->DATA;

            Tpl::output("verifys", $verifys);
            Tpl::output('credit_info', $member_credit);
            Tpl::output('loan_info', $data);
            Tpl::showPage("credit.edit");
        }
    }

    public function contractOp()
    {
        Tpl::showPage("contract");
    }

    public function getInsurancePrice($uid = 0)
    {
        $r = new ormReader();
        $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract GROUP BY loan_contract_id";
        if ($uid) {
            $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract where loan_contract_id = " . $uid . " GROUP BY loan_contract_id";
        }
        $insurance = $r->getRows($sql1);
        $insurance_arr = array();
        foreach ($insurance as $key => $value) {
            $insurance_arr[$value['loan_contract_id']] = $value;
        }
        return $insurance_arr;
    }

    public function getContractListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT contract.*,account.obj_guid,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid where contract.state != -1 ";
        if ($p['item']) {
            $sql .= " and contract.contract_sn = '" . $p['item'] . "'";
        }
        if ($p['name']) {
            $sql .= ' and member.display_name like "%' . $p['name'] . '%"';
        }
        if ($p['date']) {
            $sql .= ' and contract.start_date > "' . $p['date'] . '"';
        }
        if ($p['state'] > -1) {
            $sql .= " and contract.state = " . $p['state'];
        }
        $sql .= " ORDER BY contract.create_time desc";
        $insurance_arr = $this->getInsurancePrice();

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $insurance = $insurance_arr;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $sql1 = "select count(uid) as count from loan_contract where state = " . loanContractStateEnum::WRITE_OFF;
        $count_write_off = $r->getRow($sql1);
        $sql2 = "select count(uid) as count from loan_contract where state != " . loanContractStateEnum::WRITE_OFF . " and state != " . loanContractStateEnum::COMPLETE;
        $count_in = $r->getRow($sql2);
        return array(
            "sts" => true,
            "data" => $rows,
            "insurance" => $insurance_arr,
            "total" => $total,
            "count_write_off" => $count_write_off['count'],
            "count_in" => $count_in['count'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("contract.list");
    }

    public function contractDetailOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $r = new ormReader();
        $uid = intval($p['uid']);

        $sql = "SELECT contract.*,account.obj_guid,product.uid product_id,product.product_code,product.product_name,product.product_description,product.product_feature,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid"
            . " left join loan_product as product on contract.product_id = product.uid where contract.uid = " . $uid;
        $data = $r->getRow($sql);
        if( !$data ){
            showMessage('No contract!');
        }

        $contract_id = $data['uid'];

        // 待还金额
        $re = loan_contractClass::getContractLeftPayableInfo($contract_id);
        $payable_info = $re->DATA;
        $data['left_principal'] = $payable_info['total_payable_amount'];
        Tpl::output("detail", $data);

        $sql1 = "select * from loan_disbursement_scheme where contract_id = " . $uid;
        $disbursement = $r->getRows($sql1);
        Tpl::output("disbursement", $disbursement);


//        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $uid AND state <= " . loanDeductingPenaltiesState::PROCESSING;
//        $deducting_penalties = $r->getRow($sql3);
//        if ($deducting_penalties) {
//            Tpl::output("is_deducting_penalties", false);
//        } else {
//            Tpl::output("is_deducting_penalties", true);
//        }

        $sql2 = "select * from loan_installment_scheme where state!='".schemaStateTypeEnum::CANCEL."' and  contract_id = " . $data['uid'];
        $installment = $r->getRows($sql2);
        $penalties_total = 0;
        $time = date('Y-m-d 23:59:59', time());
        $repayment_arr = array();
        foreach ($installment as $key => $val) {
            if ($val['penalty_start_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($val['uid']);
                $val['penalties'] = $penalties;
//                $val['amount'] += $penalties;
                $penalties_total += $penalties;
                $installment[$key] = $val;
            }
            if ($val['receivable_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $repayment_arr[] = $val;
            }
        }
        Tpl::output("penalties_total", $penalties_total);
        Tpl::output("installment", $installment);
        $insurance_arr = $this->getInsurancePrice($p['uid']);
        Tpl::output("insurance", $insurance_arr);
        Tpl::output("repayment_arr", $repayment_arr);

        Tpl::showPage("contract.detail");
    }

    public function approvalOp()
    {
        Tpl::showPage("approval");
    }

    public function getApprovalListOp($p)
    {
        $r = new ormReader();
        $sql1 = "select max(uid) as uid,obj_guid from loan_approval group by obj_guid";
        $ids = $r->getRows($sql1);
        $ids = array_column($ids, 'uid');
        $ids = implode(',', $ids) ?: 0;
        $sql = "SELECT loan.*,client.uid member_id,client.display_name,client.alias_name,client.phone_id,"
            . " approval.uid as a_uid,approval.before_credit,approval.current_credit,approval.repayment_ability as approval_repayment_ability,approval.operator_id,approval.operate_time,approval.create_time,approval.remark,approval.type,approval.state"
            . " FROM loan_account as loan "
            . " left join client_member as client on loan.obj_guid = client.obj_guid"
            . " inner join loan_approval as approval on client.obj_guid = approval.obj_guid where approval.uid in (" . $ids . ") ";

        if ($p['member_item']) {
            $sql .= " and loan.obj_guid = " . $p['member_item'];
        }
        if ($p['member_name']) {
            $sql .= ' and client.display_name like "%' . $p['member_name'] . '%"';
        }
        if ($p['type'] > -1) {
            $sql .= " and approval.type = " . $p['type'];
        }

        if ($p['state'] != 2) {
            $sql .= " and approval.state = " . $p['state'];
        }
        $sql .= " ORDER BY approval.create_time desc";
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
            "pageSize" => $pageSize,
        );
        Tpl::showPage("approval.list");
    }

    public function approvalEditOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $r = new ormReader();
        $sql = "select approval.*,member.uid as member_id,member.display_name,member.phone_id,member.email FROM loan_approval as approval LEFT JOIN client_member as member ON approval.obj_guid = member.obj_guid where approval.uid = " . $p['uid'];
        $data = $r->getRow($sql);
        if (!$data) {
            showMessage('Error', getUrl('loan', 'approval', array(), false, BACK_OFFICE_SITE_URL));
        }
        Tpl::output('info', $data);
        $sql = "select * FROM loan_approval where obj_guid = " . $data['obj_guid'];
        $list = $r->getRows($sql);
        Tpl::output('list', $list);
        Tpl::showPage("approval.edit");
    }

    public function approvalConfirmOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_loan_approval = new loan_approvalModel();
        $data = $m_loan_approval->find(array('uid' => $p['uid']));
        if (!$data) {
            showMessage('No Data', getUrl('loan', 'approval', array(), false, BACK_OFFICE_SITE_URL));
        }

        $data['operator_id'] = $this->user_id;
        $data['operator_name'] = $this->user_name;
        // 审核状态
        $data['state'] = $p['state'];

        $rt = $m_loan_approval->creditGrantConfirm($data);
        if ($rt->STS) {
            showMessage($rt->MSG, getUrl('loan', 'approval', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            unset($p['form_submit']);
            showMessage($rt->MSG, getUrl('user', 'approvalEdit', $p, false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 产品页
     */
    public function productOp()
    {
        $r = new ormReader();
        $m_loan_product = M('loan_product');
        $sql = "SELECT MAX(uid) uid FROM loan_product WHERE state < 40 GROUP BY product_key";
        $product_ids = $r->getRows($sql);
        if ($product_ids) {
            $product_ids = array_column($product_ids, 'uid');
            $sql = "SELECT * FROM loan_product WHERE uid IN (" . implode(',', $product_ids) . ") ORDER BY uid DESC";
            $product_list = $r->getRows($sql);
            foreach ($product_list as $key => $product) {
                $product_key = $product['product_key'];
                $product_count = $m_loan_product->field('count(*) count')->find(array('product_key' => $product_key));
                $product_list[$key]['count'] = $product_count;
                if ($product['state'] == 10 && $product_count > 1) {
                    $product_valid = $m_loan_product->find(array('product_key' => $product_key, 'state' => 20));
                    if ($product_valid) {
                        $product_list[$key]['valid_id'] = $product_valid['uid'];
                    }
                }
                $product_contract = $this->getProductContractById($product['uid']);
                $product_list[$key]['loan_contract'] = $product_contract['loan_count'] ?: 0;
                $product_list[$key]['loan_client'] = $product_contract['loan_client'] ?: 0;
                $product_list[$key]['loan_ceiling'] = $product_contract['loan_ceiling'] ?: 0;
                $product_list[$key]['loan_balance'] = $product_contract['loan_balance'] ?: 0;
            }
        } else {
            $product_list = array();
        };
        Tpl::output("product_list", $product_list);
        Tpl::showPage("product.list");
    }

    /**
     * 系列历史版本
     */
    public function showProductHistoryOp()
    {
        $uid = intval($_REQUEST['uid']);
        $m_loan_product = M('loan_product');
        $row = $m_loan_product->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $product_history = $m_loan_product->orderBy('uid DESC')->select(array('product_key' => $row['product_key']));
        foreach ($product_history as $key => $product) {
            $product_contract = $this->getProductContractById($product['uid']);
            $product_history[$key]['loan_contract'] = $product_contract['loan_count'] ?: 0;
            $product_history[$key]['loan_client'] = $product_contract['loan_client'] ?: 0;
            $product_history[$key]['loan_ceiling'] = $product_contract['loan_ceiling'] ?: 0;
            $product_history[$key]['loan_balance'] = $product_contract['loan_balance'] ?: 0;
        }
        Tpl::output("product_history", $product_history);
        Tpl::showPage("product.history");
    }

    /**
     * 获取产品合同信息
     * @param $product_id
     * @return ormDataRow
     */
    private function getProductContractById($product_id)
    {
        $r = new ormReader();
        $sql = "SELECT COUNT(*) loan_count,SUM(apply_amount) loan_ceiling,sum(receivable_principal+receivable_interest+receivable_operation_fee+receivable_annual_fee+receivable_penalty-loss_principal-loss_interest-loss_operation_fee-loss_annual_fee-loss_penalty) receivable FROM loan_contract WHERE product_id = " . $product_id;
        $product_contract = $r->getRow($sql);
        $sql = "SELECT SUM(lr.amount) repayment FROM loan_repayment AS lr INNER JOIN loan_contract AS lc ON lr.contract_id = lc.uid WHERE lr.state = 100 AND lc.product_id = " . $product_id;
        $repayment = $r->getOne($sql);
        $loan_balance = $product_contract['receivable'] - $repayment;
        $sql = "SELECT COUNT(member.uid) loan_client FROM loan_contract AS contract"
            . " INNER JOIN loan_account AS account ON contract.account_id = account.uid"
            . " INNER JOIN client_member AS member ON account.obj_guid = member.obj_guid WHERE contract.product_id = " . $product_id . " GROUP BY member.uid";
        $loan_client = $r->getOne($sql);
        $product_contract['loan_balance'] = $loan_balance;
        $product_contract['loan_client'] = $loan_client;
        return $product_contract;
    }

    /**
     * 展示产品信息
     */
    public function showProductOp()
    {
        $uid = intval($_REQUEST['uid']);
        $class_product = new product();
        $rt = $class_product->getProductInfoById($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        Tpl::output('product_info', $rt->DATA);

        $currency_list = C('currency');
        Tpl::output("currency_list", $currency_list);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);
        Tpl::output("is_edit", isset($_GET['is_edit']) ? $_GET['is_edit'] : true);
        Tpl::showPage('product.info');
    }

    /**
     * 添加页
     */
    public function addProductOp()
    {
        $currency_list = C('currency');
        Tpl::output("currency_list", $currency_list);

        $penalty_on = (new penaltyOnEnum())->Dictionary();
        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();

        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("penalty_on", $penalty_on);
        Tpl::output("interest_rate_period", $interest_rate_period);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);
        Tpl::showPage('product.add');
    }

    /**
     * 编辑产品
     */
    public function editProductOp()
    {
        $uid = intval($_REQUEST['uid']);
        $class_product = new product();
        $rt = $class_product->getProductInfoById($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        Tpl::output('product_info', $rt->DATA);

        $currency_list = C('currency');
        Tpl::output("currency_list", $currency_list);

        $penalty_on = (new penaltyOnEnum())->Dictionary();
        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();
        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("penalty_on", $penalty_on);
        Tpl::output("interest_rate_period", $interest_rate_period);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);
        Tpl::showPage('product.add');
    }

    /**
     * 保存产品主要信息
     * @param $p
     * @return result
     */
    public function insertProductMainOp($p)
    {
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $class_product = new product();
        $rt = $class_product->insertProductMain($p);
        return $rt;
    }

    /**
     * 更新产品主要信息
     * @param $p
     * @return result
     */
    public function updateProductMainOp($p)
    {
        $class_product = new product();
        $rt = $class_product->updateProductMain($p);
        return $rt;
    }

    /**
     * 保存罚金信息
     * @param $p
     * @return result
     */
    public function updateProductPenaltyOp($p)
    {
        $class_product = new product();
        $rt = $class_product->updateProductPenalty($p);
        return $rt;
    }

    /**
     * 利率列表
     * @param $p
     * @return array
     */
    public function getSizeRateListOp($p)
    {
        $class_product = new product();
        $rt = $class_product->getSizeRateList($p);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        $rt['mortgage_type'] = $define_arr['mortgage_type'];
        $rt['guarantee_type'] = $define_arr['guarantee_type'];
        $rt['type'] = $p['type'];
        return $rt;
    }

    /**
     * 保存size利率
     * @param $p
     * @return result
     */
    public function insertSizeRateOp($p)
    {
        $class_product = new product();
        $rt = $class_product->insertSizeRate($p);
        return $rt;
    }

    /**
     * 更新size利率
     * @param $p
     * @return result
     */
    public function updateSizeRateOp($p)
    {
        $class_product = new product();
        $rt = $class_product->updateSizeRate($p);
        return $rt;
    }

    /**
     * 保存size利率
     * @param $p
     * @return result
     */
    public function removeSizeRateOp($p)
    {
        $class_product = new product();
        $rt = $class_product->removeSizeRate($p);
        return $rt;
    }

    /**
     * 更新贷款条件
     * @param $p
     * @return result
     */
    public function updateProductConditionOp($p)
    {
        $class_product = new product();
        $rt = $class_product->updateProductCondition($p);
        return $rt;
    }

    /**
     * 更新详情
     * @param $p
     * @return result
     */
    public function updateDescriptionOp($p)
    {
        $class_product = new product();
        $rt = $class_product->updateDescription($p);
        return $rt;
    }

    /**
     * 发布产品
     * @param $p
     * @return result
     */
    public function releaseProductOp($p)
    {
        $uid = intval($p['uid']);
        $class_product = new product();
        $rt = $class_product->changeProductState($uid, 20);
        if ($rt->STS) {
            return new result(true, 'Release Successful!');
        } else {
            return new result(false, 'Release Failure!');
        }
    }

    /**
     * 产品下架
     * @param $p
     * @return result
     */
    public function unShelveProductOp($p)
    {
        $uid = intval($p['uid']);
        $class_product = new product();
        $rt = $class_product->changeProductState($uid, 30);
        if ($rt->STS) {
            return new result(true, 'Inactive Successful!');
        } else {
            return new result(false, 'Inactive Failure!');
        }
    }

    /**
     * 逾期合同
     */
    public function overdueOp()
    {
        Tpl::showPage('contract.overdue');
    }

    /**
     * @param $p
     * @return array
     */
    public function getOverdueListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT lis.contract_id,SUM(lis.amount) amount,COUNT(lis.uid) num ,MIN(lis.receivable_date) receivable_date" .
            " FROM loan_installment_scheme lis WHERE lis.state = 0 AND lis.receivable_date < '" . Now() . "' GROUP BY lis.contract_id ORDER BY lis.receivable_date DESC";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_ids = array_column($rows, 'contract_id');
            $contract_id_str = '(' . implode(',', $contract_ids) . ')';
            $sql = "SELECT lc.uid,lc.contract_sn,cm.display_name,cm.phone_id" .
                " FROM loan_contract lc LEFT JOIN loan_account la ON lc.account_id = la.uid" .
                " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid" .
                " WHERE lc.uid IN $contract_id_str";
            $arr = $r->getRows($sql);
            $arr = resetArrayKey($arr, 'uid');
            foreach ($rows as $key => $row) {
                $contract_id = $row['contract_id'];
                $row = array_merge($row, $arr[$contract_id]);
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
        );
    }

    /**
     * 特殊利率
     */
    public function specialRateOp()
    {
        $product_id = intval($_GET['product_id']);
        $size_rate_id = intval($_GET['size_rate_id']);
        $class_product = new product();
        $special_rate_list = $class_product->getSpecialRateList($size_rate_id);
        Tpl::output('special_rate_list', $special_rate_list);
        Tpl::output('product_id', $product_id);
        Tpl::output('size_rate_id', $size_rate_id);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('client_grade'));
        Tpl::output('client_grade', $define_arr['client_grade']);
        //Tpl::output('client_type', $define_arr['client_type']);
        Tpl::showpage('special.rate');
    }

    /**
     * 增加特殊利率
     * @param $p
     * @return result
     */
    public function insertSpecialSizeRateOp($p)
    {
        $class_product = new product();
        if (!trim($p['client_type']) && !trim($p['client_grade'])) {
            return new result(false, 'Please set member grade or member type first!');
        }
        $rt = $class_product->insertSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }

    /**
     * 编辑特殊利率
     * @param $p
     * @return result
     */
    public function updateSpecialSizeRateOp($p)
    {
        $class_product = new product();
        if (!trim($p['client_type']) && !trim($p['client_grade'])) {
            return new result(false, 'Please set member grade or member type first!');
        }
        $rt = $class_product->updateSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }

    /**
     * 移除特殊利率
     * @param $p
     * @return result
     */
    public function removeSpecialSizeRateOp($p)
    {
        $class_product = new product();
        $rt = $class_product->removeSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }

    /**
     * 申请列表
     */
    public function applyOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $type = $_GET['type'] ?: 'unprocessed';
        Tpl::output('type', $type);
        Tpl::showpage('apply');
    }

    /**
     * 获取申请列表
     * @param $p
     * @return array
     */
    public function getApplyListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();

        // todo 暂时只有operator权限的列表
        $sql = "SELECT la.*,uu.user_name,uu.user_code FROM loan_apply la LEFT JOIN um_user uu ON la.credit_officer_id = uu.uid WHERE (la.apply_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['type']) == 'unprocessed') {
            $sql .= " AND la.state in ('" . loanApplyStateEnum::LOCKED . "','" . loanApplyStateEnum::CREATE . "') ";
        } else {
            $sql .= " AND la.state > " . loanApplyStateEnum::CREATE;
        }
        if (trim($p['search_text'])) {
            $sql .= " AND al.applicant_name like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY la.apply_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $apply_source = (new loanApplySourceEnum)->Dictionary();
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "cur_uid" => $this->user_id,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "type" => trim($p['type']),
            "apply_source" => $apply_source,
        );
    }

    /**
     * 审核通过
     */
    public function operatorAuditApplyOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_apply = M('loan_apply');
        $row = $m_loan_apply->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $m_um_user = M('um_user');
        // 绑定的credit officer

        $bound_credit_officer = $m_um_user->find(array(
            'uid' => $row->credit_officer_id
        ));

        Tpl::output('bound_credit_officer', $bound_credit_officer);

        if ($p['form_submit'] == 'ok') {

            $row->operator_id = $this->user_id;
            $row->operator_name = $this->user_name;
            $row->operator_remark = $p['remark'];
            $row->update_time = Now();

            $state = intval($p['approve_state']);
            if ($state == 1) {

                // 通过，分配CO
                $co_id = intval($p['credit_officer_id']);
                if (!$co_id) {
                    showMessage('Please allot credit officer!');
                }

                $co = $m_um_user->getRow($co_id);
                if (!$co) {
                    showMessage('Credit officer not exist!');
                }
                $row->state = loanApplyStateEnum::ALLOT_CO;
                $row->credit_officer_id = $co_id;


            } else {
                // 拒绝
                $row->state = loanApplyStateEnum::OPERATOR_REJECT;
            }

            $rt = $row->update();
            if ($rt->STS) {
                showMessage('Handle Successful!', getUrl('loan', 'apply', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Handle fail!');
            }


        } else {
            //审核中
            if ($row->state == loanApplyStateEnum::LOCKED) {
                // 超时放开，让别人可审 1小时
                if ((strtotime($row['update_time']) + 3600) < time()) {
                    $row->handler_id = $this->user_id;
                    $row->handler_name = $this->user_name;
                    $row->update_time = Now();
                    $up = $row->update();
                    if (!$up->STS) {
                        showMessage($up->MSG, getUrl('loan', 'apply', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
                    }
                }
            } elseif ($row->state == loanApplyStateEnum::CREATE) {
                $row->state = loanApplyStateEnum::LOCKED;
                $row->handler_id = $this->user_id;
                $row->handler_name = $this->user_name;
                $row->update_time = Now();
                $rt = $row->update();
                if (!$rt->STS) {
                    showMessage($rt->MSG);
                }
            }
            $lock = false;
            if ($row['state'] == loanApplyStateEnum::LOCKED && $this->user_id != $row['handler_id']) {
                //审核中
                $lock = true;
            }
            Tpl::output('lock', $lock);
            Tpl::output('apply_info', $row);
            $apply_source = (new loanApplySourceEnum)->Dictionary();
            Tpl::output('apply_source', $apply_source);

            $sql = "select * from um_user where user_status='1' and user_position like '%".userPositionEnum::CREDIT_OFFICER."%' ";
            $credit_officer_list = $m_um_user->reader->getRows($sql);
            Tpl::output('credit_officer_list', $credit_officer_list);
            Tpl::showpage('apply.audit');
        }
    }

    /**
     * 解锁申请锁定
     * @param $p
     * @return result
     */
    public function unlockedApplyOp($p)
    {
        $uid = intval($p['uid']);
        $m_loan_apply = M('loan_apply');
        $row = $m_loan_apply->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        if ($row->state == loanApplyStateEnum::LOCKED) {
            return new result(true);
        }
        $row->state = loanApplyStateEnum::CREATE;
        $row->handler_id = 0;
        $row->handler_name = '';
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true);
        } else {
            return new result(false, $rt->MSG);
        }
    }

    /**
     * 添加贷款申请
     */
    public function addApplyOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_loan_apply = M('loan_apply');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;

            $rt = $m_loan_apply->addApply($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('loan', 'apply', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('loan', 'addApply', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type'));
            Tpl::output('mortgage_type', $define_arr['mortgage_type']);

            $apply_source = (new loanApplySourceEnum)->Dictionary();
            Tpl::output('request_source', $apply_source);
            Tpl::showpage('apply.add');
        }
    }

    /**
     * 提前还款
     */
    public function requestToPrepaymentOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $request_state = (new prepaymentApplyStateEnum())->Dictionary();
        Tpl::output('request_state', $request_state);
        Tpl::showPage('request.prepayment');
    }

    /**
     * 获取提前还款申请
     * @param $p
     * @return array
     */
    public function getRequestPrepaymentListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);

        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE (lrr.apply_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($p['state'] >= 0) {
            $sql .= " AND lrr.state = " . intval($p['state']);
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lrr.apply_time DESC";
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
            "pageSize" => $pageSize,
            "cur_uid" => $this->user_id,
            "type" => trim($p['type']),
        );
    }

    /**
     * 提前还款审核页面
     */
    public function auditRequestPrepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_request_repayment = M('loan_prepayment_apply');
        $row = $m_loan_request_repayment->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        //审核中
        if ($row->state == prepaymentApplyStateEnum::AUDITING) {
            // 超时放开，让别人可审 1小时
            if ((strtotime($row['update_time']) + 3600) < time()) {
                $row->state = prepaymentApplyStateEnum::AUDITING;
                $row->auditor_id = $this->user_id;
                $row->auditor_name = $this->user_name;
                $row->update_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG);
                }
            }
        } elseif ($row->state == prepaymentApplyStateEnum::CREATE) {
            $row->state = prepaymentApplyStateEnum::AUDITING;
            $row->auditor_id = $this->user_id;
            $row->auditor_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
        }
        $lock = false;
        if ($this->user_id != $row['auditor_id']) {
            //审核中
            $lock = true;
        }
        Tpl::output('lock', $lock);

        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn,lc.currency contract_currency"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);

        $re = loan_contractClass::getPrepaymentDetail($detail['contract_id']);
        $prepayment_detail = $re->DATA;
        Tpl::output('prepayment_detail', $prepayment_detail);

        Tpl::showpage('request.prepayment.audit');
    }

    /**
     * 审核提前还款  批准？不批准
     * @param $p
     * @return result
     */
    public function auditPrepaymentOp($p)
    {
        $uid = intval($p['uid']);
        $type = trim($p['type']);
        $remark = trim($p['remark']);

        $m_loan_request_prepayment = M('loan_prepayment_apply');
        $row = $m_loan_request_prepayment->getRow($uid);
        if ($row->auditor_id != $this->user_id) {
            return new result(false, 'Auditor Error!');
        }
        if ($type == 'approve') {
            $row->state = prepaymentApplyStateEnum::APPROVED;
        } else {
            $row->state = prepaymentApplyStateEnum::DISAPPROVE;
        }

        $row->auditor_id = $this->user_id;
        $row->auditor_name = $this->user_name;
        $row->audit_remark = $remark;
        $row->audit_time = Now();
        $row->update_time = Now();
        $rt = $row->update();

        if ($rt->STS) {
            return new result(true, 'Audit Successful!');
        } else {
            return new result(false, 'Audit Failed!');
        }
    }

    /**
     * 查看提前还款申请情况
     */
    public function viewRequestPrepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        if (!$detail) {
            showMessage('Invalid Id!');
        }
        Tpl::output('detail', $detail);
        Tpl::showpage('request.prepayment.view');
    }

    /**
     * 还款请求
     */
    public function requestToRepaymentOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $request_state = (new requestRepaymentStateEnum())->Dictionary();

        Tpl::output('request_state', $request_state);
        Tpl::showPage('request.repayment');

    }

    /**
     * 获取还款申请列表
     * @param $p
     * @return array
     */
    public function getRequestRepaymentListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $type = trim($p['type']);

        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn,lis.scheme_name FROM loan_request_repayment lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " LEFT JOIN loan_installment_scheme lis ON lrr.scheme_id = lis.uid"
            . " WHERE (lrr.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($p['state'] >= 0) {
            $sql .= " AND lrr.state = " . intval($p['state']);
        }
        if ($type) {
            $sql .= " AND lrr.type = '" . $type . "'";
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lrr.create_time DESC";
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
            "pageSize" => $pageSize,
            "cur_uid" => $this->user_id,
        );
    }

    /**
     * 审核还款申请
     */
    public function auditRequestRepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_request_repayment = M('loan_request_repayment');
        $row = $m_loan_request_repayment->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        //审核中
        if ($row->state == requestRepaymentStateEnum::PROCESSING) {
            // 超时放开，让别人可审 1小时
            if ((strtotime($row['update_time']) + 3600) < time()) {
                $row->state = requestRepaymentStateEnum::PROCESSING;
                $row->handler_id = $this->user_id;
                $row->handler_name = $this->user_name;
                $row->update_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG);
                }
            }
        } elseif ($row->state == requestRepaymentStateEnum::CREATE) {
            $row->state = requestRepaymentStateEnum::PROCESSING;
            $row->handler_id = $this->user_id;
            $row->handler_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
        }
        $lock = false;
        if ($this->user_id != $row['handler_id']) {
            //审核中
            $lock = true;
        }
        Tpl::output('lock', $lock);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_request_repayment lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);

        $repayment_type_lang = enum_langClass::getPaymentTypeLang();
        Tpl::output('repayment_type_lang',$repayment_type_lang);

        if ($row['type'] == 'schema') {

            $rt = loan_contractClass::getRepaymentSchemaByAmount($detail['contract_id'], $detail['amount'],$row['currency']);
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
            $repayment_schema = $rt->DATA;
            $expired_schema = array();
            $unexpired_schema = array();

            $today_start = strtotime(date('Y-m-d 00:00:00', time()));
            foreach ($repayment_schema['repayment_schema'] as $schema) {
                $receivable_date = strtotime(date('Y-m-d 00:00:00', strtotime($schema['receivable_date'])));
                if ($receivable_date <= $today_start) {
                    $expired_schema[] = $schema;
                } else {
                    $unexpired_schema[] = $schema;
                }
            }

            Tpl::output('expired_schema', $expired_schema);
            Tpl::output('unexpired_schema', $unexpired_schema);
            Tpl::showpage('request.repayment.audit');
        } else {
            $prepayment_apply_id = $row['prepayment_apply_id'];
            $m_loan_prepayment_apply = M('loan_prepayment_apply');
            $prepayment_apply = $m_loan_prepayment_apply->find(array('uid' => $prepayment_apply_id));
            Tpl::output('prepayment_apply', $prepayment_apply);
            Tpl::showpage('request.prepayment.handle');
        }
    }

    /**
     * 确定还款到账
     * @param $p
     * @return result
     */
    public function auditRepaymentOp($p)
    {
        $uid = intval($p['uid']);
        $type = $p['type'];
        $remark = trim($p['remark']);

        $payer_name = trim($p['payer_name']);
        $payer_type = trim($p['payer_type']);
        $payer_account = trim($p['payer_account']);
        $payer_phone = trim($p['payer_phone']);
        $bank_name = trim($p['bank_name']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_no = trim($p['bank_account_no']);

        $m_loan_request_repayment = M('loan_request_repayment');
        $row = $m_loan_request_repayment->getRow($uid);


        if ($payer_name) $row->payer_name = $payer_name;
        if ($payer_type) $row->payer_type = $payer_type;
        if ($payer_account) $row->payer_account = $payer_account;
        if ($payer_phone) $row->payer_phone = $payer_phone;
        if ($bank_name) $row->bank_name = $bank_name;
        if ($bank_account_name) $row->bank_account_name = $bank_account_name;
        if ($bank_account_no) $row->bank_account_no = $bank_account_no;

        $row->handler_id = $this->user_id;
        $row->handler_name = $this->user_name;
        $row->handle_remark = $remark;
        $row->handle_time = Now();

        if ($type == 'offline_failure') {

            $row->state = requestRepaymentStateEnum::FAILED;
            $rt_1 = $row->update();
            if ( !$rt_1->STS ) {
                return new result(false, 'Handle failure!');
            }
            return new result(true, 'Handle successful!');

        } else {

            $row->state = requestRepaymentStateEnum::PROCESSING;
            $rt_1 = $row->update();
            if ( !$rt_1->STS ) {
                return new result(false, 'Handle failure!');
            }

            $request_id = $row->uid;
            if ($p['received_date']) {
                $received_date = date('Y-m-d H:i:s', strtotime($p['received_date']));
            } else {
                $received_date = date('Y-m-d H:i:s');
            }

            $handler_info = array(
                'handler_id' => $this->user_id,
                'handler_name' => $this->user_name,
                'handle_remark' => $remark,
                'handle_time' => Now()
            );


            try{
                $conn = ormYo::Conn();
                $conn->startTransaction();
                $re = loan_contractClass::requestRepaymentConfirmReceived($request_id, $received_date, $handler_info);
                if (!$re->STS) {
                    $conn->rollback();
                    return new result(false, $re->MSG);
                }

                $conn->submitTransaction();
                return new result(true, 'Handle success!');


            }catch(Exception $e ){

                return new result(false,$e->getMessage());
            }





        }


    }

    /**
     * 查看提前还款申请情况
     */
    public function viewRequestRepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        if (!$detail) {
            showMessage('Invalid Id!');
        }
        Tpl::output('detail', $detail);
        Tpl::showpage('request.prepayment.view');
    }


    /**
     * 查看还款明细
     */
    public function viewRequestRepaymentDetailOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "select r.*,c.contract_sn from loan_request_repayment r left join loan_contract c on r.contract_id=c.uid where r.uid='$uid' ";
        $detail = $r->getRow($sql);
        $payment_type_lang = enum_langClass::getPaymentTypeLang();
        Tpl::output('detail', $detail);
        Tpl::output('payment_type_lang', $payment_type_lang);
        Tpl::showpage('request.repayment.view');
    }

    /**
     * @param $p
     * @return result
     */
    public function modifyPenaltiesOp($p)
    {
        $uid = intval($p['uid']);
        $r = new ormReader();

        $sql = "SELECT * FROM loan_contract WHERE uid = $uid AND state > " . loanContractStateEnum::PENDING_APPROVAL;
        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            return array("sts" => false);
        }

        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $uid AND state <= " . loanDeductingPenaltiesState::PROCESSING;
        $deducting_penalties = $r->getRow($sql3);

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $uid AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        $deduction_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $scheme['penalties'] = $penalties;
            $penalties_total += $penalties;
            $deduction_total += $scheme['deduction_penalty'];
            $scheme_list[$key] = $scheme;
        }

        return array(
            "sts" => true,
            "loan_contract" => $loan_contract,
            "deducting_penalties" => $deducting_penalties,
            "penalties_total" => $penalties_total,
            "deduction_total" => $deduction_total,
            "data" => $scheme_list
        );
    }

    /**
     * 保存减免罚息申请
     * @param $p
     * @return result
     */
    public function savePenaltiesApplyOp($p)
    {
        $contract_id = intval($p['uid']);
        $deducting_penalties = round($p['deducting_penalties'], 2);
        $remark = $p['remark'];
        $r = new ormReader();
        $sql = "SELECT * FROM loan_contract WHERE uid = $contract_id AND state > " . loanContractStateEnum::PENDING_APPROVAL;
        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            return new result(false, 'Invalid Id!');
        }

        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $contract_id AND state <= " . loanDeductingPenaltiesState::PROCESSING;
        $deducting_penalties_apply = $r->getRow($sql3);
        if ($deducting_penalties_apply) {
            return new result(false, 'There has been an unaudited application!');
        }

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $penalties_total += $penalties;
        }

        if ($deducting_penalties > $penalties_total) {
            return new result(false, 'It can\'t be greater than penalties total!');
        }

        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $row = $m_loan_deducting_penalties->newRow();
        $row->contract_id = $contract_id;
        $row->deducting_penalties = $deducting_penalties;
        $row->type = 1;
        $row->remark = $remark;
        $row->state = loanDeductingPenaltiesState::CREATE;
        $row->creator_id = $this->user_id;
        $row->creator_name = $this->user_name;
        $row->creator_name = $this->user_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!');
        } else {
            return new result(false, 'Add Failure!');
        }
    }

    /**
     * 减免罚息
     */
    public function deductingPenaltiesOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('deducting_penalties');
    }

    /**
     * @param $p
     * @return array
     */
    public function getDeductingPenaltiesListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT ldp.*,lc.contract_sn FROM loan_deducting_penalties ldp"
            . " INNER JOIN loan_contract lc ON ldp.contract_id = lc.uid"
            . " WHERE (ldp.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY ldp.create_time DESC";
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
            "pageSize" => $pageSize,
        );
    }

    /**
     * 审核减免申请
     */
    public function showAuditPenaltiesOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $deducting_penalties = $m_loan_deducting_penalties->find(array('uid' => $uid));
        if (!$deducting_penalties || $deducting_penalties['state'] != loanDeductingPenaltiesState::CREATE) {
            showMessage('Invalid Id!');
        }

        $r = new ormReader();
        $contract_id = intval($deducting_penalties['contract_id']);

        $sql = "SELECT lc.*,cm.display_name,cm.login_code  FROM loan_contract lc LEFT JOIN loan_account la ON lc.account_id = la.uid"
            . " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " WHERE lc.uid = $contract_id AND lc.state > " . loanContractStateEnum::PENDING_APPROVAL;

        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            showMessage('Invalid Id!');
        }

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        $deduction_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $scheme['penalties'] = $penalties;
            $penalties_total += $penalties;
            $deduction_total += $scheme['deduction_penalty'];
            $scheme_list[$key] = $scheme;
        }

        $loan_contract['penalties_total'] = $penalties_total;
        $loan_contract['deduction_total'] = $deduction_total;
        Tpl::output('deducting_penalties', $deducting_penalties);
        Tpl::output('loan_contract', $loan_contract);
        Tpl::output('scheme_list', $scheme_list);
        Tpl::showpage('audit.penalties');
    }

    /**
     * 审核
     * @param $p
     * @return result
     */
    public function auditPenaltiesOp($p)
    {
        $uid = intval($p['uid']);
        $type = $p['type'];
        $r = new ormReader();
        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $m_loan_installment_scheme = M('loan_installment_scheme');

        $deducting_penalties_row = $m_loan_deducting_penalties->getRow(array('uid' => $uid));
        if (!$deducting_penalties_row || $deducting_penalties_row['state'] != loanDeductingPenaltiesState::CREATE) {
            return new result(false, 'Invalid Id!');
        }

        if ($type == 'disapprove') {
            $deducting_penalties_row->state = loanDeductingPenaltiesState::DISAPPROVE;
            $deducting_penalties_row->auditor_id = $this->user_id;
            $deducting_penalties_row->auditor_id = $this->user_name;
            $deducting_penalties_row->audit_time = Now();
            $rt = $deducting_penalties_row->update();
            if ($rt->STS) {
                return new result(true, 'Audit Successful!');
            } else {
                return new result(true, 'Audit Failure!');
            }
        } else {
            $conn = ormYo::Conn();
            $conn->startTransaction();

            try {
                $deducting_penalties_row->state = loanDeductingPenaltiesState::USED;
                $deducting_penalties_row->auditor_id = $this->user_id;
                $deducting_penalties_row->auditor_name = $this->user_name;
                $deducting_penalties_row->audit_time = Now();
                $rt_1 = $deducting_penalties_row->update();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(true, 'Audit Failure!');
                }

                $deducting_penalties = $deducting_penalties_row['deducting_penalties'];
                $contract_id = $deducting_penalties_row['contract_id'];

                $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state!=" . schemaStateTypeEnum::CANCEL . " AND state <  " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
                $scheme_list = $r->getRows($sql);
                if (!$scheme_list) {
                    $conn->rollback();
                    return new result(true, 'Invalid Id!');
                }
                foreach ($scheme_list as $scheme) {
                    $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
                    if ($penalties <= 0) continue;
                    if ($penalties > $deducting_penalties) {
                        $deduction_penalty = $deducting_penalties + $scheme['deduction_penalty'];
                        $rt = $m_loan_installment_scheme->update(array('uid' => $scheme['uid'], 'deduction_penalty' => $deduction_penalty));
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(true, 'Audit Failure!');
                        } else {
                            break;
                        }
                    } else {
                        $deducting_penalties -= $penalties;
                        $deduction_penalty = $penalties + $scheme['deduction_penalty'];
                        $update = array(
                            'uid' => $scheme['uid'],
                            'deduction_penalty' => $deduction_penalty
                        );
                        if ($scheme['actual_payment_amount'] >= $scheme['amount']) {
                            $update['state'] = schemaStateTypeEnum::COMPLETE;
                            $update['done_time'] = Now();
                        }

                        $rt = $m_loan_installment_scheme->update($update);
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(true, 'Audit Failure!');
                        }
                    }
                }

                $is_paid_off = loan_contractClass::contractIsPaidOff($contract_id);

                if ($is_paid_off) {
                    $rt_2 = loan_contractClass::contractComplete($contract_id);
                    if (!$rt_2->STS) {
                        $conn->rollback();
                        return $rt_2;
                    }
                }

                $conn->submitTransaction();
                return new result(true, 'Audit Successful!');
            } catch (Exception $ex) {
                $conn->rollback();
                return new result(false, $ex->getMessage());
            }
        }
    }

    /**
     * 确认还款
     * @param $p
     * @return result
     */
    public function submitRepaymentOp($p)
    {

        $uid = intval($p['uid']);
        $repayment_total = round($p['repayment_total'], 2);
        $remark = trim($p['remark']);
        $currency = $p['currency'] ? $p['currency'] : currencyEnum::USD;

        $class_user = new userClass();
        $user_info = $class_user->getUserInfo($this->user_id);

        $payment_info = array(
            'branch_id' => $user_info->DATA['branch_id'],
            'teller_id' => $this->user_id,
            'teller_name' => $this->user_name,
            'creator_id' => $this->user_id,
            'creator_name' => $this->user_name,
            'remark' => $remark
        );

        try{
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $rt = loan_contractClass::schemaManualRepaymentByCash($uid,$repayment_total,$currency,$user_info->DATA['branch_id'],$this->user_id,$this->user_name);
            if ($rt->STS) {
                $conn->submitTransaction();
                return new result(true, 'Repayment successful!');
            } else {
                $conn->rollback();
                return new result(false, 'Repayment failure!');
            }

        }catch( Exception $e ){
            return new result(false, 'Repayment failure!'.$e->getMessage());
        }


    }

    /**
     * 核销贷款
     */
    public function writeOffApplyOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $rt = loan_contractClass::calculateContractWriteOffLoss($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $loss_amount = $rt->DATA['loss_principal'];

        if ($p['form_submit'] == 'ok') {
            $close_remark = trim($p['close_remark']);
            if (!$close_remark) {
                showMessage('Remark required!');
            }
            $m_loan_writtenoff = M('loan_writtenoff');
            $row = $m_loan_writtenoff->newRow();
            $row->contract_id = $uid;
            $row->close_type = 10;
            $row->close_remark = $close_remark;
            $row->loss_amount = $loss_amount;
            $row->creator_id = $this->user_id;
            $row->creator_name = $this->user_name;
            $row->create_time = Now();
            $row->state = writeOffStateEnum::CREATE;
            $rt = $row->insert();
            if ($rt->STS) {
                showMessage('Add successful!', getUrl('loan', 'contractDetail', array('uid' => $uid), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->STS);
            }

        } else {
            $r = new ormReader();
            $sql = "SELECT lc.*,cm.display_name FROM loan_contract lc"
                . " INNER JOIN loan_account la ON lc.account_id = la.uid"
                . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid"
                . " WHERE lc.uid = $uid";

            $contact_info = $r->getRow($sql);
            if (!$contact_info) {
                showMessage('Invalid Id!');
            }

            $sql = "SELECT * FROM loan_writtenoff WHERE contract_id = $uid AND state IN (" . writeOffStateEnum::CREATE . ',' . writeOffStateEnum::APPROVING . ') ORDER BY uid DESC';
            $write_off = $r->getRow($sql);
            if ($write_off) {
                Tpl::output('write_off', $write_off);
            }
            $contact_info['loss_amount'] = $loss_amount;
            Tpl::output('contact_info', $contact_info);
            Tpl::showpage('write.off.apply');
        }
    }

    /**
     * 核销列表
     */
    public function writeOffOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $type = $_GET['type'] ?: 'unprocessed';
        Tpl::output('type', $type);
        Tpl::showPage('write.off');
    }

    /**
     * 获取核销列表
     * @param $p
     * @return array
     */
    public function getWriteOffListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT lw.*,lc.contract_sn,cm.display_name FROM loan_writtenoff lw"
            . " INNER JOIN loan_contract lc ON lw.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " WHERE (lw.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['type']) == 'unprocessed') {
            $sql .= " AND lw.state < " . writeOffStateEnum::INVALID;
        } else {
            $sql .= " AND lw.state > " . writeOffStateEnum::APPROVING;
        }
        if (trim($p['search_text'])) {
            $sql .= " AND (lc.contract_sn like '%" . trim($p['search_text']) . "%' OR cm.display_name like '%" . trim($p['search_text']) . "%')";
        }
        $sql .= " ORDER BY lw.create_time DESC";
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
            "pageSize" => $pageSize,
            "cur_uid" => $this->user_id,
            "type" => trim($p['type']),
        );
    }

    /**
     * 审核还款申请
     */
    public function auditWriteOffOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_writtenoff = M('loan_writtenoff');
        $row = $m_loan_writtenoff->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if ($p['form_submit'] == 'ok') {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            if ($row->auditor_id == $this->user_id && $row->state == writeOffStateEnum::APPROVING) {
                $state = $p['state'] == 'approve' ? writeOffStateEnum::COMPLETE : writeOffStateEnum::INVALID;
                $row->state = $state;
                $row->update_time = Now();
                if ($state == writeOffStateEnum::COMPLETE) {
                    $row->close_date = Now();
                    $rt = loan_contractClass::contractWriteOff($uid, array('auditor_id' => $this->user_id, 'auditor_name' => $this->user_name));
                    if (!$rt->STS) {
                        $conn->rollback();
                        showMessage($rt->MSG);
                    }
                }
                $rt_1 = $row->update();
                if ($rt_1->STS) {
                    $conn->submitTransaction();
                    showMessage('Audit successful!', getUrl('loan', 'writeOff', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
                } else {
                    $conn->rollback();
                    showMessage('Audit failed!' . $rt->MSG);
                }

            } else {
                showMessage('Param error!');
            }
        }

        //审核中
        if ($row->state == writeOffStateEnum::APPROVING) {
            // 超时放开，让别人可审 1小时
            if ((strtotime($row['update_time']) + 3600) < time()) {
                $row->state = writeOffStateEnum::APPROVING;
                $row->auditor_id = $this->user_id;
                $row->auditor_name = $this->user_name;
                $row->update_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG);
                }
            }
        } elseif ($row->state == writeOffStateEnum::CREATE) {
            $row->state = writeOffStateEnum::APPROVING;
            $row->auditor_id = $this->user_id;
            $row->auditor_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
        }
        $lock = false;
        if ($this->user_id != $row['auditor_id']) {
            //审核中
            $lock = true;
        }
        Tpl::output('lock', $lock);
        $r = new ormReader();
        $sql = "SELECT lw.*,lc.contract_sn,cm.display_name FROM loan_writtenoff lw"
            . " INNER JOIN loan_contract lc ON lw.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " WHERE lw.uid = $uid";
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);
        Tpl::showpage('write.off.audit');

    }

}
