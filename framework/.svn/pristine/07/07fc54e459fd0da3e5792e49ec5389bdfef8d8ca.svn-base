<?php

class financialControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "");
        Tpl::setDir("financial");
        Language::read('financial');
    }

    /**
     * 收款账号
     */
    public function bankAccountOp()
    {
        $m_bank_account = M('site_bank');
        $account_list = $m_bank_account->orderBy('bank_code ASC')->select(array('uid' => array('neq', 0)));
        Tpl::output('account_list', $account_list);
        Tpl::showpage('receive.account.list');
    }

    /**
     * 添加收款人账号
     */
    public function addReceiveAccountOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_bank_account = M('site_bank');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_bank_account->addReceiveAccount($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('financial', 'bankAccount', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('financial', 'addReceiveAccount', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_common_bank_lists = M('common_bank_lists');
            $bank_list = $m_common_bank_lists->select(array('uid' => array('neq', 0)));
            Tpl::output("bank_list", $bank_list);

            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->select(array('status' => 1));
            Tpl::output("branch_list", $branch_list);

            $currency_list = currency::getKindList();
            Tpl::output("currency_list", $currency_list);
            Tpl::showPage("receive.account.add");
        }
    }

    public function editReceiveAccountOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_bank_account = M('site_bank');
        if ($p['form_submit'] == 'ok') {
            $rt = $m_bank_account->editReceiveAccount($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('financial', 'bankAccount', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('financial', 'editReceiveAccount', array('uid' => intval($p['uid'])), false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_common_bank_lists = M('common_bank_lists');
            $bank_list = $m_common_bank_lists->select(array('uid' => array('neq', 0)));
            Tpl::output("bank_list", $bank_list);

            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->select(array('status' => 1));
            Tpl::output("branch_list", $branch_list);

            $currency_list = currency::getKindList();
            Tpl::output("currency_list", $currency_list);

            $uid = intval($p['uid']);
            $account_info = $m_bank_account->find(array('uid' => $uid));
            if (!$account_info) {
                showMessage('Invalid Id!');
            }
            Tpl::output('account_info', $account_info);

            $m_site_bank_branch = M('site_bank_branch');
            $branch_id = $m_site_bank_branch->select(array('bank_id' => $uid));
            Tpl::output('branch_id', array_column($branch_id, 'branch_id'));

            Tpl::showPage("receive.account.edit");
        }
    }

    public function deleteReceiveAccountOp()
    {
        $uid = intval($_GET['uid']);
        $m_bank_account = M('site_bank');
        $row = $m_bank_account->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $rt = $row->delete();
        if ($rt->STS) {
            showMessage('Remove successful!');
        } else {
            showMessage('Remove failed!');
        }
    }

    /**
     * 货币汇率表
     */
    public function exchangeRateOp()
    {
        $m_common_exchange_rate = M('common_exchange_rate');
        $exchange_rate_list = $m_common_exchange_rate->select(array('uid' => array('neq', 0)));
        Tpl::output('exchange_rate_list', $exchange_rate_list);
        Tpl::showpage('exchange_rate.list');
    }


    public function addNewExchangeRateOp($p)
    {

        $m_common_exchange_rate = new common_exchange_rateModel();
        $row = $m_common_exchange_rate->newRow();
        $first_currency = $p['first_currency'];
        $second_currency = $p['second_currency'];
        $buy_rate = round($p['buy_rate'], 2);
        $buy_rate_unit = round($p['buy_rate_unit'], 2);
        $sell_rate = round($p['sell_rate'], 2);
        $sell_rate_unit = round($p['sell_rate_unit'], 2);
        if (($buy_rate / $buy_rate_unit) > ($sell_rate_unit / $sell_rate)) {
            showMessage('The sell price is greater than the buy price!');
        }
        $row->first_currency = $first_currency;
        $row->second_currency = $second_currency;
        $row->buy_rate = $buy_rate;
        $row->buy_rate_unit = $buy_rate_unit;
        $row->sell_rate = $sell_rate;
        $row->sell_rate_unit = $sell_rate_unit;
        $row->update_id = $this->user_id;
        $row->update_name = $this->user_name;
        $row->update_time = Now();
        $insert = $row->insert();
        return $insert;
    }

    /**
     * 设置汇率
     */
    public function setExchangeRateOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_exchange_rate = M('common_exchange_rate');
        if ($p['form_submit'] == 'ok') {
            $first_currency = $p['first_currency'];
            $second_currency = $p['second_currency'];
            $row = $m_common_exchange_rate->getRow(array('first_currency' => $first_currency, 'second_currency' => $second_currency));
            $currency_list = (new currencyEnum)->Dictionary();
            unset($currency_list[$first_currency]);
            unset($currency_list[$second_currency]);
            $other_currency = array_pop($currency_list);
            if ($row) {
                $buy_rate = round($p['buy_rate'], 2);
                $buy_rate_unit = round($p['buy_rate_unit'], 2);
                $sell_rate = round($p['sell_rate'], 2);
                $sell_rate_unit = round($p['sell_rate_unit'], 2);
                if (($buy_rate / $buy_rate_unit) > ($sell_rate_unit / $sell_rate)) {
                    showMessage('The sell price is greater than the buy price!');
                }

                $exchange_0 = $buy_rate / $buy_rate_unit;
                $exchange_1 = $m_common_exchange_rate->getRateBetween($second_currency, $other_currency);
                $exchange_2 = $m_common_exchange_rate->getRateBetween($other_currency, $first_currency);
                $exchange = $exchange_0 * $exchange_1 * $exchange_2;
                if ($exchange > 1) {
                    showMessage('If buying in a third currency, the principal increases!');
                }

                $row->buy_rate = $buy_rate;
                $row->buy_rate_unit = $buy_rate_unit;
                $row->sell_rate = $sell_rate;
                $row->sell_rate_unit = $sell_rate_unit;
            } else {
                $row = $m_common_exchange_rate->getRow(array('second_currency' => $first_currency, 'first_currency' => $second_currency));

                if (!$row) {
                    $re = $this->addNewExchangeRateOp($p);
                    if ($re->STS) {
                        showMessage('Setting successful!', getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL));
                    } else {
                        showMessage('Setting failed!');
                    }
                }

                $buy_rate = round($p['buy_rate'], 2);
                $buy_rate_unit = round($p['buy_rate_unit'], 2);
                $sell_rate = round($p['sell_rate'], 2);
                $sell_rate_unit = round($p['sell_rate_unit'], 2);

                if (($sell_rate / $sell_rate_unit) > ($buy_rate_unit / $buy_rate)) {
                    showMessage('The sell price is greater than the buy price!');
                }

                $exchange_0 = $sell_rate / $sell_rate_unit;
                $exchange_1 = $m_common_exchange_rate->getRateBetween($first_currency, $other_currency);
                $exchange_2 = $m_common_exchange_rate->getRateBetween($other_currency, $second_currency);
                $exchange = $exchange_0 * $exchange_1 * $exchange_2;
                if ($exchange > 1) {
                    showMessage('If buying in a third currency, the principal increases!');
                }

                $row->buy_rate = $sell_rate;
                $row->buy_rate_unit = $sell_rate_unit;
                $row->sell_rate = $buy_rate;
                $row->sell_rate_unit = $buy_rate_unit;
            }
            $row->update_id = $this->user_id;
            $row->update_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if ($rt->STS) {
                showMessage('Setting successful!', getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Setting failed!');
            }
        } else {
            $uid = intval($p['uid']);
            if ($uid > 0) {
                $currency = $m_common_exchange_rate->find(array('uid' => $uid));
                Tpl::output('currency', $currency);
            }
            $currency_list = (new currencyEnum)->Dictionary();
            Tpl::output('currency_list', $currency_list);
            Tpl::showpage('exchange_rate.setting');
        }
    }

    /**
     * 获取汇率
     * @param $p
     * @return result
     */
    public function getRateOp($p)
    {
        $first_currency = $p['first_currency'];
        $second_currency = $p['second_currency'];

        $m_common_exchange_rate = M('common_exchange_rate');
        $currency_rate = $m_common_exchange_rate->find(array('first_currency' => $first_currency, 'second_currency' => $second_currency));
        if ($currency_rate) {
            $data = array(
                'buy_rate' => rtrim(rtrim($currency_rate['buy_rate'], '0'), '.'),
                'buy_rate_unit' => rtrim(rtrim($currency_rate['buy_rate_unit'], '0'), '.'),
                'sell_rate' => rtrim(rtrim($currency_rate['sell_rate'], '0'), '.'),
                'sell_rate_unit' => rtrim(rtrim($currency_rate['sell_rate_unit'], '0'), '.')
            );
            return new result(true, '', $data);
        }
        $currency_rate = $m_common_exchange_rate->find(array('first_currency' => $second_currency, 'second_currency' => $first_currency));
        if ($currency_rate) {
            $data = array(
                'buy_rate' => rtrim(rtrim($currency_rate['sell_rate'], '0'), '.'),
                'buy_rate_unit' => rtrim(rtrim($currency_rate['sell_rate_unit'], '0'), '.'),
                'sell_rate' => rtrim(rtrim($currency_rate['buy_rate'], '0'), '.'),
                'sell_rate_unit' => rtrim(rtrim($currency_rate['buy_rate_unit'], '0'), '.')
            );
            return new result(true, '', $data);
        } else {
            return new result(true, '', null);
        }
    }
}
