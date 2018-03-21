<?php

class toolsControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('tools');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Home");
        Tpl::setDir("tools");
    }

    /**
     * 计算器
     */
    public function calculatorOp()
    {

        $class_product = new product();
        $valid_products = $class_product->getValidProductList();
        Tpl::output("valid_products", $valid_products);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);

        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();
        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("interest_rate_period", $interest_rate_period);

        Tpl::showPage("calculator");
    }

    /**
     * 贷款计算
     * @param $p
     * @return result
     */
    public function loanPreviewOp($p)
    {
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->loanPreview($p);
        if (!$re->STS) {
            return $re;
        }
        $data = $re->DATA;
        $data_new = array();
        $data_new['loan_amount'] = ncAmountFormat($data['total_repayment']['total_principal']);
        $data_new['repayment_amount'] = ncAmountFormat($data['total_repayment']['total_payment']);
        $data_new['arrival_amount'] = ncAmountFormat($data['arrival_amount']);
        $data_new['service_charge'] = ncAmountFormat($data['loan_fee']);
        $data_new['total_interest'] = ncAmountFormat($data['total_repayment']['total_interest']);
        $data_new['period_repayment_amount'] = ncAmountFormat($data['period_repayment_amount']);
        $data_new['interest_rate'] = $data['interest_rate_type'] == 0 ? ($data['interest_rate'] . '%') : ncAmountFormat($data['interest_rate']);
        $data_new['interest_rate_unit'] = $data['interest_rate_unit'];
        $data_new['repayment_number'] = count($data['repayment_schema']);
        if ($data_new['repayment_number'] > 1) {
            $first_repayment = array_shift($data['repayment_schema']);
            $second_repayment = array_shift($data['repayment_schema']);
            if ($first_repayment['amount'] == $second_repayment['amount']) {
                $data_new['each_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['first_repayment'] = 0;
            } else {
                $data_new['first_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['each_repayment'] = 0;
            }
        } else {
            $first_repayment = array_shift($data['repayment_schema']);
            $data_new['single_repayment'] = ncAmountFormat($first_repayment['amount']);
            $data_new['first_repayment'] = 0;
            $data_new['each_repayment'] = 0;
        }
        $data_new['operation_fee'] = ncAmountFormat($first_repayment['receivable_operation_fee']);
        $re->DATA = $data_new;
        return $re;
    }

    /**
     * sms
     */
    public function smsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('sms');
    }

    /**
     * @param $p
     * @return array
     */
    public function getSmsListOp($p)
    {
        $search_text = trim($p['search_text']);
        $need_resend = intval($p['need_resend']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM common_sms WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND task_state != " . smsTaskState::CANCEL;
        if ($search_text) {
            $sql .= " AND phone_id like '%" . $search_text . "%'";
        }
        if ($need_resend) {
            $sql .= " AND task_state = " . smsTaskState::SEND_FAILED;
        }
        $sql .= ' ORDER BY uid DESC';
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

    public function resendSmsOp($p)
    {
        $uid = intval($p['uid']);
        $m_common_sms = M('common_sms');
        $sms_row = $m_common_sms->getRow($uid);
        if (!$sms_row) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Invalid Id!', $data);
        }
        if ($sms_row->task_state != smsTaskState::SEND_FAILED) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Sms state error!', $data);
        }

        $smsHandler = new smsHandler();
        if ($sms_row->task_type == smsTaskType::VERIFICATION_CODE) {
            // 发送短信验证码
            $verify_code = mt_rand(100001, 999999);
            $contact_phone = $sms_row->phone_id;

            $rt = $smsHandler->sendVerifyCode($contact_phone, $verify_code);
            if (!$rt->STS) {
                $data = array('state' => 'Resend Failed');
                return new result(false, 'Send code fail: ' . $rt->MSG, $data);
            }
            $data = $rt->DATA;
            $content = $data->content;
            $conn = ormYo::Conn();
            $conn->startTransaction();
            try {
                $m_phone_verify_code = M('common_verify_code');
                $new_row = $m_phone_verify_code->newRow();
                $new_row->phone_country = $sms_row->phone_country;
                $new_row->phone_id = $contact_phone;
                $new_row->verify_code = $verify_code;
                $new_row->create_time = Now();
                $new_row->sms_id = $sms_row->uid;
                $insert = $new_row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Insert verify code fail');
                }

                $sms_row->task_state = smsTaskState::CANCEL;
                $sms_row->update_time = Now();
                $update = $sms_row->update();
                if (!$update->STS) {
                    $conn->rollback();
                    $data = array('state' => 'Resend Failed');
                    return new result(false, 'Update sms fail', $data);
                }
                $conn->submitTransaction();
                $data = array('content' => $content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } catch (Exception $ex) {
                $conn->rollback();
                return new result(false, $ex->getMessage());
            }

        } else {
            $rt = $smsHandler->resend($uid);
            if ($rt->STS) {
                $data = $rt->DATA;
                $data = array('content' => $data->content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } else {
                $data = array('state' => 'Resend Failed');
                return new result(true, 'Resend failed!', $data);
            }
        }
    }
}
