<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class product
{
    /**
     * 获取产品列表
     * @return result
     */
    public function getValidProductList()
    {
        $m_loan_product = M('loan_product');
        $product_list = $m_loan_product->select(array('state' => loanProductStateEnum::ACTIVE));
        return $product_list;
    }

    /**
     * 获取产品详情
     * @param $uid
     * @return result
     */
    public function getProductInfoById($uid)
    {
        $uid = intval($uid);
        $m_loan_product = M('loan_product');
        $m_loan_product_condition = M('loan_product_condition');
        $product_info = $m_loan_product->find(array('uid' => $uid));
        if (!$product_info) {
            return new result(false, 'Invalid Id!');
        }
        $product_condition = $m_loan_product_condition->select(array('loan_product_id' => $uid));
        $product_info['condition'] = $product_condition;
        return new result(true, '', $product_info);
    }

    /**
     * 重新创建temporary 产品
     * @param $uid
     * @return result
     */
    private function copyTemporaryProduct($uid)
    {
        $m_loan_product = M('loan_product');
        $product_info = $m_loan_product->find(array('uid' => $uid));
        if (empty($product_info)) {
            return new result(false, 'Invalid Id!');
        }
        $product_key = $product_info['product_key'];
        $chk_temporary = $m_loan_product->find(array('product_key' => $product_key, 'state' => 10));
        if ($chk_temporary) {
            return new result(true, '', array('is_copy' => false, 'uid' => $chk_temporary['uid']));
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $main_row = $m_loan_product->newRow($product_info);
            $main_row->start_time = null;
            $main_row->end_time = null;
            $main_row->state = 10;
            $main_row->update_time = Now();
            $rt_1 = $main_row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Failed!--' . $rt_1->MSG);
            }
            $new_product_id = $rt_1->AUTO_ID;

            $m_loan_product_condition = M('loan_product_condition');
            $product_condition_arr = $m_loan_product_condition->select(array('loan_product_id' => $uid));
            foreach ($product_condition_arr as $product_condition) {
                $product_condition_row = $m_loan_product_condition->newRow($product_condition);
                $product_condition_row->loan_product_id = $rt_1->AUTO_ID;
                $rt_2 = $product_condition_row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Failed!--' . $rt_2->MSG);
                }
            }

            $size_rate_map = array();
            $special_rate_map = array();
            $m_loan_product_size_rate = M('loan_product_size_rate');
            $m_loan_product_special_rate = M('loan_product_special_rate');
            $product_size_rate_arr = $m_loan_product_size_rate->select(array('product_id' => $uid));
            foreach ($product_size_rate_arr as $product_size_rate) {
                $product_size_rate_row = $m_loan_product_size_rate->newRow($product_size_rate);
                $product_size_rate_row->product_id = $rt_1->AUTO_ID;
                $product_size_rate_row->update_time = Now();
                $rt_3 = $product_size_rate_row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Failed!--' . $rt_3->MSG);
                } else {
                    $size_rate_map[$product_size_rate['uid']] = $rt_3->AUTO_ID;
                    $product_special_rate_arr = $m_loan_product_special_rate->select(array('size_rate_id' => $product_size_rate['uid']));
                    foreach ($product_special_rate_arr as $product_special_rate) {
                        $product_special_rate_row = $m_loan_product_special_rate->newRow($product_special_rate);
                        $product_special_rate_row->size_rate_id = $rt_3->AUTO_ID;
                        $product_special_rate_row->update_time = Now();
                        $rt_4 = $product_special_rate_row->insert();
                        if (!$rt_4->STS) {
                            $conn->rollback();
                            return new result(false, 'Failed!--' . $rt_4->MSG);
                        } else {
                            $special_rate_map[$product_special_rate['uid']] = $rt_4->AUTO_ID;
                        }
                    }
                }
            }

            // 移植绑定的保险产品
            // todo 更通用的方式来处理，不用每次移植
            $insurance_items = array();
            $m_insurance_relation = new insurance_product_relationshipModel();
            $insurances = $m_insurance_relation->getRows(array(
                'loan_product_id' => $uid
            ));
            if (count($insurances) > 0) {
                foreach ($insurances as $item) {
                    $new_insurance = $m_insurance_relation->newRow();
                    $new_insurance->loan_product_id = $new_product_id;
                    $new_insurance->insurance_product_item_id = $item['insurance_product_item_id'];
                    $new_insurance->type = $item['type'];
                    $rt = $new_insurance->insert();
                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(false, 'Failed!--' . $rt->MSG);
                    }
                    $insurance_items[] = $item['insurance_product_item_id'];
                }
            }
            $conn->submitTransaction();
            $uid = $rt_1->AUTO_ID;
            return new result(true, '', array('is_copy' => true, 'uid' => $uid, 'new_product_id' => $new_product_id, 'size_rate_map' => $size_rate_map, 'special_rate_map' => $special_rate_map));
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 保存产品主要信息
     * @param $p
     * @return result
     */
    public function insertProductMain($p)
    {
        $product_name = trim($p['product_name']);
        $product_code = trim($p['product_code']);
//        $currency = trim($p['currency']);
        $is_multi_contract = intval($p['is_multi_contract']);
        $is_advance_interest = intval($p['is_advance_interest']);
        $is_editable_interest = intval($p['is_editable_interest']);
        $is_editable_grace_days = intval($p['is_editable_grace_days']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);

        $is_credit_loan = intval($p['is_credit_loan']);

        if (empty($product_name)) {
            return new result(false, 'Name cannot be empty!');
        }
        if (empty($product_code)) {
            return new result(false, 'Code cannot be empty!');
        }

        $m_loan_product = M('loan_product');
        $condition = array('product_code' => $product_code);
        $chk_code = $m_loan_product->find($condition);
        if ($chk_code) {
            return new result(false, 'Code Exist!');
        }

        if( $is_credit_loan ){
            // 信用贷产品只能有一个
            $product = $m_loan_product->getRow(array(
                'is_credit_loan' => 1
            ));
            if( $product ){
                return new result(false,'Credit loan product exist!');
            }
        }

        $row = $m_loan_product->newRow();
        $row->product_code = $product_code;
        $row->product_name = $product_name;
//        $row->currency = $currency;
        $row->is_multi_contract = $is_multi_contract;
        $row->is_advance_interest = $is_advance_interest;
        $row->is_editable_interest = $is_editable_interest;
        $row->is_editable_grace_days = $is_editable_grace_days;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $row->state = 10;
        $row->product_key = md5(uniqid());
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Save Successful!', array('uid' => $rt->AUTO_ID));
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 修改商品主要信息
     * @param $p
     * @return result
     */
    public function updateProductMain($p)
    {
        $uid = trim($p['uid']);
        $product_name = trim($p['product_name']);
        $product_code = trim($p['product_code']);
        $currency = trim($p['currency']);
        $is_multi_contract = intval($p['is_multi_contract']);
        $is_advance_interest = intval($p['is_advance_interest']);
        $is_editable_interest = intval($p['is_editable_interest']);
        $is_editable_grace_days = intval($p['is_editable_grace_days']);
        $is_credit_loan = intval($p['is_credit_loan']);

        if (empty($product_name)) {
            return new result(false, 'Name cannot be empty!');
        }
        if (empty($product_code)) {
            return new result(false, 'Code cannot be empty!');
        }

        $m_loan_product = M('loan_product');
        $product_info = $m_loan_product->getRow($uid);
        if( !$product_info->is_credit_loan ){
            if( $is_credit_loan ){
                // 信用贷产品只能有一个
                $product = $m_loan_product->getRow(array(
                    'is_credit_loan' => 1
                ));
                if( $product ){
                    return new result(false,'Credit loan product exist!');
                }
            }
        }


        $rt = $this->copyTemporaryProduct($uid);
        if (!$rt->STS) {
            return $rt;
        } else {
            $uid = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }


        $row = $m_loan_product->getRow(array('uid' => $uid));
        $chk_code = $m_loan_product->find(array('product_code' => $product_code, 'uid' => array('neq', $uid), 'product_key' => array('neq', $row['product_key'])));
        if ($chk_code) {
            return new result(false, 'Code Exist!');
        }

        $row->product_code = $product_code;
        $row->product_name = $product_name;
        $row->currency = $currency;
        $row->is_multi_contract = $is_multi_contract;
        $row->is_advance_interest = $is_advance_interest;
        $row->is_editable_interest = $is_editable_interest;
        $row->is_editable_grace_days = $is_editable_grace_days;
        $row->is_credit_loan = $is_credit_loan;
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            return new result(false, 'Update Failure!');
        } else {
            if ($is_copy) {
                $data = array('uid' => $uid);
            } else {
                $data = array();
            }
            return new result(true, 'Update Successful!', $data);
        }
    }

    /**
     * 保存罚金信息
     * @param $p
     * @return result
     */
    public function updateProductPenalty($p)
    {
        $uid = intval($p['uid']);
        $penalty_on = trim($p['penalty_on']);
        $penalty_rate = round($p['penalty_rate'], 2);
        $penalty_divisor_days = intval($p['penalty_divisor_days']);
        $is_editable_penalty = intval($p['is_editable_penalty']);

        $rt = $this->copyTemporaryProduct($uid);
        if (!$rt->STS) {
            return $rt;
        } else {
            $uid = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }

        $m_loan_product = M('loan_product');
        $row = $m_loan_product->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        if (empty($penalty_rate)) {
            return new result(false, 'Penalty rate be empty!');
        }
        if (empty($penalty_divisor_days)) {
            return new result(false, 'Penalty divisor days cannot be empty!');
        }

        $row->penalty_on = $penalty_on;
        $row->penalty_rate = $penalty_rate;
        $row->penalty_divisor_days = $penalty_divisor_days;
        $row->is_editable_penalty = $is_editable_penalty;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            if ($is_copy) {
                $data = array('uid' => $uid);
            } else {
                $data = array();
            }
            return new result(true, 'Save Successful!', $data);
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 获取利率设置列表
     * @param $p
     * @return array
     */
    public function getSizeRateList($p)
    {
        $product_id = intval($p['product_id']);
        $m_loan_product_size_rate = M('loan_product_size_rate');
        $list = $m_loan_product_size_rate->orderBy('interest_payment asc,loan_size_min asc,loan_size_max asc,min_term_days asc,max_term_days asc')->select(array('product_id' => $product_id));
        return array('STS' => true, 'data' => $list);
    }

    /**
     * 保存利率
     * @param $p
     * @return result
     */
    public function insertSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $currency = trim($p['currency']);
        $loan_size_min = round($p['loan_size_min'], 2);
        $loan_size_max = round($p['loan_size_max'], 2);
        $min_term_days = intval($p['min_term_days']);
        $max_term_days = intval($p['max_term_days']);
        $guarantee_type = $p['guarantee_type'];
        $mortgage_type = $p['mortgage_type'];
        $interest_payment = $p['interest_payment'];
        if ($interest_payment == interestPaymentEnum::SINGLE_REPAYMENT) {
            $interest_rate_period = '';
        } else {
            $interest_rate_period = $p['interest_rate_period'];
        }
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_unit = trim($p['interest_rate_unit']);
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_unit = trim($p['operation_fee_unit']);
        $operation_min_value = round($p['operation_min_value'], 2);
        $grace_days = intval($p['grace_days']);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        if ($min_term_days > $max_term_days) {
            return new result(false, 'The minimum days cannot exceed the maximum days!');
        }

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $uid = $rt->DATA['uid'];
            $new_product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }

        $m_loan_product_size_rate = M('loan_product_size_rate');
        $param = array(
            'product_id' => $product_id,
            'currency' => $currency,
            'interest_payment' => $interest_payment,
            'guarantee_type' => $guarantee_type,
            'mortgage_type' => $mortgage_type,
        );
        if ($interest_rate_period) $param['interest_rate_period'] = $interest_rate_period;
        $size_rate_arr = $m_loan_product_size_rate->select($param);
        foreach ($size_rate_arr as $size_rate) {
            if ((($loan_size_min > $size_rate['loan_size_min'] && $loan_size_min < $size_rate['loan_size_max']) || ($loan_size_min > $size_rate['loan_size_min'] && $loan_size_min < $size_rate['loan_size_max'])) && (($min_term_days > $size_rate['min_term_days'] && $min_term_days < $size_rate['max_term_days']) || ($max_term_days > $size_rate['min_term_days'] && $max_term_days < $size_rate['max_term_days']))) {
                return new result(false, 'Conditional Repetition!');
            }
        }

        $row = $m_loan_product_size_rate->newRow();
        $row->product_id = $new_product_id;
        $row->currency = $currency;
        $row->loan_size_min = $loan_size_min;
        $row->loan_size_max = $loan_size_max;
        $row->min_term_days = $min_term_days;
        $row->max_term_days = $max_term_days;
        $row->guarantee_type = $guarantee_type;
        $row->mortgage_type = $mortgage_type;
        $row->interest_payment = $interest_payment;
        $row->interest_rate_period = $interest_rate_period;
        $row->interest_min_value = $interest_min_value;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_unit = $interest_rate_unit;
        $row->interest_rate_type = 0;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_unit = $operation_fee_unit;
        $row->operation_fee_type = 0;
        $row->operation_min_value = $operation_min_value;
        $row->grace_days = $grace_days;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            $data = array('size_rate_id' => $rt->AUTO_ID);
            if ($is_copy) {
                $data['uid'] = array('uid' => $uid);
            }
            return new result(true, 'Save Successful!', $data);
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 更新利率
     * @param $p
     * @return result
     */
    public function updateSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $currency = trim($p['currency']);
        $loan_size_min = round($p['loan_size_min'], 2);
        $loan_size_max = round($p['loan_size_max'], 2);
        $min_term_days = intval($p['min_term_days']);
        $max_term_days = intval($p['max_term_days']);
        $guarantee_type = $p['guarantee_type'];
        $mortgage_type = $p['mortgage_type'];
        $interest_payment = $p['interest_payment'];
        if ($interest_payment == interestPaymentEnum::SINGLE_REPAYMENT) {
            $interest_rate_period = '';
        } else {
            $interest_rate_period = $p['interest_rate_period'];
        }
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_unit = trim($p['interest_rate_unit']);
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_unit = trim($p['operation_fee_unit']);
        $operation_min_value = round($p['operation_min_value'], 2);
        $grace_days = intval($p['grace_days']);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        if ($min_term_days > $max_term_days) {
            return new result(false, 'The minimum days cannot exceed the maximum days!');
        }

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }
        }

        $m_loan_product_size_rate = M('loan_product_size_rate');
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        if (!$row) {
            return new result(false, 'Invalid Size Rate!');
        }

        $m_loan_product_size_rate = M('loan_product_size_rate');

        $param = array(
            'product_id' => $product_id,
            'currency' => $currency,
            'uid' => array('neq', $size_rate_id),
            'interest_payment' => $interest_payment,
            'guarantee_type' => $guarantee_type,
            'mortgage_type' => $mortgage_type,
        );
        if ($interest_rate_period) $param['interest_rate_period'] = $interest_rate_period;
        $size_rate_arr = $m_loan_product_size_rate->select($param);
        foreach ($size_rate_arr as $size_rate) {
            if ((($loan_size_min > $size_rate['loan_size_min'] && $loan_size_min < $size_rate['loan_size_max']) || ($loan_size_min > $size_rate['loan_size_min'] && $loan_size_min < $size_rate['loan_size_max'])) && (($min_term_days > $size_rate['min_term_days'] && $min_term_days < $size_rate['max_term_days']) || ($max_term_days > $size_rate['min_term_days'] && $max_term_days < $size_rate['max_term_days']))) {
                return new result(false, 'Conditional Repetition!');
            }
        }

        $row->currency = $currency;
        $row->loan_size_min = $loan_size_min;
        $row->loan_size_max = $loan_size_max;
        $row->min_term_days = $min_term_days;
        $row->max_term_days = $max_term_days;
        $row->guarantee_type = $guarantee_type;
        $row->mortgage_type = $mortgage_type;
        $row->interest_payment = $interest_payment;
        $row->interest_rate_period = $interest_rate_period;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_unit = $interest_rate_unit;
        $row->interest_rate_type = 0;
        $row->interest_min_value = $interest_min_value;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_type = 0;
        $row->operation_fee_unit = $operation_fee_unit;
        $row->operation_min_value = $operation_min_value;
        $row->grace_days = $grace_days;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            if ($is_copy) {
                $data = array('uid' => $product_id);
            } else {
                $data = array();
            }
            return new result(true, 'Update Successful!', $data);
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    /**
     * 移除利率
     * @param $p
     * @return result
     */
    public function removeSizeRate($p)
    {
        $size_rate_id = intval($p['size_rate_id']);
        $m_loan_product_size_rate = M('loan_product_size_rate');
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        if (!$row) {
            return new result(false, 'Invalid Size Rate!');
        }

        $rt = $this->copyTemporaryProduct($row['product_id']);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        $rt = $row->delete();
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Remove Failure!');
        }

        $rt_1 = $m_loan_product_special_rate = M('loan_product_special_rate')->delete(array('size_rate_id' => $size_rate_id));
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Remove Failure!');
        }

        if ($is_copy) {
            $data = array('uid' => $product_id);
        } else {
            $data = array();
        }
        $conn->submitTransaction();
        return new result(true, 'Remove Success!', $data);
    }

    /**
     * 编辑产品条件
     * @param $p
     * @return result
     */
    public function updateProductCondition($p)
    {
        $product_id = intval($p['product_id']);
        unset($p['product_id']);

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }

        $m_loan_product_condition = M('loan_product_condition');
        $m_core_definition = M('core_definition');
        $conn = ormYo::Conn();
        $conn->startTransaction();

        try {
            $rt_1 = $m_loan_product_condition->delete(array('loan_product_id' => $product_id));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Save Failure!');
            }

            foreach ($p as $key => $val) {
                if ($val != 1) continue;
                $definition_arr = explode(',', $key);
                $definition_category = $definition_arr[0];
                $definition_id = $definition_arr[1];
                $definition = $m_core_definition->find(array('uid' => $definition_id, 'category' => $definition_category));
                if (!$definition) {
                    $conn->rollback();
                    return new result(false, 'Invalid Definition!');
                }
                $row = $m_loan_product_condition->newRow();
                $row->loan_product_id = $product_id;
                $row->definition_category = $definition_category;
                $row->definition_id = $definition_id;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failure!');
                }
            }

            $conn->submitTransaction();
            if ($is_copy) {
                $data = array('uid' => $product_id);
            } else {
                $data = array();
            }
            return new result(true, 'Save Successful!', $data);
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 更新描述
     * @param $p
     * @return result
     */
    public function updateDescription($p)
    {
        $product_id = intval($p['product_id']);
        $name = 'product_' . $p['name'];
        $val = $p['val'];

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }

        $m_loan_product = M('loan_product');
        $row = $m_loan_product->getRow(array('uid' => $product_id));
        if (!$row) {
            return new result(false, 'Invalid Product!');
        }
        $row->$name = $val;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            if ($is_copy) {
                $data = array('uid' => $product_id);
            } else {
                $data = array();
            }
            return new result(true, 'Update Successful!', $data);
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    /**
     * 改变产品状态
     * @param $uid
     * @param $state
     * @return result
     * 一个系列同时只能有一个产品state 为20
     */
    public function changeProductState($uid, $state)
    {
        $m_loan_product = M('loan_product');
        $row = $m_loan_product->getRow(array('uid' => $uid));
        if (!$row && $row['state'] == 40) {
            return new result(false, 'Invalid Product!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($state == 20) {
                $rows = $m_loan_product->getRows(array('product_key' => $row['product_key'], 'uid' => array('neq', $uid), 'state' => array('neq', 40)));
                foreach ($rows as $product) {
                    if ($product['state'] == 10) continue;
                    $product->state = 40;
                    $product->end_time = Now();
                    $product->update_time = Now();
                    $rt = $product->update();
                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(true, 'Update Failure!');
                    }
                }
            }
            $row->state = $state;
            if ($state == 20 && !$row->start_time) {
                $row->start_time = Now();
            }
            $row->update_time = Now();
            $rt = $row->update();
            if ($rt->STS) {
                $conn->submitTransaction();
                return new result(true, 'Update Successful!');
            } else {
                $conn->rollback();
                return new result(false, 'Update Failure!');
            }
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 获取特殊利率
     * @param $size_rate_id
     * @return array|null
     */
    public function getSpecialRateList($size_rate_id)
    {
        $m_loan_product_special_rate = M('loan_product_special_rate');
        $special_rate_list = $m_loan_product_special_rate->orderBy('client_grade asc,client_type asc')->select(array('size_rate_id' => $size_rate_id));
        return $special_rate_list;
    }

    /**
     * 增加特殊利率
     * @param $p
     * @return result
     */
    public function insertSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $client_grade = trim($p['client_grade']);
        $client_type = trim($p['client_type']);
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_type = 0;
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_type = 0;
        $operation_min_value = round($p['operation_min_value'], 2);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        $m_loan_product_special_rate = M('loan_product_special_rate');
        $chk_rate = $m_loan_product_special_rate->find(array('size_rate_id' => $size_rate_id, 'client_grade' => $client_grade, 'client_type' => $client_type));
        if ($chk_rate) {
            return new result(false, 'Conditions repeated!');
        }

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];

            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }

        }

        $row = $m_loan_product_special_rate->newRow();
        $row->size_rate_id = $size_rate_id;
        $row->client_grade = $client_grade ?: null;
        $row->client_type = $client_type ?: null;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_type = $interest_rate_type;
        $row->interest_min_value = $interest_min_value;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_type = $operation_fee_type;
        $row->operation_min_value = $operation_min_value;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        } else {
            return new result(false, 'Add Failure!' . $rt->STS);
        }
    }

    /**
     * 更新特殊利率
     * @param $p
     * @return result
     */
    public function updateSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $uid = intval($p['uid']);
        $size_rate_id = intval($p['size_rate_id']);
        $client_grade = trim($p['client_grade']);
        $client_type = trim($p['client_type']);
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_type = 0;
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_type = 0;
        $operation_min_value = round($p['operation_min_value'], 2);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        $m_loan_product_special_rate = M('loan_product_special_rate');
        $chk_rate = $m_loan_product_special_rate->find(array('uid' => array('neq', $uid), 'size_rate_id' => $size_rate_id, 'client_grade' => $client_grade, 'client_type' => $client_type));
        if ($chk_rate) {
            return new result(false, 'Conditions repeated!');
        }

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];

            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }

            $special_rate_map = $rt->DATA['special_rate_map'];
            if ($special_rate_map) {
                $uid = $special_rate_map[$uid];
            }
        }
        $row = $m_loan_product_special_rate->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $row->client_grade = $client_grade;
        $row->client_type = $client_type ?: null;
        $row->interest_rate = $interest_rate ?: null;
        $row->interest_rate_type = $interest_rate_type;
        $row->interest_min_value = $interest_min_value;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_type = $operation_fee_type;
        $row->operation_min_value = $operation_min_value;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit Successful!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        } else {
            return new result(false, 'Edit Failure!');
        }
    }

    /**
     * 移除特殊汇率
     * @param $p
     * @return result
     */
    public function removeSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $uid = intval($p['uid']);
        $m_loan_product_special_rate = M('loan_product_special_rate');
        $row = $m_loan_product_special_rate->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];

            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }

            $special_rate_map = $rt->DATA['special_rate_map'];
            if ($special_rate_map) {
                $uid = $special_rate_map[$uid];
            }
        }

        $row = $m_loan_product_special_rate->getRow(array('uid' => $uid));
        $rt = $row->delete();
        if (!$rt->STS) {
            return new result(false, 'Remove Failure!');
        } else {
            return new result(true, 'Remove Success!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        }
    }

}
