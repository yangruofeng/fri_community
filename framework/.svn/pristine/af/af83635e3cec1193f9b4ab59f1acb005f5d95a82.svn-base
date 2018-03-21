<?php

class passbookWorkerClass {
    public static function disburseLoan($schemeId) {
        $scheme_model = new loan_disbursement_schemeModel();
        $scheme_info = $scheme_model->getRow($schemeId);
        if (!$scheme_info) {
            return new result(false, "Disbursement scheme $schemeId not found", null, errorCodesEnum::UNEXPECTED_DATA);
        }

        // 分多个交易执行
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 第一个是转到储蓄账户
        $ret = (new loanDisburseTradingClass($scheme_info))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 第二个是从储蓄账户扣除相关费用
        $ret = (new loanDeductTradingClass($scheme_info))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // TODO: 自动提现到partner账户

        // 都成功完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberDepositByCash($memberId, $cashierUserId, $amount, $currency) {
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberDepositByCashTradingClass($memberId, $cashierUserId, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberDepositByPartner($memberId, $accountHandlerId, $amount, $currency) {
        $handler_model = new member_account_handlerModel();
        $handler_info = $handler_model->getRow($accountHandlerId);

        switch ($handler_info->handler_type) {
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                $partner_id = partnerClass::getAsiaweiluyPartnerID();
                break;
            default:
                return new result(false, 'Partner is not supported now', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberDepositByPartnerTradingClass($memberId, $partner_id, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberDepositByBank($memberId, $bankAccountId, $amount, $currency) {
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberDepositByBankTradingClass($memberId, $bankAccountId, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberWithdrawToCash($memberId, $cashierUserId, $amount, $currency) {
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberWithdrawByCashTradingClass($memberId, $cashierUserId, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberWithdrawToPartner($memberId, $accountHandlerId, $amount, $currency) {
        $handler_model = new member_account_handlerModel();
        $handler_info = $handler_model->getRow($accountHandlerId);

        switch ($handler_info->handler_type) {
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                $partner_id = partnerClass::getAsiaweiluyPartnerID();
                break;
            default:
                return new result(false, 'Partner is not supported now', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberWithdrawByPartnerTradingClass($memberId, $partner_id, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberTransferToMember($fromMemberId, $toMemberId, $amount, $currency) {
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 创建和执行交易
        $ret = (new memberToMemberTradingClass($fromMemberId, $toMemberId, $amount, $currency))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

    public static function memberLoanRepaymentOfSchema($schemeId,$penalty,$paid_currency)
    {
        $scheme_model = new loan_installment_schemeModel();
        $scheme_info = $scheme_model->getRow($schemeId);
        if (!$scheme_info) {
            return new result(false, "Installment scheme $schemeId not found", null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        $ret = (new loanRepaymentTradingClass($scheme_info,$paid_currency,$penalty))->execute();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }

        // 都成功完成提交事务
        $conn->submitTransaction();
        return $ret;
    }

}