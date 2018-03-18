<?php

class settingControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Company Info");
        Tpl::setDir("setting");

        Language::read('setting,certification');
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output("cert_verify_lang", $verify_field);
    }

    /**
     * 查看公司信息
     */
    public function companyInfoOp()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('company_config');
        if ($data) {
            tpl::output('company_config', my_json_decode($data['dict_value']));
        }
        Tpl::showPage("company.info");
    }

    /**
     * 修改公司信息
     */
    public function editCompanyInfoOp()
    {
        $m_core_dictionary = M('core_dictionary');
        if ($_POST['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            if (empty($param['hotline'])) {
                $param['hotline'] = array();
            } else {
                $param['hotline'] = array_unique($param['hotline']);
            }

            $rt = $m_core_dictionary->updateDictionary('company_config', my_json_encode($param));
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('setting', 'companyInfo', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $data = $m_core_dictionary->getDictionary('company_config');
            if ($data) {
                $company_config = my_json_decode($data['dict_value']);
                tpl::output('company_config', $company_config);
            }
            $address_id = $company_config['address_id'];
            $m_core_tree = M('core_tree');
            $region_list = $m_core_tree->getParentAndBrotherById($address_id, 'region');
            Tpl::output('region_list', $region_list);
            Tpl::showPage("company.edit");
        }
    }

    /**
     * 编码规则
     */
    public function codingRuleOp()
    {
        var_dump('Todo soon');
//        Tpl::showPage("coding_rule");
    }

    /**
     * 配置设置
     */
    public function globalOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_dictionary = M('core_dictionary');
        if ($p['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            $param['credit_register'] = round($param['credit_register'], 2);
            $param['credit_without_approval'] = round($param['credit_without_approval'], 2);
            $param['credit_system_limit'] = round($param['credit_system_limit'], 2);
            $param['withdrawal_single_limit'] = round($param['withdrawal_single_limit'], 2);
            $param['withdrawal_monitor_limit'] = round($param['withdrawal_monitor_limit'], 2);
            $param['operator_credit_maximum'] = round($param['operator_credit_maximum'], 2);
            $param['date_format'] = intval($param['date_format']);
            $param['is_trade_password'] = intval($param['is_trade_password']);
            $param['is_create_savings_account'] = intval($param['is_create_savings_account']);
            $rt = $m_core_dictionary->updateDictionary('global_settings', my_json_encode($param));
            showMessage($rt->MSG);
        } else {
            $data = $m_core_dictionary->getDictionary('global_settings');
            if ($data) {
                tpl::output('global_settings', my_json_decode($data['dict_value']));
            }
            Tpl::showPage("global.setting");
        }
    }

    /**
     * 系统枚举定义
     * 弃用
     */
    public function systemDefineOp()
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->initSystemDefine();
        if (!$rt->STS) {
            showMessage('Init Failure!');
        } else {
            Tpl::showpage('system.define');
        }
    }

    /**
     * 获取define列表
     * @param $p
     * @return mixed
     */
    public function getDefineListOp($p)
    {
        $m_core_definition = M('core_definition');
        $define_list = $m_core_definition->getDefineList($p);
        return $define_list;
    }

    /**
     * 修改define 分类名称
     * @param $p
     */
    public function editCategoryNameOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->editCategoryName($p);
        return $rt;
    }

    /**
     * 编辑define item
     * @param $p
     * @return mixed
     */
    public function editDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->editDefineItem($p);
        return $rt;
    }

    /**
     * user.define
     */
    public function shortCodeOp()
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->initUserDefine();
        if (!$rt->STS) {
            showMessage('Init Failure!');
        } else {
            $lang_list = C('lang_type_list');
            Tpl::output('lang_list', $lang_list);
            Tpl::showpage('user.define');
        }
    }

    /**
     * 添加define item
     * @param $p
     * @return mixed
     */
    public function addDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->addDefineItem($p);
        return $rt;
    }

    /**
     * 移除define item
     * @param $p
     * @return mixed
     */
    public function removeDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->removeDefineItem($p);
        return $rt;
    }


    public function creditLevelOp()
    {
        $return = credit_loanClass::getCreditLevelList();
        $type_lang = array(
            creditLevelTypeEnum::MEMBER => 'Member',
            creditLevelTypeEnum::MERCHANT => 'Merchant'
        );
        Tpl::output('credit_level', $return);
        Tpl::output('level_type_lang', $type_lang);
        Tpl::showPage('credit.level.list');
    }

    public function addCreditLevelOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        if ($params['form_submit'] == 'ok') {
            $min_amount = $params['min_amount'];
            $max_amount = $params['max_amount'];

            $cert_list = $params['cert_list'];
            if ($min_amount < 0 || $max_amount <= 0) {
                showMessage('Amount Invalid');
            }
            if ($min_amount >= $max_amount) {
                showMessage('Min amount more than max amount');
            }

            if (empty($cert_list)) {
                showMessage('Did not select certification');
            }

            $conn = ormYo::Conn();

            try {
                $conn->startTransaction();

                $re = credit_loanClass::addCreditLevel($params);
                if (!$re->STS) {
                    $conn->rollback();
                    showMessage('Add fail');
                }

                $conn->submitTransaction();
                showMessage('Add success', getUrl('setting', 'creditLevel', array(), false, BACK_OFFICE_SITE_URL));

            } catch (Exception $e) {
                showMessage('Add fail');
            }

        }
        Tpl::showPage('credit.level.add');
    }

    public function editCreditLevelOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        if ($params['form_submit'] == 'ok') {

            $min_amount = $params['min_amount'];
            $max_amount = $params['max_amount'];

            $cert_list = $params['cert_list'];
            if ($min_amount < 0 || $max_amount <= 0) {
                showMessage('Amount Invalid');
            }
            if ($min_amount >= $max_amount) {
                showMessage('Min amount more than max amount');
            }

            if (empty($cert_list)) {
                showMessage('Did not select certification');
            }

            $conn = ormYo::Conn();
            try {
                $conn->startTransaction();
                $re = credit_loanClass::editCreditLevel($params);
                if (!$re->STS) {
                    $conn->rollback();
                    showMessage('Edit fail');
                }
                $conn->submitTransaction();
                showMessage('Edit success');

            } catch (Exception $e) {
                showMessage('Edit fail');
            }

        } else {
            $uid = $params['uid'];
            if (!$uid) {
                showMessage('Invalid param');
            }
            $m_level = new loan_credit_cert_levelModel();
            $row = $m_level->getRow($uid);
            if (!$row) {
                showMessage('No data!');
            }
            $sql = "select cert_type from loan_credit_level_cert_list where cert_level_id='$uid' ";
            $cert_list = array();
            $list = $m_level->reader->getRows($sql);
            foreach ($list as $v) {
                $cert_list[] = $v['cert_type'];
            }

            $level_info = $row->toArray();
            $level_info['cert_list'] = $cert_list;
            Tpl::output('level_info', $level_info);
            Tpl::showPage('credit.level.edit');
        }

    }

    public function deleteCreditLevelOp($p)
    {
        $id = $p['id'];
        return credit_loanClass::deleteCreditLevel($id);
    }


    public function creditProcessOp()
    {
        $all_dict = global_settingClass::getAllDictionary();
        Tpl::output('all_dict',$all_dict);
        Tpl::showPage('credit.process.list');
    }

    public function openCreditProcessOp($p)
    {
        $type = $p['type'];
        $m = new core_dictionaryModel();
        switch( $type )
        {
            case 1:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_fingerprint_cert',
                ));
                if( $row ){
                    if( $row->dict_value == 0 ){
                        return new result(false,'Is opened!');
                    }
                    $row->dict_value = 0;
                    $up = $row->update();
                    if( !$up->STS ){
                        return new result(false,'Open fail!');
                    }
                    return new result(true,'Success!');
                }else{
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_fingerprint_cert';
                    $row->dict_value = 0;
                    $insert = $row->insert();
                    if( !$insert->STS ){
                        return new result(false,'Open fail!');
                    }
                    return new result(true,'Success!');

                }
                break;
            case 2:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_authorized_contract',
                ));
                if( $row ){
                    if( $row->dict_value == 0 ){
                        return new result(false,'Is opened!');
                    }
                    $row->dict_value = 0;
                    $up = $row->update();
                    if( !$up->STS ){
                        return new result(false,'Open fail!');
                    }
                    return new result(true,'Success!');

                }else{
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_authorized_contract';
                    $row->dict_value = 0;
                    $insert = $row->insert();
                    if( !$insert->STS ){
                        return new result(false,'Open fail!');
                    }
                    return new result(true,'Success!');

                }
                break;
            default:
                return new result(false,'Unknown function');
                break;
        }
    }

    public function closeCreditProcessOp($p)
    {
        $type = $p['type'];
        $m = new core_dictionaryModel();
        switch( $type )
        {
            case 1:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_fingerprint_cert',
                ));
                if( $row ){
                    if( $row->dict_value == 1 ){
                        return new result(false,'Is Closed!');
                    }
                    $row->dict_value = 1;
                    $up = $row->update();
                    if( !$up->STS ){
                        return new result(false,'Close fail!');
                    }
                    return new result(true,'Success!');
                }else{
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_fingerprint_cert';
                    $row->dict_value = 1;
                    $insert = $row->insert();
                    if( !$insert->STS ){
                        return new result(false,'Close fail!');
                    }
                    return new result(true,'Success!');

                }
                break;
            case 2:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_authorized_contract',
                ));
                if( $row ){
                    if( $row->dict_value == 1 ){
                        return new result(false,'Is Closed!');
                    }
                    $row->dict_value = 1;
                    $up = $row->update();
                    if( !$up->STS ){
                        return new result(false,'Close fail!');
                    }
                    return new result(true,'Success!');

                }else{
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_authorized_contract';
                    $row->dict_value = 1;
                    $insert = $row->insert();
                    if( !$insert->STS ){
                        return new result(false,'Close fail!');
                    }
                    return new result(true,'Success!');

                }
                break;
            default:
                return new result(false,'Unknown function');
                break;
        }
    }

    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

    /**
     * 重置系统
     */
    public function resetSystemOp()
    {
        Tpl::showPage('reset.system');
    }

    public function resetSystemConfirmOp()
    {
        $is_can = intval(getConf('is_open_reset_system'));
        if( $is_can !== 1 ){
            return new result(false,'Function closed!');
        }
        // todo 检查操作人权限
        $re = global_settingClass::resetSystemData();
        if( $re == true ){
            return new result(true,'success');
        }else{
            return new result(false,'Reset fail!');
        }

    }

}
