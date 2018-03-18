<?php

abstract class loan_handlerClass {
    protected $handler_info;

    protected function __construct($handler_info)
    {
        $this->handler_info = $handler_info;
    }

    private static $_cached_handlers = array();

    /**
     * @param $id
     * @return loan_handlerClass
     */
    public static function getHandler($id) {
        if (!array_key_exists($id, self::$_cached_handlers)) {
            $m = new member_account_handlerModel();
            $handler_info = $m->getRow($id);

            switch ($handler_info->handler_type) {
                case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                    self::$_cached_handlers[$id] = new loan_asiaweiluy_handlerClass($handler_info);
                    break;
                default:
                    self::$_cached_handlers[$id] = null;
                    break;
            }
        }
        return self::$_cached_handlers[$id];
    }

    public function disburse($schema) {
        if (strtotime($schema->disbursable_date) > time()) {
            return new result(false, 'The planned time has not been reached', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $schema->state = schemaStateTypeEnum::GOING;
        $rt = $schema->update();
        if (!$rt->STS) {
            return new result(false, 'Update schema state failed', null, errorCodesEnum::DATA_INCONSISTENCY);
        }

        $disbursement_model = new loan_disbursementModel();
        $disbursement_log = $disbursement_model->newRow();
        $disbursement_log->scheme_id = $schema->uid;
        $disbursement_log->contract_id = $schema->contract_id;
        $disbursement_log->currency = 'USD';
        $disbursement_log->amount = $schema->amount;
        $disbursement_log->receiver_id = $this->handler_info->uid;
        $disbursement_log->receiver_type = $this->handler_info->handler_type;
        $disbursement_log->receiver_name = $this->handler_info->handler_name;
        $disbursement_log->receiver_phone = $this->handler_info->handler_phone;
        $disbursement_log->receiver_account = $this->handler_info->handler_account;
        $disbursement_log->receiver_property = $this->handler_info->handler_property;
        $disbursement_log->create_time = date("Y-m-d H:i:s");

        $special_info = $this->getDisbursementSpecialInfo();

        $disbursement_log->branch_id = $special_info['branch_id'];
        $disbursement_log->teller_id = $special_info['teller_id'];
        $disbursement_log->teller_name = $special_info['teller_name'];
        $disbursement_log->creator_id = $special_info['creator_id'];
        $disbursement_log->creator_name = $special_info['creator_name'];
        $disbursement_log->gl_invoice_id = $special_info['gl_invoice_id'];

        $disbursement_log->state = disbursementStateEnum::GOING;
        $rt = $disbursement_log->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert disbursement log failed', null, errorCodesEnum::DB_ERROR);
        }

        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $contract_info = $contract_model->getRow($schema->contract_id);
        $product_info = $product_model->getRow($contract_info->product_id);
        $ret = $this->apiDisbursementExecute($schema->amount, $contract_info->currency, 'Samrithisak Disbursement: ' . $product_info->product_name . " - " . $contract_info->contract_sn);

        if ($ret->STS) {
            $disbursement_log->state = disbursementStateEnum::DONE;
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }

            $schema->state = schemaStateTypeEnum::COMPLETE;
            $schema->done_time = date("Y-m-d H:i:s");
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }

            return new result(true);
        } else if ($ret->CODE != errorCodesEnum::UNKNOWN_ERROR) {
            $disbursement_log->state = disbursementStateEnum::FAILED;
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            $schema->state = schemaStateTypeEnum::FAILURE;
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            return new result(false, 'API failed', null, errorCodesEnum::API_FAILED, $ret);
        } else {
            return $ret;
        }
    }


    /** 自动扣款
     * @param $handler_id
     * @param $amount
     * @param string $currency
     * @return result
     */
    public function automaticDeduction($refBiz,$amount,$currency,$description)
    {
        return $this->apiInstallmentExecute($refBiz,$amount, $currency, $description,false);

    }

    /**
     * @return array
     */
    protected abstract function getDisbursementSpecialInfo();

    /**
     * @param $refBiz array 外部业务信息
     * @param $amount
     * @param $currency
     * @param $description
     * @return result
     */
    public abstract function apiDisbursementExecute($refBiz, $amount, $currency, $description);

    /**
     * @return array
     */
    protected abstract function getInstallmentSpecialInfo();

    /**
     * @param $refBiz array 外部业务信息
     * @param $amount
     * @param $currency
     * @param $description
     * @param $maximizationDeduction
     * @return result
     */
    public abstract function apiInstallmentExecute($refBiz, $amount, $currency, $description, $maximizationDeduction=true);
}