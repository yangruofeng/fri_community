<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/3/1
 * Time: 12:01
 */

abstract class tradingClass {
    /**
     * 获取交易的主要信息
     * @return array (
     *  category
     *  trading_type
     *  subject
     *  remark
     *  is_outstanding
     * )
     */
    protected abstract function getTradingInfo();

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected abstract function getTradingDetail();

    /**
     * 获得交易明细记录项
     * @param $passbook passbookClass
     * @param $amount
     * @param $currency
     * @param $direction
     * @return array
     */
    protected function createTradingDetailItem($passbook, $amount, $currency, $direction) {
        return array(
            'passbook' => $passbook,
            'currency' => $currency,
            'credit' => $direction == accountingDirectionEnum::CREDIT ? $amount : 0,
            'debit' => $direction == accountingDirectionEnum::DEBIT ? $amount : 0
        );
    }

    /**
     * 检查交易明细记录合法性，要求各种货币借方金额与贷方金额合计相等
     * @param $tradingDetail
     * @return result
     */
    private function checkTradingDetail($tradingDetail) {
        // 统计各种货币借方与贷方的合计金额
        $sum_credit = array();    // 贷方金额合计
        $sum_debit = array();     // 借方金额合计
        $currencies = array();    // 交易涉及到的货币列表
        foreach ($tradingDetail as $item) {
            $currency = $item['currency'];
            if (!in_array($currency, $currencies)) {
                $currencies[]=$currency;
                $sum_credit[$currency] = 0;
                $sum_debit[$currency] = 0;
            }
            $sum_credit[$currency] += $item['credit'];
            $sum_debit[$currency] += $item['debit'];
        }
        // 检查各种货币借贷双方金额是否相等
        foreach ($currencies as $currency) {
            if ($sum_debit[$currency] != $sum_credit[$currency])
                return new result(false, 'Invalid trading detail', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true);
    }

    /**
     * 保存交易主要信息
     * @param $tradingInfo
     * @return result
     */
    private function insertTradingInfo($tradingInfo) {
        $trading_model = new passbook_tradingModel();
        $trading_row = $trading_model->newRow();
        $trading_row->category = $tradingInfo['category'];
        $trading_row->trading_type = $tradingInfo['trading_type'];
        $trading_row->subject = $tradingInfo['subject'];
        $trading_row->remark = $tradingInfo['remark'];
        $trading_row->is_outstanding = $tradingInfo['is_outstanding'];
        $trading_row->create_time = date("Y-m-d H:i:s");
        $trading_row->update_time = date("Y-m-d H:i:s");
        $trading_row->state = passbookTradingStateEnum::CREATE;
        $ret = $trading_row->insert();
        if (!$ret->STS) {
            return new result(false, 'Insert trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else {
            return new result(true, null, $trading_row);
        }
    }

    /**
     * 保存交易明细信息
     * @param $tradingDetail
     * @param $tradingRow
     * @return result
     */
    private function insertTradingDetail($tradingDetail, $tradingRow) {
        $flow_model = new passbook_account_flowModel();
        $flows = array();
        $accounts = array();
        foreach ($tradingDetail as $item) {
            $passbook = $item['passbook'];
            $account = $passbook->getAccount($item['currency']);

            $row = $flow_model->newRow();
            $row->account_id = $account->uid;
            $row->begin_balance = $account->balance;
            $row->credit = $item['credit'];
            $row->debit = $item['debit'];
            $row->end_balance = $account->balance + $passbook->getBalanceDelta($item['credit'], $item['debit']);
            $row->trade_id = $tradingRow->uid;
            $row->create_time = date("Y-m-d H:i:s");
            $row->update_time = date("Y-m-d H:i:s");
            $row->state = passbookAccountFlowStateEnum::CREATE;
            $ret = $row->insert();
            if (!$ret->STS) {
                return new result(false, 'Insert flow failed - '. $ret->MSG, null, errorCodesEnum::DB_ERROR);
            }

            $account->balance = $row->end_balance;
            if ($account->balance < 0) {
                return new result(false, 'Insufficient Balance - ' . $passbook->getName() . "/" . $item['currency'], null, errorCodesEnum::BALANCE_NOT_ENOUGH);
            }

            $flows[]=$row;
            $accounts[$account->uid] = $account;
        }

        return new result(true, null, array(
            'flows' => $flows,
            'accounts' => $accounts));
    }

    /**
     * 完成交易，一齐更改记录状态、更新相关账户余额
     * @param $tradingRow
     * @param $tradingFlows
     * @param $accounts
     * @return result
     */
    private function completeTrading($tradingRow, $tradingFlows, $accounts)
    {
        $tables = array();
        $sets = array();
        $filters = array();

        // trading 表
        $tables[] = "passbook_trading t";
        $sets[] = "t.state=" . passbookTradingStateEnum::DONE;
        $filters[] = "t.uid=" . $tradingRow->uid . " AND t.state=" . passbookTradingStateEnum::CREATE;

        // flow 表
        foreach ($tradingFlows as $i => $flow) {
            $tables[] = "passbook_account_flow f$i";
            if ($tradingRow->is_outstanding) {
                $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::OUTSTANDING;
            } else {
                $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::DONE;
            }
            $filters[] = "f$i.uid=" . $flow->uid . " AND f$i.state=" . passbookAccountFlowStateEnum::CREATE;
        }

        // account 表
        foreach ($accounts as $i => $account) {
            $tables[] = "passbook_account a$i";
            $sets[] = "a$i.balance=" . $account->balance;
            if ($tradingRow->is_outstanding) {
                $sets[] = "a$i.outstanding=a$i.outstanding+" . ($account->balance-$account->getOldRow()->balance);
            }
            $filters[] = "a$i.uid=" . $account->uid . " AND a$i.balance=" . $account->getOldRow()->balance;
        }

        $sql = "update " . join(",", $tables) . " set " . join(",", $sets) . " where " . join(" AND ", $filters);
        $ret = ormYo::Conn()->execute($sql);
        if (!$ret->STS) {
            return new result(false, 'Complete trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else if ($ret->AFFECTED_ROWS == 0) {
            return new result(false, 'Complete trading failed - update conflicted', null, errorCodesEnum::UNKNOWN_ERROR);
        } else {
            return new result(true);
        }
    }

    /**
     * 执行交易
     * 交易的内容在$this之中，由具体的交易类构建成trading标准的数据
     * @return result
     */
    public function execute() {
        try {
            $trading_detail = $this->getTradingDetail();
            // 检查trading detail
            $ret = $this->checkTradingDetail($trading_detail);
            if (!$ret->STS) return $ret;

            // 获取交易信息
            $trading_info = $this->getTradingInfo();
            // 建立trading数据
            $ret = $this->insertTradingInfo($trading_info);
            if (!$ret->STS) return $ret;
            $trading_row = $ret->DATA;

            // 建立flow数据，并构建完成交易更新所需参数
            $ret = $this->insertTradingDetail($trading_detail, $trading_row);
            if (!$ret->STS) return $ret;
            $flows = $ret->DATA['flows'];
            $accounts = $ret->DATA['accounts'];

            // 准备都没有问题，一起更新状态、余额完成交易
            $ret = $this->completeTrading($trading_row, $flows, $accounts);
            if (!$ret->STS) return $ret;

            return new result(true, null, $trading_row->uid);
        } catch(Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }
}
