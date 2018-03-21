<?php

class monitorClass {
    static function getMonitorConfig(){
        $rt=array(
            array("auth"=>authEnum::AUTH_CLIENT_CERIFICATION,"fn"=>"getMonitorSQL_verification","title"=>"Verification",
                "url"=>getUrl("client","cerification",array(),"operation", BACK_OFFICE_SITE_URL),"group"=>"client"),
            array("auth"=>authEnum::AUTH_LOAN_CREDIT,"fn"=>"getMonitorSQL_grant_credit","title"=>"Grant Credit",
                "url"=>getUrl("loan","credit",array(),"operation", BACK_OFFICE_SITE_URL),"group"=>"loan"),
            array("auth"=>authEnum::AUTH_LOAN_APPROVAL,"fn"=>"getMonitorSQL_approve_credit","title"=>"Approval Credit",
                "url"=>getUrl("loan","approval",array(),"operation", BACK_OFFICE_SITE_URL),"group"=>"loan"),
            array("auth"=>authEnum::AUTH_LOAN_CONTRACT,"fn"=>"getMonitorSQL_contract","title"=>"New Contracts",
                "url"=>getUrl("loan","contract",array(),"operation", BACK_OFFICE_SITE_URL),"group"=>"loan"),
        );

        return $rt;
    }

    public static function getMonitorItems(){
        $user=userBase::Current();
        $items=self::getMonitorConfig();
        $req=array();
        foreach($items as $k=>$item) {
            if ($user->checkAuth($item['auth'])) {
                $req[$k] = array("key" => $k, "title" => $item['title'], "url" => $item['url'], "group" => $item['group']);
            }
        }
        return $req;
    }

    public function getMonitor($p){
        $last_time=$p['last_time'];

        $user=userBase::Current();
        $items=self::getMonitorConfig();
        $req=array();
        foreach($items as $k=>$item) {
            if ($user->checkAuth($item['auth'])) {
                $req[$k] = array_merge(array("key" => $k), $item);
            }
        }

        if(!count($req)) return array('STS'=>false);
        $data=array();
        foreach($req as $item){
            $fn=$item['fn'];
            $values=$this->$fn();
            if($values && is_string($values)){
                $tmp_rd=new ormReader(ormYo::Conn("db_loan"));
                $values=$tmp_rd->getRows($values);
            }

            if($values && is_array($values)){
                $data[$item['key']]['count']=count($values);
                $data[$item['key']]['new']=0;
                $data[$item['key']]['title']=$item['title'];
                if($last_time){
                    $new_cnt=0;
                    foreach($values as $v){
                        if($v['operate_time']>$last_time){
                            $new_cnt+=1;
                        }
                    }
                    $data[$item['key']]['new']=$new_cnt;
                }
                //$data[$item['key']]['new']=1;
                if(count($values)>0){
                    $last_item=array_pop($values);
                    $data[$item['key']]['content']='<span style="font-weight: bold;">'.$last_item['task_item'].'</span><br/><span>'.$last_item['operate_time'].'</span>';
                }else{
                    $data[$item['key']]['content']='null<br/>&nbsp;';
                }
            }
        }

        return array('STS'=>true,"data"=>$data,"last_time"=>Now());

    }

    public function getMonitorSQL_verification() {
        $sql="select member.display_name task_item, verify.auditor_time operate_time from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where verify.verify_state = 0";
        return $sql;
    }

    public function getMonitorSQL_grant_credit() {
        $sql="SELECT client.display_name task_item, loan.update_time operate_time FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.credit is null or loan.credit = 0";
        return $sql;
    }

    public function getMonitorSQL_approve_credit() {
        $r = new ormReader();
        $sql1 = "select uid from (select * from loan_approval order by uid desc) loan_approval group by obj_guid";
        $ids = $r->getRows($sql1);
        $ids = array_column($ids, 'uid');
        $ids = implode(',', $ids);
        $sql = "SELECT client.display_name task_item, approval.create_time operate_time "
            . " FROM loan_account as loan "
            . " left join client_member as client on loan.obj_guid = client.obj_guid"
            . " inner join loan_approval as approval on client.obj_guid = approval.obj_guid where approval.uid in (" . $ids . ")"
            . " and approval.state = 0 ";
        return $sql;
    }

    public function getMonitorSQL_contract() {
        $sql = "SELECT contract.contract_sn task_item,contract.create_time operate_time FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid where contract.state = 0 and contract.create_time >= date_add(NOW(), INTERVAL -1 DAY)";
        return $sql;
    }
}