<?php

class clientControl extends baseControl{
    public function __construct(){
        parent::__construct();
        Language::read('certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "User List");
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output("verify_field", $verify_field);
        Tpl::setDir("client");
    }

    public function clientOp(){
      Tpl::showPage("client");
    }

    public function getLoanBalance($uid = 0){
      $r = new ormReader();
      $sql1 = "select contract_id,sum(receivable_principal) as count from loan_installment_scheme where state != ".schemaStateTypeEnum::CREATE." and state != ".schemaStateTypeEnum::COMPLETE." GROUP BY contract_id";
      $rows = $r->getRows($sql1);
      $sum_arr = array();
      foreach ($rows as $key => $value) {
        $sum_arr[$value['contract_id']] = $value['count'];
      }
      $sql2 = "SELECT uid,account_id from loan_contract";
      if($uid){
        $sql2 = "SELECT uid,account_id from loan_contract where account_id = ".$uid;
      }
      $rows2 = $r->getRows($sql2);
      $acc_loan_balance = array();
      foreach ($rows2 as $key => $value) {
        if($acc_loan_balance[$value['account_id']]){
          $acc_loan_balance[$value['account_id']]['sum'] += $sum_arr[$value['uid']];
        }else{
          $acc_loan_balance[$value['account_id']]['sum'] = $sum_arr[$value['uid']];
        }
      }
      return $acc_loan_balance;
    }

    public function getClientListOp($p){
        $r = new ormReader();
        $sql = "SELECT loan.*,client.uid as member_id,client.obj_guid as o_guid,client.display_name,client.alias_name,client.phone_id,client.email,client.create_time FROM client_member as client left join loan_account as loan on loan.obj_guid = client.obj_guid where 1 = 1 ";
        if ($p['member_item']) {
            $sql .= " and loan.obj_guid = " . $p['member_item'];
        }
        if ($p['member_name']) {
            $sql .= ' and client.display_name like "%' . $p['member_name'] . '%"';
        }
        if ($p['ck']) {
            $sql .= ' and client.create_time > "'.date("Y-m-d").'"';
        }
        if ($p['phone']) {
            $sql .= " and client.phone_id = " . $p['phone'];
        }
        $sql .= " ORDER BY client.create_time desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("client.list");
    }

    public function clientDetailOp(){
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $m_client_black = M('client_black');
        $contract_info = array();
        $member_id = intval($p['uid']);
        if( !$member_id ){
            showMessage('Client Error');
        }

            $sql = "SELECT client.*,loan.uid as loan_uid FROM client_member as client left join loan_account as loan on loan.obj_guid = client.obj_guid where client.uid = " . $p['uid'];
            $data = $r->getRow($sql);

            if(!$data){
              showMessage('Client Error','','html','error');
            }

            $loan_uid = $data['loan_uid'] ? : 0;
            $sql2 = "SELECT contract.*,product.product_code,product.product_name,product.product_description FROM loan_contract as contract left join loan_product as product on contract.product_id = product.uid where contract.account_id = ".$loan_uid." order by contract.uid desc";
            $contracts = $r->getRows($sql2);
            $sql2 = "SELECT contract.* FROM insurance_contract as contract left join insurance_account as account on contract.account_id = account.uid where account.obj_guid = ".$data['obj_guid']." order by contract.uid desc";
            $insurance_contracts = $r->getRows($sql2);
            //*这是request loan的次数
            $sql = "SELECT count(uid) as count from loan_apply where member_id = ".$p['uid'];
            $count = $r->getRow($sql);
            $loan_count = $data['count'];
            $contract_info['all_enquiries'] = $loan_count;
            //*这是第一次发放贷款的日期
            $sql = "select c.uid,d.create_time from loan_contract c LEFT JOIN loan_disbursement d on c.uid = d.contract_id where c.account_id = ".$loan_uid." ORDER BY d.create_time desc limit 1";
            $count = $r->getRow($sql);
            $create_time = $data['create_time'];
            $contract_info['earliest_loan_issue_date'] = $create_time;
            $loan_summary = memberClass::getMemberLoanSummary($p['uid'], 1);
            $guarantee_loan_summary = memberClass::getMemberLoanSummary($p['uid'], 2);

        Tpl::output("contract_info", $contract_info);
        Tpl::output("loan_summary", $loan_summary->DATA);
        Tpl::output("guarantee_loan_summary", $guarantee_loan_summary->DATA);



        $cert_type_lang = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('cert_type_lang',$cert_type_lang);

        $re = memberClass::getMemberSimpleCertResult($member_id);
        if (!$re->STS) {
            showMessage('Error: ' . $re->MSG);
        }
        $verifys = $re->DATA;

        $credit_info = memberClass::getCreditBalance(intval($p['uid']));
        Tpl::output("detail", $data);
        Tpl::output('credit_info',$credit_info);
        Tpl::output("verifys", $verifys);
        Tpl::output("contracts", $contracts);
        Tpl::output("insurance_contracts", $insurance_contracts);

        $sql = "select uid, type, list from client_black";
        $types = $r->getRows($sql);
        foreach ($types as $key => $value) {
          $members = $value['list'];
          $members = $members ? explode(',', $members) : array();
          $types[$key]['check'] = false;
          if(in_array($p['uid'], $members)){
            $types[$key]['check'] = true;
          }
          unset($types[$key]['list']);
        }
        Tpl::output("black", $types);

        if($data['id_address1']){
          $arr = array($data['id_address1'],$data['id_address2'],$data['id_address3'],$data['id_address4']);
          $adds = implode(',',$arr)?:0;
          $sql = "select uid,node_text,node_text_alias from core_tree where uid in(".$adds.")";
          $address = $r->getRows($sql);
          $addr = array();
          foreach ($address as $key => $value) {
            $addr[$value['uid']] = $value;
          }
          Tpl::output("addr", $addr);
        }
        Tpl::showPage("client.detail");
    }

    public function editClientBlackFieldOp($p){
      $p = array_merge(array(), $_GET, $_POST);
      $m_client_black = M('client_black');
      $data = $m_client_black->getBlackInfo($p['obj_guid']);
      if($data->STS){
        $param =  json_decode($data->DATA['type'], true);
        foreach ($param as $key => $value) {
          if($key == $p['filed']){
            $param[$key] = $p['state'];
          }
        }
        $param['obj_guid'] = $p['obj_guid'];
        $param['auditor_id'] = $this->user_id;
        $param['auditor_name'] = $this->user_name;
        $rt = $m_client_black->updateBlack($param);
      }else{
        $param =  array('t1'=> 0, 't2'=> 0, 't3'=> 0, 't4' => 0, 't5' => 0);
        foreach ($param as $key => $value) {
          if($key == $p['filed']){
            $param[$key] = $p['state'];
          }
        }
        $param['obj_guid'] = $p['obj_guid'];
        $param['auditor_id'] = $this->user_id;
        $param['auditor_name'] = $this->user_name;
        $rt = $m_client_black->insertBlack($param);
      }
      if ($rt->STS) {
        return new result(true, 'Edit Success!');
      } else {
        return new result(false, 'Invalid Member!');
      }
    }

    public function cerificationOp(){
        Tpl::showPage("cerification");
    }

    public function getCerificationListOp($p){
        $r = new ormReader();
        /*$sql = "select max(uid) as uid,member_id,cert_type from member_verify_cert group by member_id,cert_type";
        $ids = $r->getRows($sql);
        // 处理无数据的bug
        if( count($ids) < 1 ){
            $ids = array(0);
        }
        $ids = array_column($ids, 'uid');
        $ids =  implode(',', $ids)?:0;
        $sql1 = "select verify.*,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid WHERE verify.uid in (".$ids.") ";
        */

        // 不分组了，家庭关系有多条
        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where 1=1  ";

        if ($p['cert_type'] != 0) {
            $sql1 .= " and verify.cert_type = '".$p['cert_type']."' " ;
        }
        if ($p['verify_state'] == 1) {
            $sql1 .= " and (verify.verify_state = 0 or verify.verify_state = -1 ) ";
        }
        if ($p['verify_state'] == 10) {
            $sql1 .= " and verify.verify_state = " . $p['verify_state'];
        }
        if ($p['verify_state'] == 100) {
            $sql1 .= " and verify.verify_state = " . $p['verify_state'];
        }
        if ($p['member_name']) {
            $name = ' (member.login_code like "%' . $p['member_name'] . '%" )';
            $sql1 .= " and " . $name;
        }
        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;
        $list = array();
        // 取图片
        foreach( $rows as $row ){
            $sql = "select * from member_verify_cert_image where cert_id='".$row['uid']."'";
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
            "cur_uid" => $this->user_id,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("cerification.list");
    }

    public function cerificationDetailOp(){

        $sample_images = global_settingClass::getCertSampleImage();
        Tpl::output('cert_sample_images',$sample_images);

      $r = new ormReader();
      $p = array_merge(array(), $_GET, $_POST);
      $m_member_verify_cert = M('member_verify_cert');
      $row = $m_member_verify_cert->getRow(array('uid' => $p['uid']));
      $data = $row->toArray();

        $ID = $m_member_verify_cert->getRow(array('member_id' => $data['member_id'], 'cert_type' => certificationTypeEnum::ID, 'verify_state' => 10));
        if( $data['cert_type'] == certificationTypeEnum::FAIMILYBOOK && !$ID ){
            showMessage('Please verify the identity card information', getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
        }
        $sql = "select * from member_verify_cert where uid != ".$data['uid']." and member_id = ".$data['member_id']." and cert_type = ".$data['cert_type']." order by uid desc";
        $history = $r->getRows($sql);
        // image
        foreach( $history as $k=>$v ){
            $sql = "select * from member_verify_cert_image where cert_id='".$v['uid']."'";
            $images = $r->getRows($sql);
            $v['cert_images'] = $images;
            $history[$k] = $v;
        }

        if ($row['verify_state'] == -1) {

            // 用row，可及时更新状态
            // 超时放开，让别人可审 1小时
            if( ( strtotime($row['auditor_time']) + 3600 ) < time() ){
                $row ->verify_state = -1;
                $row->auditor_id = $this->user_id;
                $row->auditor_name = $this->user_name;
                $row->auditor_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }
            }



        } elseif ($row['verify_state'] == 0) {
            $row ->verify_state = -1;
            $row->auditor_id = $this->user_id;
            $row->auditor_name = $this->user_name;
            $row->auditor_time = Now();
            $up = $row->update();
            if (!$up->STS) {
                showMessage($up->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
            }
        }
        $sql = "select verify.*,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where verify.uid = " . $p['uid'];
        $info = $r->getRow($sql);

        // image
        $sql = "select * from member_verify_cert_image where cert_id='".$info['uid']."'";
        $images = $r->getRows($sql);
        $info['cert_images'] = $images;

        Tpl::output('info', $info);
        if($ID){
            Tpl::output('IDInfo', $ID->toArray());
        }
        Tpl::output('history', $history);
        $lock = false;
        if ( $row['verify_state'] &&  $this->user_id != $row['auditor_id']) {
            //审核中
            $lock = true;
        }

        Tpl::output('lock', $lock);
      $cert_type = $row->cert_type;
      switch( $cert_type ){
          case certificationTypeEnum::MOTORBIKE:
          case certificationTypeEnum::RESIDENT_BOOK :
          case certificationTypeEnum::ID :
          case certificationTypeEnum::PASSPORT :
          case certificationTypeEnum::FAIMILYBOOK :
          case certificationTypeEnum::HOUSE :
          case certificationTypeEnum::CAR :
          case certificationTypeEnum::LAND :
              Tpl::showPage("cerification.detail");
              break;
          case certificationTypeEnum::WORK_CERTIFICATION :
              $m_work = new member_workModel();
              $extend_info = $m_work->getRow(array(
                  'cert_id' => $row->uid
              ));
              Tpl::output('extend_info',$extend_info);
              Tpl::showPage('certification.work.detail');
              break;

          default:
              showMessage('Not supported type', getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));

      }



    }

    public function cerifycationConfirmOp(){
      $p = array_merge(array(), $_GET, $_POST);
      $obj_validate = new Validate();
      $obj_validate->deliverparam = $p;
      $error = $obj_validate->validate();
      if ($error != ''){
        showMessage($error,'','html','error');
      }

      $m_member_verify_cert = M('member_verify_cert');
      $row = $m_member_verify_cert->getRow(array('uid' => $p['uid']));
      if (!$row->toArray()) {
          return new result(false, 'Invalid Id!');
      }
      $data = $row->toArray();

      $cert_type = $row->cert_type;

        // 审核过的不会再审核
        if( $row->verify_state == certStateEnum::PASS || $row->verify_state == certStateEnum::NOT_PASS ){
            Tpl::showPage("cerification");
        }

        switch( $cert_type ){
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
            case certificationTypeEnum::MOTORBIKE:

                // 资产的审核

                // 更新状态
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                $p['insert'] = false;
                $ret = $m_member_verify_cert->updateState($p);
                if( !$ret->STS ){
                    showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }

                if( $p['verify_state'] == certStateEnum::PASS ){
                    $asset_state = assetStateEnum::CERTIFIED;
                }else{
                    $asset_state = assetStateEnum::INVALID;
                }
                // 更新资产认证状态
                $m_asset = new member_assetsModel();
                $asset = $m_asset->getRow(array(
                    'cert_id' => $row['uid']
                ));
                if( $asset ){
                    $asset->asset_state = $asset_state;
                    $asset->update_time = Now();
                    $up = $asset->update();
                    if( !$up->STS ){
                        showMessage('Update asset state fail',getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                    }
                }
                Tpl::showPage("cerification");
                break;
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::FAIMILYBOOK :

                //更新原来通过的为过期状态
                /*$sql = "update member_verify_cert set verify_state='".certStateEnum::EXPIRED."' where member_id='".$row['member_id']."'
                 and cert_type='".$row['cert_type']."' and verify_state='".certStateEnum::PASS."'  ";
                $up = $m_member_verify_cert->conn->execute($sql);
                if( !$up->STS ){
                    showMessage('Update history cert fail',getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }*/

                // 更新状态
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;

                if( $cert_type == certificationTypeEnum::ID ){
                    $p['insert'] = true;
                    $ret = $m_member_verify_cert->updateState($p);
                    if( !$ret->STS ){
                        showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                    }
                    if( $p['verify_state'] == certStateEnum::PASS ){
                        // 修改会员表身份证信息
                        $m_member = new memberModel();
                        $member = $m_member->getRow($row->member_id);
                        if( !$member ){
                            showMessage('Error member',getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                        }
                        $id_en_name_json = json_encode(array('family_name'=>$p['en_family_name'],'given_name'=>$p['en_given_name']));
                        $id_kh_name_json = json_encode(array('family_name'=>$p['kh_family_name'],'given_name'=>$p['kh_given_name']));
                        $member->initials = strtoupper(substr($p['en_family_name'],0,1));
                        $member->display_name = $p['en_family_name'].' '.$p['en_given_name'];
                        $member->kh_display_name = $p['kh_family_name'].' '.$p['kh_given_name'];
                        $member->id_sn = $row['cert_sn'];
                        $member->id_type = $p['id_type'];
                        $member->nationality = $p['nationality'];
                        $member->id_en_name_json = $id_en_name_json;
                        $member->id_kh_name_json = $id_kh_name_json;
                        $member->id_address1 = $p['id_address1'];
                        $member->id_address2 = $p['id_address2'];
                        $member->id_address3 = $p['id_address3'];
                        $member->id_address4 = $p['id_address4'];
                        $member->id_expire_time = $p['cert_expire_time'];
                        $up = $member->update();
                        if( !$up->STS ){
                            showMessage('Update member ID sn fail',getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                        }
                    }

                }else{
                    $p['insert'] = false;
                    $ret = $m_member_verify_cert->updateState($p);
                    if( !$ret->STS ){
                        showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                    }
                }

                Tpl::showPage("cerification");
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :

                $p['insert'] = false;
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                $ret = $m_member_verify_cert->updateState($p);
                if (!$ret->STS) {
                    showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }

                //更新原来通过的为过期状态
                /*$sql = "update member_verify_cert set verify_state='".certStateEnum::EXPIRED."' where member_id='".$row['member_id']."'
                 and cert_type='".$row['cert_type']."' and verify_state='".certStateEnum::PASS."'  ";
                $up = $m_member_verify_cert->conn->execute($sql);
                if( !$up->STS ){
                    showMessage('Update history cert fail',getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }*/

                $m_work = new member_workModel();
                $extend_info = $m_work->getRow(array(
                    'cert_id' => $row->uid
                ));
                // 更新扩展信息
                // 当前只能有一条合法的，其余的更新为历史
                /*$sql = "update member_work set state='".workStateStateEnum::HISTORY."' where member_id='".$extend_info->member_id."' and state='".workStateStateEnum::VALID."' ";
                $up = $m_work->conn->execute($sql);
                if (!$up->STS) {
                    showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }*/

                if( $p['verify_state'] == certStateEnum::PASS ){
                    $work_state = workStateStateEnum::VALID;
                }else{
                    $work_state = workStateStateEnum::INVALID;
                }
                if( $extend_info ){
                    $extend_info->state = $work_state;
                    $up = $extend_info->update();
                    if (!$up->STS) {
                        showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                    }
                }

                // 如果通过
                if( $p['verify_state'] == certStateEnum::PASS ){
                    // 如果是政府员工，更新member表
                    if( $extend_info->is_government ){
                        $m_member = new memberModel();
                        $member = $m_member->getRow($extend_info->member_id);
                        if( $member ){
                            $member->is_government = 1;
                            $up = $member->update();
                            if (!$up->STS) {
                                showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                            }
                        }
                    }
                }


                Tpl::showPage("cerification");
                break;

            /*case certificationTypeEnum::FAMILY_RELATIONSHIP :

                $p['insert'] = false;
                $p['auditor_id'] = $this->user_id;
                $p['auditor_name'] = $this->user_name;
                $ret = $m_member_verify_cert->updateState($p);
                if (!$ret->STS) {
                    showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                }

                // 更新扩展表信息
                if( $p['verify_state'] == certStateEnum::PASS ){
                    $state = memberFamilyStateEnum::APPROVAL;
                }else{
                    $state = memberFamilyStateEnum::INVALID;
                }
                $m_family = new member_familyModel();
                $extend_info = $m_family->getRow(array(
                    'cert_id' => $row->uid
                ));
                if( $extend_info ){
                    $extend_info->relation_cert_sn = $p['relation_cert_sn'];
                    $extend_info->relation_state = $state;
                    $up = $extend_info->update();
                    if (!$up->STS) {
                        showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
                    }
                }

                Tpl::showPage("cerification");
                break;*/
            default:
                showMessage('Not supported type', getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));

        }


    }

    public function cerifycationCancelOp(){
        $p = array_merge(array(), $_GET, $_POST);
        $m_member_verify_cert = M('member_verify_cert');
        $p['verify_state'] = 0;
        $p['auditor_id'] = 0;
        $p['auditor_name'] = '';
        $ret = $m_member_verify_cert->updateState($p);
        if (!$ret->STS) {
            showMessage($ret->MSG, getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            Tpl::showPage("cerification");
        }

    }

    public function blackListOp(){
      $r = new ormReader();
      $sql = "select * from client_black";
      $types = $r->getRows($sql);
      foreach ($types as $key => $value) {
        $types[$key]['child'] = '';
        if($value['list']){
          $types[$key]['count'] = count(explode(',',$value['list']));
        }
      }
      Tpl::output('types', $types);
      Tpl::showPage("black");
    }

    public function getBlackClientListOp($p){
      $r = new ormReader();
      $sql = "select uid,display_name from client_member";
      $list = $r->getRows($sql);
      $sql1 = "select * from client_black where type = ".$p['type'];
      $black = $r->getRow($sql1);
      $members = $black['list'];
      $members = $members ? explode(',', $members) : array();
      foreach ($list as $key => $value) {
        $list[$key]['check'] = false;
        if(in_array($value['uid'], $members)){
          $list[$key]['check'] = true;
        }
      }
      return new result(true, '', $list);
    }

    public function getBlackListOp($p){
      $r = new ormReader();
      $sql = "select member.*,black.type as black from client_member as member left join client_black as black on member.obj_guid = black.obj_guid";
      $pageNumber = intval($p['pageNumber']) ?: 1;
      $pageSize = intval($p['pageSize']) ?: 20;
      $data = $r->getPage($sql, $pageNumber, $pageSize);
      $rows = $data->rows;
      $total = $data->count;
      $pageTotal = $data->pageCount;
      $sql1 = "select * from client_black";

      return array(
          "sts" => true,
          "data" => $rows,
          "total" => $total,
          "pageNumber" => $pageNumber,
          "pageTotal" => $pageTotal,
          "pageSize" => $pageSize,
      );
      Tpl::showPage("black.list");
    }

    public function addBlackClientOp(){
      $p = array_merge(array(), $_GET, $_POST);
      Tpl::output('type', $p['type']);
      Tpl::showPage("black_add");
    }
    public function getAddBlackClientOp($p){
      $r = new ormReader();
      $sql = "select * from client_black where type = ".$p['type'];
      $item = $r->getRow($sql);
      $ids = $item['list']?:0;
      $sql1 = "select uid,obj_guid,display_name,member_icon from client_member WHERE uid not in (".$ids.") ";
      if ($p['obj_guid']) {
          $sql1 .= " and obj_guid = " . $p['obj_guid'];
      }
      if ($p['member_name']) {
          $sql1 .= ' and display_name like "%' . $p['member_name'] . '%"';
      }
      $pageNumber = intval($p['pageNumber']) ?: 1;
      $pageSize = intval($p['pageSize']) ?: 15;
      $data = $r->getPage($sql1, $pageNumber, $pageSize);
      $rows = $data->rows;
      $total = $data->count;
      $pageTotal = $data->pageCount;

      return array(
          "sts" => true,
          "data" => $rows,
          "total" => $total,
          "pageNumber" => $pageNumber,
          "pageTotal" => $pageTotal,
          "pageSize" => $pageSize,
      );
      Tpl::showPage("black_add.list");
    }

    public function removeBlackClientOp(){
      $p = array_merge(array(), $_GET, $_POST);
      Tpl::output('type', $p['type']);
      Tpl::showPage("black_remove");
    }
    public function getRemoveBlackClientOp($p){
      $r = new ormReader();
      $sql = "select * from client_black where type = ".$p['type'];
      $item = $r->getRow($sql);
      $ids = $item['list']?:0;
      $sql1 = "select uid,obj_guid,display_name,member_icon from client_member WHERE uid in (".$ids.") ";
      if ($p['obj_guid']) {
          $sql1 .= " and obj_guid = " . $p['obj_guid'];
      }
      if ($p['member_name']) {
          $sql1 .= ' and display_name like "%' . $p['member_name'] . '%"';
      }
      $pageNumber = intval($p['pageNumber']) ?: 1;
      $pageSize = intval($p['pageSize']) ?: 15;
      $data = $r->getPage($sql1, $pageNumber, $pageSize);
      $rows = $data->rows;
      $total = $data->count;
      $pageTotal = $data->pageCount;

      return array(
          "sts" => true,
          "data" => $rows,
          "total" => $total,
          "pageNumber" => $pageNumber,
          "pageTotal" => $pageTotal,
          "pageSize" => $pageSize,
      );
      Tpl::showPage("black_remove.list");
    }

    public function updateBlackClientListOp($p){
      $m_client_black = M('client_black');
      $p['auditor_id'] = $this->user_id;
      $p['auditor_name'] = $this->user_name;
      $rt = $m_client_black->updateBlack($p);
      if ($rt->STS) {
        return new result(true);
      } else {
        return new result(false);
      }
    }

    public function updateBlackClientTypeOp($p){
      $m_client_black = M('client_black');
      $row = $m_client_black->getRow(array('type' => $p['type']));
      $members = $row?$row->toArray()['list']:'';
      $members = $members ? explode(',', $members) : array();
      $index = array_search($p['uid'], $members);
      if($p['state']){//添加到黑名单
        if($index === false){
          array_push($members, $p['uid']);
        }else{
          return new result(true);
        }
      }else{//移出黑名单
        if($index !== false){
          unset($members[$index]);
        }else{
          return new result(true);
        }
      }
      $list = implode(',', $members);
      $p['list'] = $list;
      $p['auditor_id'] = $this->user_id;
      $p['auditor_name'] = $this->user_name;
      $rt = $m_client_black->updateBlack($p);
      if ($rt->STS) {
        return new result(true);
      } else {
        return new result(false);
      }
    }

    public function editBlackOp(){
      $r = new ormReader();
      $p = array_merge(array(), $_GET, $_POST);
      $m_client_member = M('client_member');
      $m_client_black = M('client_black');
      if ($p['form_submit'] == 'ok') {
        unset($p['form_submit']);
        unset($p['op']);
        unset($p['act']);
        $data = $m_client_black->getBlackInfo($p['obj_guid']);
        $p['auditor_id'] = $this->user_id;
        $p['auditor_name'] = $this->user_name;
        if($data->STS){
          $rt = $m_client_black->updateBlack($p);
        }else{
          $rt = $m_client_black->insertBlack($p);
        }
        if ($rt->STS) {
          showMessage($rt->MSG, getUrl('client', 'blackList', array(), false, BACK_OFFICE_SITE_URL));
        } else {
          showMessage($rt->MSG, getUrl('client', 'editBlack', $p, false, BACK_OFFICE_SITE_URL));
        }
      }else{
        $row = $m_client_member->getRow(array('uid' => $p['uid']));
        $data = $row->toArray();
        if (!$data) {
            showMessage('Client Not Exist', getUrl('loan', 'credit', array(), false, BACK_OFFICE_SITE_URL));
        }
        $sql = "SELECT loan.*,client.display_name,client.alias_name,client.phone_id,client.email FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.obj_guid = '" . $data['obj_guid'] . "'";
        $info = $r->getRow($sql);
        Tpl::output('info', $info);
        $blacks = $m_client_black->getBlackInfo($data['obj_guid']);
        $blacks = $blacks->DATA;
        $black = json_decode($blacks['type'], true);
        Tpl::output('black', $black);
        Tpl::showPage("black.edit");
      }



    }

    public function creditReportOp(){
      $r = new ormReader();
      $p = array_merge(array(), $_GET, $_POST);
      if ($p['obj_guid']) {
          $sql = "SELECT client.*,loan.uid as loan_uid,loan.credit FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where client.obj_guid = " . $p['obj_guid'];
          $data = $r->getRow($sql);
          $sql1 = "SELECT * FROM member_verify_cert where member_id = " . $data['uid'];
          $rows = $r->getRows($sql1);
          $sql2 = "SELECT * FROM loan_approval where obj_guid = " . $data['obj_guid'] . " ORDER BY  uid desc";
          $credit_list = $r->getRows($sql2);
          $sql3 = "SELECT repayment.uid,repayment.state FROM loan_contract as contract right JOIN loan_repayment as repayment on contract.uid = repayment.contract_id"
                  ." where contract.account_id = ".$data['loan_uid']." and repayment.state = 100";
          $remayment_list = $r->getRows($sql3);
          $sql4 = "SELECT scheme.uid FROM loan_contract as contract left JOIN loan_installment_scheme as scheme on contract.uid = scheme.contract_id"
                  ." where contract.account_id = ".$data['loan_uid']." and contract.state >= 20 and scheme.state != 100 and '".date("Y-m-d H:m:s")."' > scheme.penalty_start_date";
          $breach_list = $r->getRows($sql4);
      }
      $verifys = array();
      foreach ($rows as $key => $value) {
        $verifys[$value['cert_type']] = $value;
      }
      Tpl::output("detail", $data);
      Tpl::output("verifys_list", $rows);
      Tpl::output("verifys", $verifys);
      Tpl::output('credit_list', $credit_list);
      Tpl::output('remayment_count', count($remayment_list));
      Tpl::output('default_count', count($breach_list));
      Tpl::showPage("client.report");
    }

    public function gradeOp(){
      $r = new ormReader();
      $sql = "select * from member_grade";
      $rows = $r->getRows($sql);
      Tpl::output('list', $rows);
      Tpl::showPage("grade");
    }

    public function addGradeOp(){
      $p = array_merge(array(), $_GET, $_POST);
      if ($p['form_submit'] == 'ok') {
        $m_member_grade = M('member_grade');
        unset($p['form_submit']);
        unset($p['op']);
        unset($p['act']);
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $rt = $m_member_grade->insertGrade($p);
        if ($rt->STS) {
          showMessage($rt->MSG, getUrl('client', 'grade', array(), false, BACK_OFFICE_SITE_URL));
        } else {
          showMessage($rt->MSG, getUrl('client', 'grade', $p, false, BACK_OFFICE_SITE_URL));
        }
      }else{
        Tpl::showPage("grade.add");
      }

    }

}
