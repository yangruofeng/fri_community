<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/25
 * Time: 11:27
 */
class memberControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('member');
    }

    public function registerOp()
    {
        Tpl::showPage('register');
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeForRegisterOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone_number = trim($p['phone']);
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他member注册过
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if ($row) {
            return new result(false, 'The phone number has been registered.');
        }

        $rt = $this->sendVerifyCodeOp($p);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE), array('code' => $rt->CODE, 'msg' => $rt->MSG));
        }
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeOp($p)
    {
        $data = $p;
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $data);
        debug($rt);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    /**
     * 注册账号
     * @param $p
     * @return result
     */
    public function registerClientOp($p)
    {
        $p['open_source'] = 1;
        $p['password'] = $p['login_password'];
        $p['login_code'] = $p['login_account'];
        $p['sms_id'] = $p['verify_id'];
        $p['sms_code'] = $p['verify_code'];
        $rt = memberClass::phoneRegisterNew($p);
        return $rt;
    }

    /**
     * 证件采集
     */
    public function documentCollectionOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if ($client_info) {
            $format_phone = tools::separatePhone($client_info['phone_id']);
            Tpl::output('phone_arr', $format_phone);
        }
        Tpl::showPage('document.collection');
    }

    /**
     * 获取client信息
     * @param $p
     * @return result
     */
    public function getClientInfoOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_member_verify_cert = M('member_verify_cert');
        $member_verify_cert = $m_member_verify_cert->select(array('member_id' => $client_info['uid'], 'verify_state' => array('<=', certStateEnum::PASS)));
        foreach ($member_verify_cert as $val) {
            switch ($val['cert_type']) {
                case certificationTypeEnum::ID:
                    $client_info['identity_authentication'] = $val['verify_state'] == 10 ? 1 : 0;
                    break;
                case certificationTypeEnum::FAIMILYBOOK:
                    $client_info['family_book'] = $val['verify_state'] == 10 ? 1 : 0;
                    break;
                case certificationTypeEnum::WORK_CERTIFICATION:
                    $client_info['working_certificate'] = $val['verify_state'] == 10 ? 1 : 0;
                    break;
                case certificationTypeEnum::RESIDENT_BOOK:
                    $client_info['resident_book'] = $val['verify_state'] == 10 ? 1 : 0;
                    break;
                default:
            }
        }

        if (!isset($client_info['identity_authentication'])) {
            $client_info['identity_authentication'] = 0;
        }

        if (!isset($client_info['family_book'])) {
            $client_info['family_book'] = 0;
        }

        if (!isset($client_info['working_certificate'])) {
            $client_info['working_certificate'] = 0;
        }

        if (!isset($client_info['resident_book'])) {
            $client_info['resident_book'] = 0;
        }

        $r = new ormReader();
        $sql = "SELECT cert_type,COUNT(uid) cert_num FROM member_verify_cert WHERE member_id = " . $client_info['uid'] . " AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type";
        $member_assets = $r->getRows($sql);
        foreach ($member_assets as $val) {
            switch ($val['cert_type']) {
                case certificationTypeEnum::CAR:
                    $client_info['vehicle_property'] = $val['cert_num'];
                    break;
                case certificationTypeEnum::LAND:
                    $client_info['land_property'] = $val['cert_num'];
                    break;
                case certificationTypeEnum::HOUSE:
                    $client_info['housing_property'] = $val['cert_num'];;
                    break;
                case certificationTypeEnum::MOTORBIKE:
                    $client_info['motorcycle_asset_certificate'] = $val['cert_num'];;
                    break;
                default:
            }
        }

        if (!isset($client_info['vehicle_property'])) {
            $client_info['vehicle_property'] = 0;
        }

        if (!isset($client_info['land_property'])) {
            $client_info['land_property'] = 0;
        }

        if (!isset($client_info['housing_property'])) {
            $client_info['housing_property'] = 0;
        }

        if (!isset($client_info['motorcycle_asset_certificate'])) {
            $client_info['motorcycle_asset_certificate'] = 0;
        }

        $sql = "SELECT COUNT(uid) guarantee_num FROM member_guarantee WHERE member_id = " . $client_info['uid'] . " AND relation_state = 100";
        $guarantee_num = $r->getOne($sql);
        $client_info['guarantee_num'] = intval($guarantee_num);
        return new result(true, '', $client_info);
    }

    /**
     * 身份证信息
     */
    public function identityAuthenticationOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        $country_code = (new nationalityEnum)->Dictionary();
        Tpl::output('country_code', $country_code);

        Tpl::showPage('document.identity.authentication');
    }

    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

    /**
     * 保存身份证信息
     * @param $p
     * @return result
     */
    public function saveIdentityAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $id_number = trim($p['id_number']);
        $expire_date = date('Y-m-d', strtotime($p['expire_date']));
        $gender = intval($p['gender']);
        $civil_status = intval($p['civil_status']);
        $birthday = date('Y-m-d', strtotime($p['birthday']));
        $birth_country = trim($p['birth_country']);
        $birth_province = intval($p['birth_province']);
        $birth_district = intval($p['birth_district']);
        $birth_commune = intval($p['birth_commune']);
        $birth_village = intval($p['birth_village']);
        $address = trim($p['address']);

        $kh_family_name = trim($p['kh_family_name']);
        $kh_given_name = trim($p['kh_given_name']);
        $kh_second_name = trim($p['kh_second_name']);
        $kh_third_name = trim($p['kh_third_name']);

        $en_family_name = trim($p['en_family_name']);
        $en_given_name = trim($p['en_given_name']);
        $en_second_name = trim($p['en_second_name']);
        $en_third_name = trim($p['en_third_name']);

        $handheld_img = trim($p['handheld_img']);
        $frontal_img = trim($p['frontal_img']);
        $back_img = trim($p['back_img']);

        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        if (!$id_number || !$birth_country || !$handheld_img || !$frontal_img || !$back_img || $birth_province == 0 || $birth_district == 0 || $birth_commune == 0 || $birth_village == 0 || !$address) {
            return new result(false, 'Param Error!');
        }

        $row->id_sn = $id_number;
        $row->nationality = $birth_country;
        $row->id_en_name_json = my_json_encode(
            array(
                'en_family_name' => $en_family_name,
                'en_given_name' => $en_given_name,
                'en_second_name' => $en_second_name,
                'en_third_name' => $en_third_name,
            )
        );
        $row->id_kh_name_json = my_json_encode(
            array(
                'kh_family_name' => $kh_family_name,
                'kh_given_name' => $kh_given_name,
                'kh_second_name' => $kh_second_name,
                'kh_third_name' => $kh_third_name,
            )
        );

        $row->initials = strtoupper(substr($en_family_name, 0, 1));
        $row->display_name = $en_family_name . ' ' . $en_given_name;
        $row->kh_display_name = $kh_family_name . ' ' . $kh_given_name;

        $row->gender = $gender;
        $row->civil_status = $civil_status;
        $row->birthday = $birthday;

        $row->id_address1 = $birth_province;
        $row->id_address2 = $birth_district;
        $row->id_address3 = $birth_commune;
        $row->id_address4 = $birth_village;
        $row->id_expire_time = $expire_date;
        $row->update_time = Now();

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $sql = "UPDATE member_verify_cert SET verify_state='" . certStateEnum::EXPIRED . "' WHERE member_id='" . $uid . "'
                 AND cert_type='" . certificationTypeEnum::ID . "' AND verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed!');
            }

            $m_member_verify_cert = M('member_verify_cert');
            $m_member_verify_cert_image = M('member_verify_cert_image');
            $row_cert = $m_member_verify_cert->newRow();
            $row_cert->member_id = $uid;
            $row_cert->cert_type = certificationTypeEnum::ID;
            $row_cert->cert_name = $row->display_name;
            $row_cert->cert_sn = $id_number;
            $row_cert->cert_addr = $address;
            $row_cert->cert_expire_time = $expire_date;
            $row_cert->source_type = 1;
            $row_cert->verify_state = certStateEnum::PASS;
            $row_cert->auditor_id = $this->user_id;
            $row_cert->auditor_name = $this->user_name;
            $row_cert->auditor_time = Now();
            $row_cert->create_time = Now();
            $rt_2 = $row_cert->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed!');
            }

            $cert_images = array(
                certImageKeyEnum::ID_HANDHELD => $handheld_img,
                certImageKeyEnum::ID_FRONT => $frontal_img,
                certImageKeyEnum::ID_BACK => $back_img,
            );
            foreach ($cert_images as $key => $img) {
                $row_cert_img = $m_member_verify_cert_image->newRow();
                $row_cert_img->cert_id = $rt_2->AUTO_ID;
                $row_cert_img->image_key = $key;
                $row_cert_img->image_url = $img;
                $row_cert_img->image_sha = sha1_file($img);
                $rt_3 = $row_cert_img->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 工作证明信息
     * @return result
     */
    public function workAuthenticationOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.work.authentication');
    }

    /**
     * 保存工作证明
     * @param $p
     * @return result
     */
    public function saveWorkAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $company_name = trim($p['company_name']);
        $company_address = trim($p['company_address']);
        $position = trim($p['position']);
        $is_government = intval($p['is_government']);

        $working_certificate = trim($p['working_certificate']);
        $work_employment_certification = trim($p['work_employment_certification']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        if (!$company_name || !$company_address || !$position || !$working_certificate || !$work_employment_certification) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $uid . "'
                 and cert_type='" . certificationTypeEnum::WORK_CERTIFICATION . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            // 当前只能有一条合法的，其余的更新为历史
            $sql = "update member_work set state='" . workStateStateEnum::HISTORY . "' where member_id='" . $uid . "' and state='" . workStateStateEnum::VALID . "' ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_work = new member_workModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = certificationTypeEnum::WORK_CERTIFICATION;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->auditor_id = $this->user_id;
            $new_row->auditor_name = $this->user_name;
            $new_row->auditor_time = Now();
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = array(
                certImageKeyEnum::WORK_CARD => $working_certificate,
                certImageKeyEnum::WORK_EMPLOYMENT_CERTIFICATION => $work_employment_certification,
            );

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file($img);;
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $row = $m_work->newRow();
            $row->cert_id = $new_row->uid;
            $row->member_id = $uid;
            $row->company_name = $company_name;
            $row->company_addr = $company_address;
            $row->position = $position;
            $row->is_government = $is_government;
            $row->create_time = Now();
            $in = $row->insert();
            if (!$in->STS) {
                $conn->rollback();
                return new result(false, 'Add work cert fail');
            }

            $client_info->is_government = $is_government;
            $client_info->update_time = Now();
            $rt = $client_info->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add work cert fail');
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 户口簿信息
     * @return result
     */
    public function familyBookAuthenticationOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.family.book.authentication');
    }

    /**
     * 保存户口簿信息
     * @param $p
     * @return result
     */
    public function saveFamilyBookAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $family_book_front = trim($p['family_book_front']);
        $family_book_back = trim($p['family_book_back']);
        $family_book_household = trim($p['family_book_household']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        if (!$family_book_front || !$family_book_back || !$family_book_household) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $uid . "'
                 and cert_type='" . certificationTypeEnum::FAIMILYBOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = certificationTypeEnum::FAIMILYBOOK;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->auditor_id = $this->user_id;
            $new_row->auditor_name = $this->user_name;
            $new_row->auditor_time = Now();
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = array(
                certImageKeyEnum::FAMILY_BOOK_FRONT => $family_book_front,
                certImageKeyEnum::FAMILY_BOOK_BACK => $family_book_back,
                certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD => $family_book_household,
            );

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file($img);;
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 暂住证
     */
    public function residentBookAuthenticationOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.resident.book.authentication');
    }

    /**
     * 保存居住证信息
     * @param $p
     * @return result
     */
    public function saveResidentBookAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $resident_book_front = trim($p['resident_book_front']);
        $resident_book_back = trim($p['resident_book_back']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        if (!$resident_book_front || !$resident_book_back) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $uid . "'
                 and cert_type='" . certificationTypeEnum::RESIDENT_BOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = certificationTypeEnum::RESIDENT_BOOK;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->auditor_id = $this->user_id;
            $new_row->auditor_name = $this->user_name;
            $new_row->auditor_time = Now();
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = array(
                certImageKeyEnum::RESIDENT_BOOK_FRONT => $resident_book_front,
                certImageKeyEnum::RESIDENT_BOOK_BACK => $resident_book_back,
            );

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file($img);;
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 汽车证明
     */
    public function vehiclePropertyAuthenticateOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.vehicle.property.authentication');
    }

    /**
     * 保存汽车资产证明
     * @param $p
     * @return result
     */
    public function saveVehiclePropertyAuthenticationOp($p)
    {
        $car_cert_front = trim($p['car_cert_front']);
        $car_cert_back = trim($p['car_cert_back']);
        $car_front = trim($p['car_front']);
        $car_back = trim($p['car_back']);

        if (!$car_cert_front || !$car_cert_back || !$car_front || !$car_back) {
            return new result(false, 'Param Error!');
        }

        $p['cert_type'] = certificationTypeEnum::CAR;
        $p['cert_images'] = array(
            certImageKeyEnum::CAR_CERT_FRONT => $car_cert_front,
            certImageKeyEnum::CAR_CERT_BACK => $car_cert_back,
            certImageKeyEnum::CAR_FRONT => $car_front,
            certImageKeyEnum::CAR_BACK => $car_back,
        );
        return $this->addVerifyCert($p);
    }

    /**
     * 获取历史记录
     * @param $p
     * @return array
     */
    public function getCertificationListOp($p)
    {
        $uid = intval($p['uid']);
        $cert_type = intval($p['cert_type']);
        $r = new ormReader();

        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where 1=1  ";

        if ($uid) {
            $sql1 .= " and verify.member_id = $uid";
        }
        if ($cert_type) {
            $sql1 .= " and verify.cert_type = $cert_type";
        }

        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $row['uid'] . "'";
            $images = $r->getRows($sql);
            $row['cert_images'] = $images;
            $list[] = $row;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 土地证明
     */
    public function landPropertyAuthenticateOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.land.property.authentication');
    }

    /**
     * 保存土地证明
     * @param $p
     * @return result
     */
    public function saveLandPropertyAuthenticationOp($p)
    {
        $land_property_card = trim($p['land_property_card']);
        $land_trading_record = trim($p['land_trading_record']);
        if (!$land_property_card || !$land_trading_record) {
            return new result(false, 'Param Error!');
        }

        $p['cert_type'] = certificationTypeEnum::LAND;
        $p['cert_images'] = array(
            certImageKeyEnum::CAR_CERT_FRONT => $land_property_card,
            certImageKeyEnum::CAR_CERT_BACK => $land_trading_record,
        );

        return $this->addVerifyCert($p);

    }

    public function housingPropertyAuthenticateOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.housing.property.authentication');
    }

    /**
     * 保存房产信息
     * @param $p
     * @return result
     */
    public function saveHousingPropertyAuthenticationOp($p)
    {
        $house_property_card = trim($p['house_property_card']);
        $house_relationships_certify = trim($p['house_relationships_certify']);
        $house_front = trim($p['house_front']);
        $house_side_face = trim($p['house_side_face']);
        $house_front_road = trim($p['house_front_road']);
        $house_inside = trim($p['house_inside']);
        if (!$house_property_card || !$house_relationships_certify || !$house_front || !$house_side_face || !$house_front_road || !$house_inside) {
            return new result(false, 'Param Error!');
        }

        $p['cert_type'] = certificationTypeEnum::HOUSE;
        $p['cert_images'] = array(
            certImageKeyEnum::HOUSE_PROPERTY_CARD => $house_property_card,
            certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY => $house_relationships_certify,
            certImageKeyEnum::HOUSE_FRONT => $house_front,
            certImageKeyEnum::HOUSE_SIDE_FACE => $house_side_face,
            certImageKeyEnum::HOUSE_FRONT_ROAD => $house_front_road,
            certImageKeyEnum::HOUSE_INSIDE => $house_inside,
        );

        return $this->addVerifyCert($p);
    }

    /**
     * 摩托车
     */
    public function motorcyclePropertyAuthenticateOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }

        Tpl::output('client_info', $client_info);

        Tpl::showPage('document.motorcycle.property.authentication');
    }

    /**
     * 保存房产信息
     * @param $p
     * @return result
     */
    public function saveMotorcyclePropertyAuthenticationOp($p)
    {
        $motorbike_cert_front = trim($p['motorbike_cert_front']);
        $motorbike_cert_back = trim($p['motorbike_cert_back']);
        $motorbike_photo = trim($p['motorbike_photo']);
        if (!$motorbike_cert_front || !$motorbike_cert_back || $motorbike_photo) {
            return new result(false, 'Param Error!');
        }

        $p['cert_type'] = certificationTypeEnum::MOTORBIKE;
        $p['cert_images'] = array(
            certImageKeyEnum::MOTORBIKE_CERT_FRONT => $motorbike_cert_front,
            certImageKeyEnum::MOTORBIKE_CERT_BACK => $motorbike_cert_back,
            certImageKeyEnum::MOTORBIKE_PHOTO => $motorbike_photo,
        );

        return $this->addVerifyCert($p);
    }

    /**
     * 保存资产证明信息
     * @param $param
     * @return result
     */
    private function addVerifyCert($param)
    {
        $uid = intval($param['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = $param['cert_type'];
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->auditor_id = $this->user_id;
            $new_row->auditor_name = $this->user_name;
            $new_row->auditor_time = Now();
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = $param['cert_images'];

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file($img);;
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 担保相关
     */
    public function guaranteeAuthenticateOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showPage('No eligible clients!');
        }
        Tpl::output('client_info', $client_info);

        $m_core_definition = new core_definitionModel();
        $define_arr = $m_core_definition->getDefineByCategory(array('guarantee_relationship'));
        Tpl::output("guarantee_relationship", $define_arr['guarantee_relationship']);

        Tpl::showPage('document.guarantee.authentication');
    }

    /**
     * 保存担保人
     * @param $p
     * @return result
     */
    public function saveGuarantorAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $relationship = $p['relationship'];
        $member_account = trim($p['member_account']);
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $guarantor = $m_client_member->find(array('login_code' => $member_account, 'phone_id' => $contact_phone));
        if (!$guarantor || $guarantor['uid'] == $uid) {
            return new result(false, 'Guarantor information error.');
        }

        $m_member_guarantee = M('member_guarantee');
        $chk = $m_member_guarantee->find(array('member_id' => $uid, 'relation_member_id' => $guarantor['uid'], 'relation_state' => array('neq', 11)));
        if ($chk) {
            return new result(false, 'The guarantor already exists.');
        }

        $row = $m_member_guarantee->newRow();
        $row->member_id = $uid;
        $row->relation_member_id = $guarantor['uid'];
        $row->relation_type = $relationship;
        $row->create_time = Now();
        $row->relation_state = 0;
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful.', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));
        } else {
            return new result(false, 'Add Failed.');
        }
    }

    /**
     * 获取担保记录
     * @param $p
     * @return array
     */
    public function getGuarantorListOp($p)
    {
        $uid = intval($p['uid']);
        $type = trim($p['type']);

        $r = new ormReader();
        if ($type == 'guarantor') {
            $sql = "select mg.*,cm.display_name,phone_id from member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.member_id = $uid AND relation_state != 11 order by mg.uid desc";
        } else {
            $sql = "select mg.*,cm.display_name,phone_id from member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.relation_member_id = $uid AND relation_state != 11 order by mg.uid desc";
        }

        $rows = $r->getRows($sql);
        return array(
            "sts" => true,
            "data" => $rows,
        );
    }

    /**
     * 指纹录入
     */
    public function fingerprintCollectionOp()
    {
        Tpl::showPage('fingerprint.collection');
    }

    /**
     * 获取指纹信息
     * @param $p
     * @return result
     */
    public function getClientFingermarkOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_common_fingerprint_library = M('common_fingerprint_library');
        $fingerprint_info = $m_common_fingerprint_library->find(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
        if ($fingerprint_info) {
            $client_info['feature_img'] = $fingerprint_info['feature_img'];
            $client_info['certification_status'] = 'Registered';
            $client_info['certification_time'] = timeFormat($fingerprint_info['create_time']);
        } else {
            $client_info['feature_img'] = 'resource/img/member/photo.png';
            $client_info['certification_status'] = 'Unregistered';
            $client_info['certification_time'] = '';
        }

        return new result(true, '', $client_info);
    }

    /**
     * 保存指纹
     * @param $p
     * @return result
     */
    public function saveFeatureAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $feature_img = $p['feature_img'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_common_fingerprint_library = M('common_fingerprint_library');


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $fingerprint = $m_common_fingerprint_library->getRow(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
            if ($fingerprint) {
                $rt_1 = $fingerprint->delete();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Failed!1');
                }
            }

            $fingerprint_row = $m_common_fingerprint_library->newRow();
            $fingerprint_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
            $fingerprint_row->obj_uid = $client_info['obj_guid'];
            $fingerprint_row->finger_index = 1;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->create_time = Now();
            $rt_2 = $fingerprint_row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!2');
            }

            $client_info->fingerprint = $feature_img;
            $client_info->update_time = Now();
            $rt_3 = $client_info->update();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!3');
            }

            $conn->submitTransaction();
            return new result(false, 'Add Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    /**
     * 贷款
     */
    public function loanOp()
    {
        $uid = $_GET['uid'];
        if ($uid) {
            $info = $this->getContractInfoOp(array(), $uid);
            Tpl::output('loan_info', $info['data']);
        }
        Tpl::showpage('loan');
    }

    /**
     * 获取合同信息
     * @param $p
     * @param $uid
     * @return array
     */
    public function getContractInfoOp($p, $uid)
    {
        $uid = $uid ?: intval($p['search_text']);
        $r = new ormReader();

        $sql = "SELECT contract.*,account.obj_guid,product.product_code,product.product_name,product.product_description,product.product_feature,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid"
            . " left join loan_product as product on contract.product_id = product.uid where contract.uid = " . $uid;
        $info = $r->getRow($sql);
        if (!$info) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }

        $sql = "select count(uid) left_period,sum(receivable_principal) left_principal from loan_installment_scheme where contract_id='$uid'  and state !='" . schemaStateTypeEnum::COMPLETE . "' ";
        $row = $r->getRow($sql);
        $info['left_period'] = $row['left_period'];
        $info['left_principal'] = $row['left_principal'] > $info['receivable_principal'] ? $info['receivable_principal'] : $row['left_principal'];

        $sql1 = "select * from loan_disbursement_scheme where contract_id = " . $uid;
        $disbursement = $r->getRows($sql1);
        $info["disbursement"] = $disbursement;

        $sql2 = "select * from loan_installment_scheme where contract_id = " . $uid;
        $installment = $r->getRows($sql2);
        $penalties_total = 0;
        $time = date('Y-m-d 23:59:59', time());
        $repayment_arr = array();
        foreach ($installment as $key => $val) {
            if ($val['penalty_start_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($val['uid']);
                $val['penalties'] = $penalties;
                $penalties_total += $penalties;
                $installment[$key] = $val;
            }
            if ($val['receivable_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $repayment_arr[] = $val;
            }
        }

        $info['penalties_total'] = $penalties_total;
        $info['installment'] = $installment;

        $insurance_arr = $this->getInsurancePrice($p['uid']);
        $info['insurance'] = $insurance_arr;
        $info['repayment_arr'] = $repayment_arr;
        return array(
            "sts" => true,
            "data" => $info,
        );

    }

    public function getInsurancePrice($uid = 0)
    {
        $r = new ormReader();
        $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract GROUP BY loan_contract_id";
        if ($uid) {
            $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract where loan_contract_id = " . $uid . " GROUP BY loan_contract_id";
        }
        $insurance = $r->getRows($sql1);
        $insurance_arr = array();
        foreach ($insurance as $key => $value) {
            $insurance_arr[$value['loan_contract_id']] = $value;
        }
        return $insurance_arr;
    }

    /**
     * 确认还款
     * @param $p
     * @return result
     */
    public function submitRepaymentOp($p)
    {
        $uid = intval($p['uid']);
        $repayment_total = round($p['repayment_total'], 2);
        $remark = trim($p['remark']);
        $currency = $p['currency'] ? $p['currency'] : currencyEnum::USD;

        $class_user = new userClass();
        $user_info = $class_user->getUserInfo($this->user_id);

        $payment_info = array(
            'branch_id' => $user_info->DATA['branch_id'],
            'teller_id' => $this->user_id,
            'teller_name' => $this->user_name,
            'creator_id' => $this->user_id,
            'creator_name' => $this->user_name,
            'remark' => $remark
        );
        $rt = loan_contractClass::schemaManualRepayment($uid, $repayment_total, $currency, repaymentWayEnum::CASH, Now(), $payment_info);
        if ($rt->STS) {
            return new result(true, 'Repayment successful!');
        } else {
            return new result(false, 'Repayment failure!');
        }
    }

    /**
     * 添加新合同
     */
    public function addContractOp()
    {
        Tpl::output('show_menu', 'loan');
        Tpl::showpage('contract.add.one');
    }

    /**
     * 获取贷款请求信息
     * @param $p
     * @return array
     */
    public function getRequestInfoOp($p)
    {
        $uid = intval($p['search_text']);
        $m_loan_apply = M('loan_apply');
        $info = $m_loan_apply->find(array('uid' => $uid));
        if ($info) {
            return array(
                "sts" => true,
                "data" => $info,
            );
        } else {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
    }

    /**
     * 根据申请创建合同
     * @param $p
     * @return result
     */
    public function createContractOp($p)
    {
        $uid = intval($p['uid']);

        $obj_user = new objectUserClass($this->user_id);
        $rt = $obj_user->createContractByApply($uid);
        if ($rt->STS) {
            $data = $rt->DATA;
            $contract_id = $data['Contract'];
            return new result(true, '', array('url' => getUrl('member', 'showCreateContract', array('uid' => $contract_id), false, ENTRY_COUNTER_SITE_URL)));
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function showMortgageOp($uid)
    {

        Tpl::output('show_menu', 'loan');
        $uid = intval($_GET['uid']);
        $info = $this->getContractInfoOp(array(), $uid);
        $contract_info = $info['data'];

        Tpl::output('contract_info', $contract_info);

        //担保人
        $r = new ormReader();
        $sql = 'SELECT mg.*,cm.display_name,cm.login_code FROM member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.relation_state = 100 AND mg.member_id = ' . $contract_info['member_id'];
        $guarantor_list = $r->getRows($sql);
        Tpl::output('guarantor_list', $guarantor_list);

        //抵押物
        $sql = "SELECT cert.*,cert_image.image_url FROM member_verify_cert AS cert LEFT JOIN member_verify_cert_image AS cert_image ON cert_image.cert_id = cert.uid WHERE cert.verify_state = 10";
        $sql .= " AND cert.member_id = " . $contract_info['member_id'] . " AND cert.cert_type IN(" . certificationTypeEnum::HOUSE . ',' . certificationTypeEnum::LAND . ',' . certificationTypeEnum::CAR . ',' . certificationTypeEnum::MOTORBIKE . ")";
        $sql .= " ORDER BY cert.cert_type ASC";
        $rows = $r->getRows($sql);
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            if (isset($list[$row['uid']])) {
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            } else {
                $list[$row['uid']] = $row;
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            }
        }
        Tpl::output('mortgage_list', $list);


        Tpl::showPage("loan.mortgage");
    }


    /**
     * 展示新创建合同，进行修改
     */
    public function showCreateContractOp()
    {
        Tpl::output('show_menu', 'loan');
        $uid = intval($_GET['uid']);

        $info = $this->getContractInfoOp(array(), $uid);
        $contract_info = $info['data'];

        Tpl::output('contract_info', $contract_info);

        //担保人
        $r = new ormReader();
        $sql = 'SELECT mg.*,cm.display_name,cm.login_code FROM member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.relation_state = 100 AND mg.member_id = ' . $contract_info['member_id'];
        $guarantor_list = $r->getRows($sql);
        Tpl::output('guarantor_list', $guarantor_list);

        //抵押物
        $sql = "SELECT cert.*,cert_image.image_url FROM member_verify_cert AS cert LEFT JOIN member_verify_cert_image AS cert_image ON cert_image.cert_id = cert.uid WHERE cert.verify_state = 10";
        $sql .= " AND cert.member_id = " . $contract_info['member_id'] . " AND cert.cert_type IN(" . certificationTypeEnum::HOUSE . ',' . certificationTypeEnum::LAND . ',' . certificationTypeEnum::CAR . ',' . certificationTypeEnum::MOTORBIKE . ")";
        $sql .= " ORDER BY cert.cert_type ASC";
        $rows = $r->getRows($sql);
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            if (isset($list[$row['uid']])) {
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            } else {
                $list[$row['uid']] = $row;
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            }
        }
        Tpl::output('mortgage_list', $list);

        Tpl::showPage('contract.add.two');

    }

    /**
     * 确认合同
     * @param $p
     * @return result
     */
    public function submitContractOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $uid = intval($_GET['uid']);
        $guarantor_id = $param['guarantor_id'];
        $mortgage_id = $param['mortgage_id'];
        $scan_img = $param['scan_img'];

        $obj_user = new objectUserClass($this->user_id);
        $rt = $obj_user->editContractAndConfirmToExecute($uid, $guarantor_id, $mortgage_id, $scan_img);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $url = getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL);
        showMessage('Submit successfully!', $url);
    }


    public function profileOp()
    {
        $client_id = intval($_GET['client_id']);
        if ($client_id) {
            $r = new ormReader();
            $sql = "select cm.*,mg.grade_code from client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE cm.uid = " . $client_id;
            $client_info = $r->getRow($sql);
            Tpl::output('client_info', $client_info);
        }
        Tpl::showPage("profile");

    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeByUidOp($p)
    {
        $client_id = intval($p['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        $phone_arr = tools::separatePhone($client_info['phone_id']);

        $param = array();
        $param['country_code'] = $phone_arr[0];
        $param['phone'] = $phone_arr[1];
        $rt = $this->sendVerifyCodeOp($param);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE), array('code' => $rt->CODE, 'msg' => $rt->MSG));
        }
    }


    /**
     * 修改登录密码*/
    public function changeLoginPwdOp()
    {
        $uid = $_GET["uid"];
        $r = new ormReader();
        $sql = "select cm.*,mg.grade_code from client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE cm.uid = " . $uid;
        $client_info = $r->getRow($sql);

        Tpl::output('client_info', $client_info);
        Tpl::output('show_menu', 'profile');
        Tpl::showPage("change.login.pwd");

    }


    /**
     * 登录密码*/

    public function verifyChangeLoginPwdOp()
    {
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $new_pwd = trim($_POST['new_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }

            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the password, must enter the verification code or the original password.');
            }
            if (md5($old_pwd) != $row->login_password) {
                $conn->rollback();
                showMessage('Old password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberPassword($member_id, $new_pwd);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change password successfully!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change password failed!');
        }
    }


    /**
     * 修改交易密码*/
    public function changeTradePwdOp()
    {
        $uid = $_GET["uid"];
        $r = new ormReader();
        $sql = "select cm.*,mg.grade_code from client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE cm.uid = " . $uid;
        $client_info = $r->getRow($sql);

        Tpl::output('client_info', $client_info);
        Tpl::output('show_menu', 'profile');
        Tpl::showPage("change.trade.pwd");
    }

    /**
     * 交易密码*/

    public function verifyChangeTradePwdOp()
    {
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $new_pwd = trim($_POST['new_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }
            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the password, must enter the verification code or the original password.');
            }
            if (md5($old_pwd) != $row->trading_password) {
                $conn->rollback();
                showMessage('Old password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberTradePassword($member_id, $new_pwd);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Trade password successfully!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Trade password failed!');
        }
    }


    /**
     * 修改手机号码*/
    public function changePhoneNumOp()
    {
        $uid = $_GET["uid"];
        $r = new ormReader();
        $sql = "select cm.*,mg.grade_code from client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE cm.uid = " . $uid;
        $client_info = $r->getRow($sql);

        Tpl::output('client_info', $client_info);
        Tpl::output('show_menu', 'profile');
        Tpl::showPage("change.phone.num");

    }

    public function verifyChangePhoneNumOp()
    {
        $country_code = trim($_POST['country_code']);
        $phone_number = trim($_POST['new_num']);
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他member注册过
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if ($row) {
            return new result(false, 'The phone number has been registered.');
        }
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);

        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();

        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }
            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the phone number, must enter the verification code or the login password.');
            }
            if (md5($old_pwd) != $row->login_password) {
                $conn->rollback();
                showMessage('login password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberPhoneNum($member_id, $contact_phone);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Phone Number successfully!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Phone Number failed!');
        }
    }


    public function depositOp()
    {
        Tpl::showPage("coming.soon");
    }

    public function withdrawalOp()
    {
        Tpl::showPage("coming.soon");
    }

    public function submitPrepaymentOp($p)
    {
        $contract_id = $p['contract_id'];
        $prepayment_type = $p['prepayment_type'];
        $repay_period = $p["repay_period"];
        $amount = $p["amount"];
        $arr = array(
            'contract_id' => $contract_id,
            'prepayment_type' => $prepayment_type,
            "repay_period" => $repay_period,
            'amount' => $amount
        );
        $rt = loan_contractClass::prepaymentPreview($arr);
        if ($rt->STS) {
            return new result(true, 'Submit successfully');
        } else {
            return new result(false, 'Submit Submit failed');
        }

    }

}