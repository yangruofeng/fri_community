<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/27
 * Time: 15:45
 */
class global_settingClass
{
    public function __construct()
    {
    }


    /** 重置数据库
     * @return bool
     */
    public static function resetSystemData()
    {
        set_time_limit(0);

        $conn = ormYo::Conn();

        // 清空member相关
        $conn->execute("truncate table client_member");
        $conn->execute("truncate table member_assets");
        $conn->execute("truncate table member_family");
        $conn->execute("truncate table member_guarantee");
        $conn->execute("truncate table member_login_log");
        $conn->execute("truncate table member_message");
        $conn->execute("truncate table member_message_receiver");
        $conn->execute("truncate table member_passport");
        $conn->execute("truncate table member_token");
        $conn->execute("truncate table member_verify_cert");
        $conn->execute("truncate table member_verify_cert_image");
        $conn->execute("truncate table member_work");
        $conn->execute("truncate table member_writtenoff");
        $conn->execute("truncate table member_trace_officer");
        $conn->execute("truncate table member_grade");
        $conn->execute("truncate table member_account_handler");
        $conn->execute("truncate table member_follow_officer");
        $conn->execute("truncate table member_credit");
        $conn->execute("truncate table member_credit_flow");

        // 删除产品相关
        /*$conn->execute("truncate table loan_product");
        $conn->execute("truncate table loan_product_condition");
        $conn->execute("truncate table loan_product_size_rate");
        $conn->execute("truncate table loan_product_special_rate");
        $conn->execute("truncate table insurance_product");
        $conn->execute("truncate table insurance_product_item");
        $conn->execute("truncate table insurance_product_relationship");*/

        // 清空账户信息
        $conn->execute("truncate table loan_account");
        $conn->execute("truncate table insurance_account");


        // 清空业务数据
        $conn->execute("truncate table client_black");
        $conn->execute("truncate table common_fingerprint_library");
        $conn->execute("truncate table common_sms");
        $conn->execute("truncate table common_verify_code");
        $conn->execute("truncate table common_verify_email");

        // 贷款
        $conn->execute("truncate table loan_credit_release");
        $conn->execute("truncate table loan_approval");
        $conn->execute("truncate table loan_apply");
        $conn->execute("truncate table loan_contract");
        $conn->execute("truncate table loan_contract_dun");
        $conn->execute("truncate table loan_deducting_penalties");
        $conn->execute("truncate table loan_disbursement");
        $conn->execute("truncate table loan_disbursement_scheme");
        $conn->execute("truncate table loan_installment_scheme");
        $conn->execute("truncate table loan_prepayment_apply");
        $conn->execute("truncate table loan_recoveries");
        $conn->execute("truncate table loan_repayment");
        $conn->execute("truncate table loan_request_repayment");
        $conn->execute("truncate table loan_writtenoff");

        // 保险
        $conn->execute("truncate table insurance_contract");
        $conn->execute("truncate table insurance_contract_beneficiary");
        $conn->execute("truncate table insurance_payment_record");
        $conn->execute("truncate table insurance_payment_scheme");

        return true;
    }


    public static function getCurrencyRateBetween($from_ccy,$to_ccy)
    {

        $m = new common_exchange_rateModel();
        $rate = $m->getRateBetween($from_ccy,$to_ccy);
        return $rate;
    }


    public static function getAllDictionary()
    {
        $m = new core_dictionaryModel();
        $list = $m->getAll();
        $return = array();
        foreach( $list as $v ){
            $return[$v['dict_key']] = $v['dict_value'];
        }
        return $return;
    }

    /** 信用激活是否检查指纹
     * @return int
     */
    public static function isCheckCreditFingerprintCert()
    {
        $is = 1;
        $m = new core_dictionaryModel();
        $set = $m->getDictionary('close_credit_fingerprint_cert');
        if( $set && $set['dict_value'] == 1 ){
            $is = 0;
        }
        return $is;
    }


    /** 信用激活是否检查授权合同
     * @return int
     */
    public static function isCheckCreditAuthorizedContract()
    {
        $is = 1;
        $m = new core_dictionaryModel();
        $set = $m->getDictionary('close_credit_authorized_contract');
        if( $set && $set['dict_value'] == 1 ){
            $is = 0;
        }
        return $is;
    }

    /** 获取常用的setting
     * @return array|mixed
     */
    public static function getCommonSetting()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('global_settings');
        if( $row && $row['dict_value'] ){
            $re = @json_decode($row['dict_value'],true);
        }
        return $re;
    }

    /** 获取所有功能状态
     * @return array|mixed
     */
    public static function getFunctionSwitch()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('function_switch');
        if( $row && $row['dict_value'] ){
            $re = @json_decode($row['dict_value'],true);
        }
        return $re;
    }


    /** 是否开启重置密码功能
     * @return int|mixed
     */
    public static function isCanResetPassword()
    {
        $is = 1;
        $re = self::getFunctionSwitch();
        if( $re['close_reset_password'] ){
            $is = 0;
        }
        return $is;
    }


    /** 是否开启信用贷提现
     * @return int|mixed
     */
    public static function isCanCreditLoanWithdraw()
    {
        $is = 1;
        $re = self::getFunctionSwitch();
        if( $re['close_credit_withdraw'] ){
            $is = 0;
        }
        return $is;
    }

    /** 是否开启注册就送信用
     * @return int
     */
    public static function isAllowRegisterToSendCredit()
    {
        $is = 1;
        $re = self::getFunctionSwitch();
        if( $re['close_register_send_credit'] ){
            $is = 0;
        }
        return $is;
    }


    /** 获取公司信息
     * @return array|mixed
     */
    public static function getCompanyInfo()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('company_config');
        if( $row && $row['dict_value'] ){
            $re = @json_decode($row['dict_value'],true);
            $re['company_icon'] = getCompanyIconUrl($re['company_icon']);
            $re['address_detail'] = $re['address_region'].','.$re['address_detail'];
            $re['branch_list'] = self::getCompanyBranchList();
        }
        return $re;
    }

    /** 获取公司所有分部
     * @return array|ormCollection
     */
    public static function getCompanyBranchList()
    {
        $list = array();
        $reader = new ormReader();
        $sql = "select b.*,u.user_code,u.user_name manager_name,u.mobile_phone manager_phone,u.email manager_email from site_branch b left join um_user u on b.manager=u.uid  
        where b.status='1' ";
        $rows = $reader->getRows($sql);
        if( count($rows) > 0 ){
            $list = $rows;
        }
        return $list;
    }

    /** 获取公司热线电话
     * @return array
     */
    public static function getCompanyHotline()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('company_config');
        if( $row && $row['dict_value'] ){
            $arr = @json_decode($row['dict_value'],true);
            $re = $arr['hotline'];
        }
        return $re;
    }


    public static function getCertSampleImage()
    {
        Language::read('certification');

        $url = PROJECT_RESOURCE_SITE_URL.'/certificate_sample';

        return array(

            certificationTypeEnum::ID =>array(
                array(
                    'des' => L('cert_sample_des_id_handheld'),
                    'image' => $url.'/id/handheld.png'
                ),
                array(
                    'des' => L('cert_sample_des_id_front'),
                    'image' => $url.'/id/front.png'
                ),
                array(
                    'des' => L('cert_sample_des_id_back'),
                    'image' => $url.'/id/back.png'
                ),
            ),

            certificationTypeEnum::FAIMILYBOOK => array(
                array(
                    'des' => L('cert_sample_des_family_book_front'),
                    'image' => $url.'/family_book/front.png'
                ),
                array(
                    'des' => L('cert_sample_des_family_book_back'),
                    'image' => $url.'/family_book/back.png'
                ),
                array(
                    'des' => L('cert_sample_des_family_householder'),
                    'image' => $url.'/family_book/household.png'
                ),
            ),

            certificationTypeEnum::PASSPORT => null,

            certificationTypeEnum::RESIDENT_BOOK => array(
                array(
                    'des' => L('cert_sample_des_resident_book_front'),
                    'image' => $url.'/resident_book/front.png'
                ),
                array(
                    'des' => L('cert_sample_des_resident_book_back'),
                    'image' => $url.'/resident_book/back.png'
                )
            ),

            certificationTypeEnum::WORK_CERTIFICATION => null,

            certificationTypeEnum::MOTORBIKE => array(
                array(
                    'des' => L('cert_sample_des_motorbike_certificate_front'),
                    'image' => $url.'/motorbike/certificate_front.png'
                ),
                array(
                    'des' => L('cert_sample_des_motorbike_certificate_back'),
                    'image' => $url.'/motorbike/certificate_back.png'
                ),
                array(
                    'des' => L('cert_sample_des_motorbike_photo'),
                    'image' => $url.'/motorbike/motorbike.jpg'
                ),
            ),

            certificationTypeEnum::CAR => array(
                array(
                    'des' => L('cert_sample_des_car_certificate_front'),
                    'image' => $url.'/car/certificate_front.png'
                ),
                array(
                    'des' => L('cert_sample_des_car_certificate_back'),
                    'image' => $url.'/car/certificate_back.png'
                ),
                array(
                    'des' => L('cert_sample_des_car_front'),
                    'image' => $url.'/car/car_photo.png'
                ),
                array(
                    'des' => L('cert_sample_des_car_back'),
                    'image' => $url.'/car/car_photo.png'
                ),
            ),

            certificationTypeEnum::HOUSE => array(
                array(
                    'des' => L('cert_sample_des_house_front'),
                    'image' => $url.'/house/house_front.png'
                ),
                array(
                    'des' => L('cert_sample_des_house_front_road'),
                    'image' => $url.'/house/house_front_road.png'
                ),
                array(
                    'des' => L('cert_sample_des_house_side_face'),
                    'image' => $url.'/house/house_side_face.png'
                ),
                array(
                    'des' => L('cert_sample_des_house_property_card'),
                    'image' => $url.'/house/property_card.png'
                ),
            ),

            certificationTypeEnum::LAND => array(
                array(
                    'des' => L('cert_sample_des_land_property_card'),
                    'image' => $url.'/land/property_card.png'
                ),
                array(
                    'des' => L('cert_sample_des_land_trading_record'),
                    'image' => $url.'/land/trading_record.png'
                ),
            )


        );
    }


    public static function getSystemHelpList($type,$page_num,$page_size)
    {
        $page_num = $page_num?:1;
        $page_size = $page_size?:100000;
        switch($type){
            case 'all':
                $sql = "select * from common_help where is_system='1' and state='".helpStateEnum::SHOW."' order by sort desc";
                break;
            case helpCategoryEnum::CREDIT_LOAN :
                $sql = "select * from common_help where is_system='1' and state='".helpStateEnum::SHOW."' and category='$type' order by sort desc";
                break;
            case helpCategoryEnum::INSURANCE :
                $sql = "select * from common_help where is_system='1' and state='".helpStateEnum::SHOW."' and category='$type' order by sort desc";
                break;
            default:
                $sql = "select * from common_help where is_system='1' and state='".helpStateEnum::SHOW."' order by sort desc";
                break;
        }
        $r = new ormReader();
        $re = $r->getPage($sql,$page_num,$page_size);
        return new result(true,'success',array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $re->rows
        ));
    }


    public static function getCompanyGlobalReceiveBankAccount($currency='')
    {
        $where = '';
        if( $currency ){
            $where .= " and currency='$currency' ";
        }
        $r = new ormReader();
        $sql = "select uid,bank_code,currency,bank_name,bank_account_no,bank_account_name,bank_address,bank_account_phone from site_bank 
        where is_private=0 and account_state=1 $where ";
        $rows = $r->getRows($sql);
        return $rows;
    }

    public static function getChildrenAddressList($pid=0)
    {
        $r = new ormReader();
        $sql = "select * from core_tree where root_key='region' and pid='$pid' ";
        $list = $r->getRows($sql);
        return $list;
    }


    public static function currencyExchangeRate()
    {

        $r = new ormReader();
        $sql = "select * from common_exchange_rate";
        $list = $r->getRows($sql);
        return $list;
    }


    public static function getBankLogo()
    {
        return array(
            'wing' => 'wing_logo.png',
            'ftb' => 'ftb_logo.png',
            'aba' => 'aba_logo.png',
            'sacom' => 'sacom_logo.png',
            'cpb' => 'cpb_logo.png',
            'canadia' => 'canadia_logo.png',
            'acleda' => 'acleda_logo.png',
            'maybank' => 'maybank_logo.png',
            'smartluy' => 'smartluy_logo.png',
            'truemoney' => 'truemoney_logo.png',
            'anzroyal' => 'anzroyal_logo.png',
            'default_logo' => 'default_logo.png'
        );
    }

    public static function getBankLogoByBankCode($bank_code)
    {
        $source_url = trim(getConf('global_resource_site_url'),'/').'/images/bank';
        $bank_logo = self::getBankLogo();

        $logo_url = $source_url.'/'.$bank_logo[$bank_code];
        if( !fopen($logo_url,'r') ){
            $logo_url = $source_url.'/default_logo.png';
        }
        return $logo_url;
    }

}