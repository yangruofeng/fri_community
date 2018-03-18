<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/21
 * Time: 16:49
 */
class enum_langClass{

    public function __construct()
    {
        //Language::read('certification');
    }


    public static function getCertificationTypeEnumLang()
    {
        return array(
            certificationTypeEnum::ID => L('certification_id'),
            certificationTypeEnum::PASSPORT => L('certification_passport'),
            certificationTypeEnum::FAIMILYBOOK => L('certification_family_book'),
            certificationTypeEnum::GUARANTEE_RELATIONSHIP => 'Guarantee Relationship',
            certificationTypeEnum::WORK_CERTIFICATION => L('certification_work'),
            //certificationTypeEnum::CIVIL_SERVANT => L('certification_civil_servant'),
            certificationTypeEnum::CAR => L('certification_car_asset'),
            certificationTypeEnum::HOUSE => L('certification_house_asset'),
            certificationTypeEnum::LAND => L('certification_land_asset'),
            certificationTypeEnum::RESIDENT_BOOK => L('certification_resident_book'),
            certificationTypeEnum::MOTORBIKE => L('certification_motorbike'),
        );
    }

    public static function getClientTypeLang()
    {
        return array(
            clientTypeRateEnum::STAFF => L('loan_client_staff'),
            clientTypeRateEnum::GOVERNMENT => L('loan_client_government'),
            clientTypeRateEnum::RIVAL_CLIENT => L('loan_client_rival_client'),
        );
    }

    public static function getPaymentTypeLang()
    {
        return array(
            memberAccountHandlerTypeEnum::CASH => 'Cash',
            memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY => 'Asiaweiluy',
            memberAccountHandlerTypeEnum::BANK => 'Bank transfer',
            memberAccountHandlerTypeEnum::PARTNER_LOAN => 'Loan',
            memberAccountHandlerTypeEnum::PASSBOOK => 'Passbook'
        );
    }


    public static function getLoanApplySourceLang()
    {
        return array(
            loanApplySourceEnum::MEMBER_APP => 'Member App',
            loanApplySourceEnum::OPERATOR_APP => 'Operator App',
            loanApplySourceEnum::PHONE => 'Phone',
            loanApplySourceEnum::FACEBOOK => 'Facebook',
            loanApplySourceEnum::CLIENT => 'Client'
         );
    }

    public static function getLoanApplyStateLang()
    {
        return array(
            loanApplyStateEnum::LOCKED => 'Handling,locked',
            loanApplyStateEnum::CREATE => 'New Apply',
            loanApplyStateEnum::OPERATOR_REJECT => 'Operator Reject',
            loanApplyStateEnum::ALLOT_CO => 'Allot to CO',
            loanApplyStateEnum::CO_HANDING => 'CO handling',
            loanApplyStateEnum::CO_CANCEL => 'CO canceled',
            loanApplyStateEnum::CO_APPROVED => 'CO approved',
            loanApplyStateEnum::BM_APPROVED => 'BM approved',
            loanApplyStateEnum::BM_CANCEL => 'BM canceled',
            loanApplyStateEnum::HQ_APPROVED => 'HQ approved',
            loanApplyStateEnum::HQ_CANCEL => 'HQ canceled',
            loanApplyStateEnum::ALL_APPROVED_CANCEL => 'Client cancel',
            loanApplyStateEnum::DONE => 'Completely done'
        );
    }


    public static function getLoanInstallmentTypeLang()
    {
        return array(
            interestPaymentEnum::SINGLE_REPAYMENT => 'Single Repayment',
            interestPaymentEnum::ANNUITY_SCHEME => 'Annuity Schema',
            interestPaymentEnum::BALLOON_INTEREST => 'Balloon Interest',
            interestPaymentEnum::FIXED_PRINCIPAL => 'Fixed Principal',
            interestPaymentEnum::FLAT_INTEREST => 'Flat Interest'
        );
    }


    public static function getLoanTimeUnitLang()
    {
        return array(
            loanPeriodUnitEnum::YEAR => 'Year',
            loanPeriodUnitEnum::MONTH => 'Month',
            loanPeriodUnitEnum::DAY => 'Day',
        );
    }

    public static function getMemberStateLang(){

        return array(
            memberStateEnum::CANCEL => 'Cancel',
            memberStateEnum::CREATE => 'Create',
            memberStateEnum::CHECKED => 'Checked',
            memberStateEnum::LOCKING => 'Locking',
            memberStateEnum::VERIFIED => 'Verified',
        );
    }


    public static function getPassbookTradingTypeLang()
    {
        $dict = (new passbookTradingTypeEnum())->toArray();
        $re_lang = array();
        foreach( $dict as $type ){
            $re_lang[$type] = ucwords(str_replace('_',' ',$type));
        }

        return $re_lang;
    }


    public static function getComplaintAdviceStateLang(){

        return array(
            complaintAdviceEnum::CREATE => 'Create',
            complaintAdviceEnum::HANDLE => 'Handle',
            complaintAdviceEnum::CHECKED => 'Checked',
        );
    }


}