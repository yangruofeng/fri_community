<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2016/8/30
 * Time: 17:09
 */
class authEnum extends Enum
{
    /* back office权限设置开始*/
    const AUTH_HOME_MONITOR = "home_monitor";

    const AUTH_USER_BRANCH = "user_branch";
    const AUTH_USER_ROLE = "user_role";
    const AUTH_USER_USER = "user_user";
    const AUTH_USER_LOG = "user_log";
    const AUTH_USER_POINT_EVENT = "user_pointEvent";
    const AUTH_USER_POINT_PERIOD = "user_pointPeriod";
    const AUTH_USER_DEPARTMENT_POINT = "user_departmentPoint";

    const AUTH_CLIENT_CLIENT = "client_client";
    const AUTH_CLIENT_CERIFICATION = "client_cerification";
    const AUTH_CLIENT_BLACK_LIST = "client_blackList";
    const AUTH_CLIENT_GRADE = "client_grade";

    const AUTH_PARTNER_BANK = "partner_bank";
    const AUTH_PARTNER_DEALER = "partner_dealer";

    const AUTH_LOAN_PRODUCT = "loan_product";
    const AUTH_LOAN_CREDIT = "loan_credit";
    const AUTH_LOAN_APPROVAL = "loan_approval";
    const AUTH_LOAN_APPLY = "loan_apply";
    const AUTH_LOAN_REQUEST_TO_PREPAYMENT = "loan_requestToPrepayment";
    const AUTH_LOAN_REQUEST_TO_REPAYMENT = "loan_requestToRepayment";
    const AUTH_LOAN_CONTRACT = "loan_contract";
    const AUTH_LOAN_WRITE_OFF = "loan_writeOff";
    const AUTH_LOAN_OVERDUE = "loan_overdue";
    const AUTH_LOAN_DEDUCTING_PENALTIES = "loan_deductingPenalties";

    const AUTH_INSURANCE_PRODUCT = "insurance_product";
    const AUTH_INSURANCE_CONTRACT = "insurance_contract";

    const AUTH_SETTING_COMPANY_INFO = "setting_companyInfo";
    const AUTH_SETTING_CREDIT_LEVEL = 'setting_creditLevel';
    const AUTH_SETTING_CREDIT_PROCESS = 'setting_creditProcess';
    const AUTH_SETTING_GLOBAL = "setting_global";
    const AUTH_REGION_LIST = "region_list";
    const AUTH_SETTING_SHORT_CODE = "setting_shortCode";
    const AUTH_SETTING_CODING_RULE = "setting_codingRule";
    const AUTH_SETTING_RESET_SYSTEM = "setting_resetSystem";

    const AUTH_FINANCIAL_BANK_ACCOUNT = "financial_bankAccount";
    const AUTH_FINANCIAL_EXCHANGE_RATE = "financial_exchangeRate";

    const AUTH_REPORT_OVERVIEW = "report_overview";
    const AUTH_REPORT_CLIENT_LIST = "report_clientList";
    const AUTH_REPORT_CONTRACT_LIST = "report_contractList";
    const AUTH_REPORT_CREDIT_LIST = "report_creditList";
    const AUTH_REPORT_TODAY_REPORT = "report_todayReport";
    const AUTH_REPORT_LOAN_LIST = "report_loanList";
    const AUTH_REPORT_REPAYMENT_LIST = "report_repaymentList";
    const AUTH_REPORT_ASSET_LIABILITY = "report_assetLiability";
    const AUTH_REPORT_PROFIT_REPORT = "report_profitReport";

    const AUTH_EDITOR_HELP = "editor_help";

    const AUTH_TOOLS_CALCULATOR = "tools_calculator";
    const AUTH_TOOLS_SMS = "tools_sms";

    const AUTH_POINT_EVENT = "point_event";
    const AUTH_POINT_POINT_RECORD = "point_pointRecord";
    const AUTH_POINT_USER_POINT = "point_userPoint";

    const AUTH_DEV_APP_VERSION = "dev_appVersion";
    const AUTH_DEV_FUNCTION_SWITCH = "dev_functionSwitch";
    const AUTH_DEV_RESET_PASSWORD = "dev_resetPassword";
    /* back office权限设置结束*/

    /*counter权限设置开始*/
    const AUTH_MEMBER_REGISTER = "member_register";
    const AUTH_MEMBER_DOCUMENT_COLLECTION = "member_documentCollection";
    const AUTH_MEMBER_FINGERPRINT_COLLECTION = "member_fingerprintCollection";
    const AUTH_MEMBER_LOAN = "member_loan";
    const AUTH_MEMBER_DEPOSIT = "member_deposit";
    const AUTH_MEMBER_WITHDRAWAL = "member_withdrawal";
    const AUTH_MEMBER_PROFILE = "member_profile";

    const AUTH_COMPANY_INDEX = "company_index";

    const AUTH_SERVICE_REQUEST_LOAN = "service_requestLoan";
    const AUTH_SERVICE_CURRENCY_EXCHANGE = "service_currencyExchange";

    const AUTH_MORTGAGE_INDEX = "mortgage_index";

    const AUTH_CASH_CASH_ON_HAND = "cash_cashOnHand";

    const AUTH_CASH_CASH_IN_VAULT = "cash_cashInVault";
    /*counter权限设置结束*/
}

interface IauthGroup
{
    function getGroupKey();

    function getGroupName();

    function getAuthList();
}

class authGroup_home implements IauthGroup
{
    function getGroupKey()
    {
        return "home";//menu的key值
    }

    function getGroupName()
    {
        return "home";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_HOME_MONITOR
        );
    }
}

class authGroup_user implements IauthGroup
{
    function getGroupKey()
    {
        return "user";//menu的key值
    }

    function getGroupName()
    {
        return "user";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_USER_BRANCH,
//            authEnum::AUTH_USER_DEPARTMENT,
            authEnum::AUTH_USER_ROLE,
            authEnum::AUTH_USER_USER,
            authEnum::AUTH_USER_LOG,
            authEnum::AUTH_USER_POINT_EVENT,
            authEnum::AUTH_USER_POINT_PERIOD,
            authEnum::AUTH_USER_DEPARTMENT_POINT,
        );
    }
}

class authGroup_client implements IauthGroup
{
    function getGroupKey()
    {
        return "client";//menu的key值
    }

    function getGroupName()
    {
        return "client";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CLIENT_CLIENT,
            authEnum::AUTH_CLIENT_CERIFICATION,
            authEnum::AUTH_CLIENT_BLACK_LIST,
            authEnum::AUTH_CLIENT_GRADE,
        );
    }
}

class authGroup_partner implements IauthGroup
{
    function getGroupKey()
    {
        return "partner";//menu的key值
    }

    function getGroupName()
    {
        return "partner";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_PARTNER_BANK,
            authEnum::AUTH_PARTNER_DEALER,
        );
    }
}

class authGroup_loan implements IauthGroup
{
    function getGroupKey()
    {
        return "loan";//menu的key值
    }

    function getGroupName()
    {
        return "loan";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_LOAN_PRODUCT,
            authEnum::AUTH_LOAN_CREDIT,
            authEnum::AUTH_LOAN_APPROVAL,
            authEnum::AUTH_LOAN_APPLY,
            authEnum::AUTH_LOAN_REQUEST_TO_PREPAYMENT,
            authEnum::AUTH_LOAN_REQUEST_TO_REPAYMENT,
            authEnum::AUTH_LOAN_CONTRACT,
            authEnum::AUTH_LOAN_WRITE_OFF,
            authEnum::AUTH_LOAN_OVERDUE,
            authEnum::AUTH_LOAN_DEDUCTING_PENALTIES,
        );
    }
}

class authGroup_insurance implements IauthGroup
{
    function getGroupKey()
    {
        return "insurance";//menu的key值
    }

    function getGroupName()
    {
        return "insurance";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_INSURANCE_PRODUCT,
            authEnum::AUTH_INSURANCE_CONTRACT,
        );
    }
}

class authGroup_setting implements IauthGroup
{
    function getGroupKey()
    {
        return "setting";//menu的key值
    }

    function getGroupName()
    {
        return "setting";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_SETTING_COMPANY_INFO,
            authEnum::AUTH_SETTING_CREDIT_LEVEL,
            authEnum::AUTH_SETTING_CREDIT_PROCESS,
            authEnum::AUTH_SETTING_GLOBAL,
            authEnum::AUTH_REGION_LIST,
//            authEnum::AUTH_SETTING_SYSTEM_DEFINE,
            authEnum::AUTH_SETTING_SHORT_CODE,
            authEnum::AUTH_SETTING_CODING_RULE,
            authEnum::AUTH_SETTING_RESET_SYSTEM
        );
    }
}

class authGroup_report implements IauthGroup
{
    function getGroupKey()
    {
        return "report";//menu的key值
    }

    function getGroupName()
    {
        return "report";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_REPORT_OVERVIEW,
            authEnum::AUTH_REPORT_CLIENT_LIST,
            authEnum::AUTH_REPORT_CONTRACT_LIST,
            authEnum::AUTH_REPORT_CREDIT_LIST,
            authEnum::AUTH_REPORT_TODAY_REPORT,
            authEnum::AUTH_REPORT_LOAN_LIST,
            authEnum::AUTH_REPORT_REPAYMENT_LIST,
            authEnum::AUTH_REPORT_ASSET_LIABILITY,
            authEnum::AUTH_REPORT_PROFIT_REPORT,
        );
    }
}

class authGroup_editor implements IauthGroup
{
    function getGroupKey()
    {
        return "editor";//menu的key值
    }

    function getGroupName()
    {
        return "editor";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_EDITOR_HELP,
        );
    }
}

class authGroup_tools implements IauthGroup
{
    function getGroupKey()
    {
        return "tools";//menu的key值
    }

    function getGroupName()
    {
        return "tools";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_TOOLS_CALCULATOR,
            authEnum::AUTH_TOOLS_SMS,
        );
    }
}

class authGroup_financial implements IauthGroup
{
    function getGroupKey()
    {
        return "financial";//menu的key值
    }

    function getGroupName()
    {
        return "financial";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_FINANCIAL_BANK_ACCOUNT,
            authEnum::AUTH_FINANCIAL_EXCHANGE_RATE
        );
    }
}

class authGroup_dev implements IauthGroup
{
    function getGroupKey()
    {
        return "dev";//menu的key值
    }

    function getGroupName()
    {
        return "dev";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_DEV_APP_VERSION,
            authEnum::AUTH_DEV_FUNCTION_SWITCH,
            authEnum::AUTH_DEV_RESET_PASSWORD,
        );
    }
}

/*counter 开始*/
class authGroup_counter_member implements IauthGroup
{
    function getGroupKey()
    {
        return "member";//menu的key值
    }

    function getGroupName()
    {
        return "member";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_MEMBER_REGISTER,
            authEnum::AUTH_MEMBER_DOCUMENT_COLLECTION,
            authEnum::AUTH_MEMBER_FINGERPRINT_COLLECTION,
            authEnum::AUTH_MEMBER_LOAN,
            authEnum::AUTH_MEMBER_DEPOSIT,
            authEnum::AUTH_MEMBER_WITHDRAWAL,
            authEnum::AUTH_MEMBER_PROFILE,
        );
    }
}

class authGroup_counter_company implements IauthGroup
{
    function getGroupKey()
    {
        return "company";//menu的key值
    }

    function getGroupName()
    {
        return "company";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_COMPANY_INDEX,
        );
    }
}

class authGroup_counter_service implements IauthGroup
{
    function getGroupKey()
    {
        return "service";//menu的key值
    }

    function getGroupName()
    {
        return "service";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_SERVICE_REQUEST_LOAN,
            authEnum::AUTH_SERVICE_CURRENCY_EXCHANGE,
        );
    }
}

class authGroup_counter_mortgage implements IauthGroup
{
    function getGroupKey()
    {
        return "mortgage";//menu的key值
    }

    function getGroupName()
    {
        return "mortgage";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_MORTGAGE_INDEX,
        );
    }
}

class authGroup_counter_cash_on_hand implements IauthGroup
{
    function getGroupKey()
    {
        return "cash_on_hand";//menu的key值
    }

    function getGroupName()
    {
        return "cash_on_hand";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CASH_CASH_ON_HAND,
        );
    }
}

class authGroup_counter_cash_in_vault implements IauthGroup
{
    function getGroupKey()
    {
        return "cash_in_vault";//menu的key值
    }

    function getGroupName()
    {
        return "cash_in_vault";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CASH_CASH_IN_VAULT,
        );
    }
}

/*counter 结束*/
