<?php

class baseControl extends control
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;
    public $user_position;

    function __construct()
    {
        parent::__construct();
        Language::read('auth');
        $user = userBase::Current();
        $user_info = $user->property->toArray();
        $user_info['user_position'] = my_json_decode($user_info['user_position']);
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];
        $this->user_position = $user_info['user_position'];
        $auth_arr = $user->getAuthList();
        $this->auth_list = $auth_arr['back_office'];
    }

    protected function getProcessingTask(){
        //是否有进行的任务
        if (in_array(userPositionEnum::OPERATOR, $this->user_position)) {
            $m_um_user_operator_task = M('um_user_operator_task');
            $processing_task = $m_um_user_operator_task->find(array('user_id' => $this->user_id, 'task_state' => 10));
            if (!$processing_task) {
                $processing_task = array(
                    'url' => '',
                    'title' => "<None>"
                );
                Tpl::output('processing_task', $processing_task);
            } else {
                if ($processing_task['task_type'] == operateTypeEnum::NEW_CLIENT) {
                    $processing_task = array(
                        'url' => getUrl('operator', 'checkNewClient', array('uid' => $processing_task['task_id'], 'show_menu_a' => $processing_task['task_type']), false, BACK_OFFICE_SITE_URL),
                        'title' => "<New Register>"
                    );
                } else if ($processing_task['task_type'] == operateTypeEnum::REQUEST_LOAN) {
                    $processing_task = array(
                        'url' => getUrl('operator', 'operateRequestLoan', array('uid' => $processing_task['task_id'], 'show_menu_a' => $processing_task['task_type']), false, BACK_OFFICE_SITE_URL),
                        'title' => "<Request Loan>"
                    );
                } else if ($processing_task['task_type'] == operateTypeEnum::CERTIFICATION_FILE) {
                    $m_member_verify_cert = M('member_verify_cert');
                    $verify_cert = $m_member_verify_cert->find(array('uid' => $processing_task['task_id']));

                    $processing_task = array(
                        'url' => getUrl('operator', 'certificationDetail', array('uid' => $processing_task['task_id'], 'show_menu_a' => $processing_task['task_type'], 'show_menu_b' => $verify_cert['cert_type']), false, BACK_OFFICE_SITE_URL),
                        'title' => "<Certification File>"
                    );
                }
                Tpl::output('processing_task', $processing_task);
            }
        }
    }

    /**
     * 根据权限获取menu
     * @return array
     */
    protected function getResetMenu()
    {
        if (in_array(userPositionEnum::OPERATOR, $this->user_position)) {
            Language::read('certification');
            $index_menu = $this->getOperatorMenu();
            $certification_type = enum_langClass::getCertificationTypeEnumLang();
            unset($certification_type[certificationTypeEnum::GUARANTEE_RELATIONSHIP]);
            $index_menu['certification_file']['child'] = $certification_type;
            return $index_menu;
        } else {
            $index_menu = $this->getIndexMenu();
            foreach ($index_menu as $key => $menu) {
                foreach ($menu['child'] as $k => $child) {
                    $argc = explode(',', $child['args']);
                    $auth = $argc[1] . '_' . $argc[2];
                    if (!in_array($auth, $this->auth_list)) {
                        unset($index_menu[$key]['child'][$k]);
                    }
                }
                if (empty($index_menu[$key]['child'])) {
                    unset($index_menu[$key]);
                }
            }
            return $index_menu;
        }
    }

    /**
     * 定义menu
     * @return array
     */
    private function getIndexMenu()
    {
        $indexMenu = array(
            'home' => array(
                "title" => "Home",
                'child' => array(
                    array('args' => 'microbank/backoffice,home,monitor', 'title' => 'Monitor')
                )
            ),
            'user' => array(
                "title" => 'Hr',
                'child' => array(
                    array('args' => 'microbank/backoffice,user,branch', 'title' => 'Branch'),
                    array('args' => 'microbank/backoffice,user,role', 'title' => 'Role'),
                    array('args' => 'microbank/backoffice,user,user', 'title' => 'User'),
                    array('args' => 'microbank/backoffice,user,log', 'title' => 'User Log'),
                    array('args' => 'microbank/backoffice,user,pointEvent', 'title' => 'Point Event'),
                    array('args' => 'microbank/backoffice,user,pointPeriod', 'title' => 'Point Period'),
                    array('args' => 'microbank/backoffice,user,departmentPoint', 'title' => 'Department Point'),
                )
            ),
            'client' => array(
                "title" => 'Client',
                'child' => array(
                    array('args' => 'microbank/backoffice,client,client', 'title' => 'Client'),
                    array('args' => 'microbank/backoffice,client,cerification', 'title' => 'Certification File'),
                    array('args' => 'microbank/backoffice,client,blackList', 'title' => 'Black List'),
                    array('args' => 'microbank/backoffice,client,grade', 'title' => 'Grade'),
                )
            ),
            'partner' => array(
                "title" => 'Partner',
                'child' => array(
                    array('args' => 'microbank/backoffice,partner,bank', 'title' => 'Bank'),
                    array('args' => 'microbank/backoffice,partner,dealer', 'title' => 'Dealer'),
                )
            ),
            'loan' => array(
                "title" => 'Loan',
                'child' => array(
                    array('args' => 'microbank/backoffice,loan,product', 'title' => 'Product'),
                    array('args' => 'microbank/backoffice,loan,credit', 'title' => 'Grant Credit'),
                    array('args' => 'microbank/backoffice,loan,approval', 'title' => 'Approval Credit'),
                    array('args' => 'microbank/backoffice,loan,apply', 'title' => 'Request To Loan'),

                    array('args' => 'microbank/backoffice,loan,requestToPrepayment', 'title' => 'Request To Prepayment'),
                    array('args' => 'microbank/backoffice,loan,requestToRepayment', 'title' => 'Repayment'),
                    array('args' => 'microbank/backoffice,loan,contract', 'title' => 'Contract'),
                    array('args' => 'microbank/backoffice,loan,writeOff', 'title' => 'Write Off'),
                    array('args' => 'microbank/backoffice,loan,overdue', 'title' => 'Overdue'),
                    array('args' => 'microbank/backoffice,loan,deductingPenalties', 'title' => 'Deducting Penalties'),
                )
            ),
            'insurance' => array(
                "title" => 'Insurance',
                'child' => array(
                    array('args' => 'microbank/backoffice,insurance,product', 'title' => 'Insurance Product'),
                    array('args' => 'microbank/backoffice,insurance,contract', 'title' => 'Insurance Contract'),
                )
            ),
            'setting' => array(
                "title" => 'Setting',
                'child' => array(
                    array('args' => 'microbank/backoffice,setting,companyInfo', 'title' => 'Company Info'),
                    array('args' => 'microbank/backoffice,setting,creditLevel', 'title' => 'Credit Level'),
                    array('args' => 'microbank/backoffice,setting,creditProcess', 'title' => 'Credit Process'),
                    array('args' => 'microbank/backoffice,setting,global', 'title' => 'Global'),
                    array('args' => 'microbank/backoffice,region,list', 'title' => 'Region'),
//                    array('args' => 'microbank/backoffice,setting,systemDefine', 'title' => 'System Define'),
                    array('args' => 'microbank/backoffice,setting,shortCode', 'title' => 'Short Code'),
                    array('args' => 'microbank/backoffice,setting,codingRule', 'title' => 'Coding Rule'),
                    array('args' => 'microbank/backoffice,setting,resetSystem', 'title' => 'Reset System'),

                )
            ),
            'financial' => array(
                "title" => 'Financial',
                'child' => array(
                    array('args' => 'microbank/backoffice,financial,bankAccount', 'title' => 'Bank Account'),
                    array('args' => 'microbank/backoffice,financial,exchangeRate', 'title' => 'Exchange Rate'),
                )
            ),
            'report' => array(
                "title" => 'Report',
                'child' => array(
                    array('args' => 'microbank/backoffice,report,reportOverview', 'title' => 'Overview'),
                    array('args' => 'microbank/backoffice,report,clientList', 'title' => 'Client List'),
                    array('args' => 'microbank/backoffice,report,contractList', 'title' => 'Contract List'),
                    array('args' => 'microbank/backoffice,report,creditList', 'title' => 'Credit List'),
                    array('args' => 'microbank/backoffice,report,todayReport', 'title' => 'Today\'s Report'),
                    array('args' => 'microbank/backoffice,report,loanList', 'title' => 'Loan List'),
                    array('args' => 'microbank/backoffice,report,repaymentList', 'title' => 'Repayment List'),
                    array('args' => 'microbank/backoffice,report,assetLiability', 'title' => 'Asset Liability'),
                    array('args' => 'microbank/backoffice,report,profit', 'title' => 'Profit'),
                )
            ),
            'editor' => array(
                "title" => 'Editor',
                'child' => array(
                    array('args' => 'microbank/backoffice,editor,help', 'title' => 'Cms')
                )
            ),
            'tools' => array(
                "title" => 'Tools',
                'child' => array(
                    array('args' => 'microbank/backoffice,tools,calculator', 'title' => 'Calculator'),
                    array('args' => 'microbank/backoffice,tools,sms', 'title' => 'SMS'),
                )
            ),
            'dev' => array(
                "title" => 'Dev',
                'child' => array(
                    array('args' => 'microbank/backoffice,dev,appVersion', 'title' => 'App Version'),
                    array('args' => 'microbank/backoffice,dev,functionSwitch', 'title' => 'Function Switch'),
                    array('args' => 'microbank/backoffice,dev,resetPassword', 'title' => 'Reset Password'),
                )
            )
        );
        return $indexMenu;
    }

    /**
     * 定义menu
     * @return array
     */
    private function getOperatorMenu()
    {
        $indexMenu = array(
            'new_client' => array(
                "title" => "New Client",
                'args' => "microbank/backoffice,operator,newClient"
            ),
            'request_loan' => array(
                "title" => "Request Loan",
                'args' => "microbank/backoffice,operator,requestLoan"
            ),
            'certification_file' => array(
                "title" => "Certification File",
                'args' => "microbank/backoffice,operator,certificationFile"
            ),
            'grant_credit' => array(
                "title" => "Grant Credit",
                'args' => "microbank/backoffice,operator,grantCredit"
            ),
            'request_lock' => array(
                "title" => "Request To Lock",
                'args' => "microbank/backoffice,operator,requestLock"
            ),
            'complaint_advice' => array(
                "title" => "Complaint and Advice",
                'args' => "microbank/backoffice,operator,addComplaintAdvice"
            )
        );
        return $indexMenu;
    }

    /**
     * 获取任务数
     * @return result
     */
    public function getTaskNumOp()
    {
        $r = new ormReader();
        $sql = "SELECT COUNT(uid) new_client_num FROM client_member WHERE operate_state = 0 AND member_state != 0";
        $new_client_num = $r->getOne($sql);

        $sql = "SELECT COUNT(uid) request_loan FROM loan_apply WHERE state = " . loanApplyStateEnum::CREATE;
        $request_loan = $r->getOne($sql);

        $sql = "SELECT COUNT(uid) num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::ID . " THEN 1 ELSE 0 END) id_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::PASSPORT . " THEN 1 ELSE 0 END) passport_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::FAIMILYBOOK . " THEN 1 ELSE 0 END) family_book_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::WORK_CERTIFICATION . " THEN 1 ELSE 0 END) work_certification_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::CAR . " THEN 1 ELSE 0 END) car_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::HOUSE . " THEN 1 ELSE 0 END) house_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::LAND . " THEN 1 ELSE 0 END) land_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::RESIDENT_BOOK . " THEN 1 ELSE 0 END) resident_book_num,"
            . " SUM(CASE WHEN cert_type =" . certificationTypeEnum::MOTORBIKE . " THEN 1 ELSE 0 END) motorbike_num"
            . " FROM member_verify_cert WHERE verify_state = 0";
        $cert_file = $r->getRow($sql);
        $cert_file_arr = array(
            certificationTypeEnum::ID => intval($cert_file['id_num']),
            certificationTypeEnum::PASSPORT => intval($cert_file['passport_num']),
            certificationTypeEnum::FAIMILYBOOK => intval($cert_file['family_book_num']),
            certificationTypeEnum::WORK_CERTIFICATION => intval($cert_file['work_certification_num']),
            certificationTypeEnum::CAR => intval($cert_file['car_num']),
            certificationTypeEnum::HOUSE => intval($cert_file['house_num']),
            certificationTypeEnum::LAND => intval($cert_file['land_num']),
            certificationTypeEnum::RESIDENT_BOOK => intval($cert_file['resident_book_num']),
            certificationTypeEnum::MOTORBIKE => intval($cert_file['motorbike_num']),
        );

        $data = array(
            'new_client' => intval($new_client_num),
            'request_loan' => intval($request_loan),
            'certification_file' => intval($cert_file['num']),
            'certification_file_arr' => $cert_file_arr,
        );
        return new result(true, '', $data);
    }

}
