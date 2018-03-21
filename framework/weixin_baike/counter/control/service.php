<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/5
 * Time: 10:48
 */
class serviceControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("service");
        Language::read('service');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('service');

    }

//   requestLoan页面展示
    public function requestLoanOp()
    {
        Tpl::showPage("request.loan");
    }

//   currencyExchange页面展示
    public function currencyExchangeOp()
    {
        Tpl::showPage("coming.soon");
    }

//   查询贷款申请列表，分页展示
    public function getRequestLoanListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT la.*,uu.user_name,uu.user_code FROM loan_apply la LEFT JOIN um_user uu ON la.credit_officer_id = uu.uid"
            . " WHERE la.creator_id = " . $this->user_id;
        if($search_text){
            $sql .= " AND la.applicant_name like '%" . $search_text . "%'";
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

//    添加贷款申请
    public function addRequestLoanOp()
    {
        Tpl::output('show_menu','requestLoan');
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
//            $m_loan_apply = M('loan_apply');
            $m_loan_apply = new loan_applyModel();
            $p['request_source'] = loanApplySourceEnum::CLIENT;
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_loan_apply->addApply($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('service', 'requestLoan', array(), false, ENTRY_COUNTER_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('service', 'addRequestLoan', $p, false, ENTRY_COUNTER_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type'));
            Tpl::output('mortgage_type', $define_arr['mortgage_type']);

            $apply_source = (new loanApplySourceEnum)->Dictionary();
            Tpl::output('request_source', $apply_source);
            Tpl::showPage("add.request.loan");
        }

    }

//    返回区域信息
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

//  删除新增申请
    public function deleteRequestLoanOp(){
        $uid = $_GET["uid"];
        $m_loan_apply =M("loan_apply");
        $r = $m_loan_apply->delete(array("uid"=>$uid));
        if($r->STS){
            showMessage("Delete Success");
        }else{
            showMessage("Delete failure");
        }

    }

}