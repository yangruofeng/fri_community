<?php

class passbookClass {
    private $passbook_info;
    private $accounts;

    public function __construct($passbookInfo)
    {
        $this->passbook_info = $passbookInfo;
    }

    public function getPassbookInfo()
    {
        return $this->passbook_info;
    }

    public function getBookId()
    {
        return $this->passbook_info['uid'];
    }

    public function getName() {
        if ($this->passbook_info->book_name)
            return $this->passbook_info->book_name;
        else
            return $this->passbook_info->uid;
    }

    public static function createPassbookAccounts($book_id,$currency=null)
    {
        $m = new passbook_accountModel();
        $create_time = Now();

        $sql = "insert into passbook_account(book_id,currency,create_time,operator_id,operator_name) values ";
        if( !$currency ){

            $currency_arr = (new currencyEnum())->toArray();
            $data = array();
            foreach( $currency_arr as $currency ){
                $str = "('$book_id','$currency','$create_time','0','System')";
                $data[] = $str;
            }
            $sql_str = implode(',',$data);
            $sql .= trim($sql_str,',');
        }else{
            $sql .= "('$book_id','$currency','$create_time','0','System')";
        }

        $insert = $m->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Create passbook account fail - ' . $insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }

    public static function getOrCreatePassbookByObjGuid($guid, $createOpts = null) {
        $passbook_model = new passbookModel();
        $passbook_info = $passbook_model->getRow(array(
            'obj_guid' => $guid
        ));

        if (!$passbook_info) {
            if (!$createOpts) throw new Exception("Cannot found passbook");
            $passbook_info = $passbook_model->newRow();
            $passbook_info->obj_guid = $guid;
            $passbook_info->state = passbookStateEnum::ACTIVE;
            $passbook_info->book_name = $createOpts['book_name'];
            $passbook_info->book_type = $createOpts['book_type'];
            $passbook_info->obj_type = $createOpts['obj_type'];
            $passbook_info->create_time = date("Y-m-d H:i:s");
            $passbook_info->create_org = 0;
            $passbook_info->operator_id = 0;
            $passbook_info->operator_name = 'System';
            $passbook_info->update_time = date("Y-m-d H:i:s");
            $ret = $passbook_info->insert();
            if (!$ret->STS)
                throw new Exception("Create passbook failed - " . $ret->MSG);
            $rt = self::createPassbookAccounts($passbook_info->uid,null);
            if( !$rt->STS ){
                throw new Exception($rt->MSG);
            }

        }

        return new passbookClass($passbook_info);
    }

    /**
     * 获得member的储蓄账户的passbook
     * @param $guid
     * @return passbookClass
     */
    public static function getSavingsPassbookOfMemberGUID($guid)
    {
        return self::getOrCreatePassbookByObjGuid($guid
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
    }

    /**
     * 获得loanAccount储蓄账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getSavingsPassbookOfLoanAccount($loanAccount) {
        return self::getOrCreatePassbookByObjGuid($loanAccount->getSavingsGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member'
            )
        );
    }

    /**
     * 获得loanAccount短期应收贷款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getShortLoanPassbookOfLoanAccount($loanAccount) {
        return self::getOrCreatePassbookByObjGuid($loanAccount->getShortLoanGUID()
            , array(
                'book_type' => passbookTypeEnum::ASSET,        // 应收贷款账户是资产类
                'obj_type' => 'client_short_loan'
            )
        );
    }

    /**
     * 获得loanAccount长期应收贷款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getLongLoanPassbookOfLoanAccount($loanAccount) {
        return self::getOrCreatePassbookByObjGuid($loanAccount->getLongLoanGUID()
            , array(
                'book_type' => passbookTypeEnum::ASSET,        // 应收贷款账户是资产类
                'obj_type' => 'client_long_loan'
            )
        );
    }

    /**
     * 获得loanAccount短期存款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getShortDepositPassbookOfLoanAccount($loanAccount) {
        return self::getOrCreatePassbookByObjGuid($loanAccount->getShortDepositGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,        // 储蓄账户是负债类
                'obj_type' => 'client_short_deposit'
            )
        );
    }

    /**
     * 获得loanAccount长期存款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getLongDepositPassbookOfLoanAccount($loanAccount) {
        return self::getOrCreatePassbookByObjGuid($loanAccount->getLongDepositGUID()
            , array(
                'book_type' => passbookTypeEnum::DEBT,        // 储蓄账户是负债类
                'obj_type' => 'client_long_deposit'
            )
        );
    }

    /**
     * 获取收入账户的passbook
     * @param $incomingType
     * @param $businessType
     * @return passbookClass
     */
    public static function getIncomingPassbook($incomingType, $businessType) {
        $account_info = gl_accountClass::getIncomingAccount($incomingType, $businessType);

        // 获取或创建收入类型下业务类型账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_type' => passbookTypeEnum::PROFIT,        // 收入是损益类
                'obj_type' => 'gl_account'
            )
        );
    }

    /**
     * 获取系统账户的passbook
     * @param $systemAccountCode
     * @return passbookClass
     */
    public static function getSystemPassbook($systemAccountCode) {
        $account_info = gl_accountClass::getSystemAccount($systemAccountCode);

        // 获取或创建账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_type' => $account_info->category,        // 类型根据account的category
                'obj_type' => 'gl_account'
            )
        );
    }

    /**
     * 获得分行passbook
     * @param $branchId
     * @return passbookClass
     */
    public static function getBranchPassbook($branchId) {
        return self::getOrCreatePassbookByObjGuid(branchClass::getGUID($branchId)
            , array(
                'book_type' => passbookTypeEnum::ASSET,          // 分行账户是资产类
                'obj_type' => 'branch'
            )
        );
    }

    /**
     * 获得用户passbook
     * @param $userId
     * @return passbookClass
     */
    public static function getUserPassbook($userId) {
        return self::getOrCreatePassbookByObjGuid(userClass::getGUID($userId)
            , array(
                'book_type' => passbookTypeEnum::ASSET,          // 员工账户是资产类
                'obj_type' => 'user'
            )
        );
    }

    /**
     * 获得银行账户的passbook
     * @param $bankAccountId
     * @return passbookClass
     */
    public static function getBankAccountPassbook($bankAccountId) {
        return self::getOrCreatePassbookByObjGuid(bank_accountClass::getGUID($bankAccountId)
            , array(
                'book_type' => passbookTypeEnum::ASSET,          // 银行账户是资产类
                'obj_type' => 'bank'
            )
        );
    }

    public static function getPartnerPassbook($partnerId) {
        return self::getOrCreatePassbookByObjGuid(partnerClass::getGUID($partnerId)
            , array(
                'book_type' => passbookTypeEnum::DEBT,          // partner结算户是负债类
                'obj_type' => 'bank'
            )
        );
    }


    /**
     * 获取passbook下各币种余额
     * @return array
     */
    public function getAccountBalance()
    {
        $ccy_balance = array();
        // 统一返回，避免没有账户的错误
        foreach( (new currencyEnum())->toArray() as $currency ){
            $ccy_balance[$currency] = 0.00;
        }
        $m_passbook_account = new passbook_accountModel();
        $accounts = $m_passbook_account->getRows(array(
            'book_id' => $this->passbook_info->uid
        ));
        foreach( $accounts as $item ){
            $this->accounts[$item['currency']] = $item;
            $ccy_balance[$item['currency']] = $item['balance'];
        }
        return $ccy_balance;
    }

    public function getAccount($currency) {
        if (!$this->accounts[$currency]) {
            $account_model = new passbook_accountModel();
            $this->accounts[$currency] = $account_model->getRow(array(
                'book_id' => $this->passbook_info->uid,
                'currency' => $currency
            ));
        }

        return $this->accounts[$currency];
    }

    public function getBalanceDelta($credit, $debit)
    {
        switch ($this->passbook_info->book_type) {
            case passbookTypeEnum::ASSET:
            case passbookTypeEnum::COST:
                return $debit - $credit;
            case passbookTypeEnum::DEBT:
            case passbookTypeEnum::EQUITY:
            case passbookTypeEnum::PROFIT:
                return $credit - $debit;
            case passbookTypeEnum::COMMON:
                return $credit - $debit;
            default:
                throw new Exception('Unknown passbook type - ' . $this->passbook_info->book_type);
        }
    }
}