<?php

class counter_baseControl extends control
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;
    public $user_position;

    function __construct()
    {
        Language::read('auth');
        $this->checkLogin();
        $user = userBase::Current('counter_info');
        $user_info = $user->property->toArray();
        $user_info['user_position'] = my_json_decode($user_info['user_position']);
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];
        $this->user_position = $user_info['user_position'];

        $auth_arr = $user->getAuthList();
        $this->auth_list = $auth_arr['counter'];
    }

    private function checkLogin()
    {
        if (!getSessionVar("is_login") || !getSessionVar("counter_info")) {
            $ref_url = request_uri();
            $login_url = getUrl("login", "login", array("ref_url" => urlencode($ref_url)), false, ENTRY_COUNTER_SITE_URL);
            @header('Location:' . $login_url);
            die();
        } else {
            return operator::getUserInfo();
        }
    }

    protected function outputSubMenu($key)
    {
        $reset_menu = $this->getResetMenu();
        $sub_menu = $reset_menu[$key]['child'];
        Tpl::output('sub_menu', $sub_menu);
    }

    /**
     * 根据权限获取menu
     * @return array
     */
    protected function getResetMenu()
    {
        $index_menu = $this->getIndexMenu();
        foreach ($index_menu as $key => $menu) {
            foreach ($menu['child'] as $k => $child) {
                $argc = explode(',', $child['args']);//分割args字符串
                $auth = $argc[1] . '_' . $argc[2];//取control和function连接
                if (!in_array($auth, $this->auth_list)) { //判断是否存在权限
                    unset($index_menu[$key]['child'][$k]);//没有权限就删除
                }
            }
            if (empty($index_menu[$key]['child'])) {//如果整个child都为空，及二级菜单都为空，不展示一级菜单
                unset($index_menu[$key]);
            }
            if ($key == 'cash_in_vault' && !in_array(userPositionEnum::CHIEF_TELLER, $this->user_position)) {
                unset($index_menu[$key]);
            }
        }
        return $index_menu;

    }

    /**
     * 定义menu
     * @return array
     */
    private function getIndexMenu()
    {
        $indexMenu = array(
            'member' => array(
                "title" => 'Member',
                'child' => array(
                    array('args' => 'microbank/counter,member,register', 'title' => 'Register'),
                    array('args' => 'microbank/counter,member,documentCollection', 'title' => 'Document collection'),
                    array('args' => 'microbank/counter,member,fingerprintCollection', 'title' => 'Fingerprint Collection'),
                    array('args' => 'microbank/counter,member,loan', 'title' => 'Loan'),
                    array('args' => 'microbank/counter,member,deposit', 'title' => 'Deposit'),
                    array('args' => 'microbank/counter,member,withdrawal', 'title' => 'Withdrawal'),
                    array('args' => 'microbank/counter,member,profile', 'title' => 'Profile'),
                )
            ),
            'company' => array(
                "title" => 'Company',
                'child' => array(
                    array('args' => 'microbank/counter,company,index', 'title' => 'Index'),
                )
            ),
            'service' => array(
                "title" => 'Service',
                'child' => array(
                    array('args' => 'microbank/counter,service,requestLoan', 'title' => 'Request Loan'),
                    array('args' => 'microbank/counter,service,currencyExchange', 'title' => 'Currency Exchange'),
                )
            ),
            'mortgage' => array(
                "title" => 'Mortgage',
                'child' => array(
                    array('args' => 'microbank/counter,mortgage,index', 'title' => 'Index'),
                )
            ),
            'cash_on_hand' => array(
                "title" => 'Cash On Hand',
                'child' => array(
                    array('args' => 'microbank/counter,cash,cashOnHand', 'title' => 'Cash On Hand'),
                )
            ),
            'cash_in_vault' => array(
                "title" => 'Cash In Vault',
                'child' => array(
                    array('args' => 'microbank/counter,cash,cashInVault', 'title' => 'Cash In Vault'),
                )
            ),
        );
        return $indexMenu;
    }

}