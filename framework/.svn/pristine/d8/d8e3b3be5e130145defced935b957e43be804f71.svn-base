<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 10:47
 */
class objectUserClass extends objectBaseClass
{

    public $user_id;
    public $user_name;
    public $branch_id;
    public $department_id;
    public $position;



    public function __construct($user_id)
    {

        $this->_initObject($user_id);
    }

    protected function _initObject($user_id)
    {
        $m = new um_userModel();
        $user = $m->getRow($user_id);
        if( !$user ){
            throw new Exception('User not found');
        }

        $this->object_id = $user->obj_guid;
        $this->object_type = objGuidTypeEnum::UM_USER;
        $this->object_info = $user;
        $this->user_id = $user->uid;
        $this->user_name = $user->user_name;
        $this->position = my_json_decode($user->user_position);

        $m_depart = new site_departModel();
        $depart = $m_depart->getRow($user->depart_id);

        $this->department_id = $depart?$depart->uid:0;
        $this->branch_id = $depart?$depart->branch_id:0;

    }

    public function checkValid()
    {
        // TODO: Implement checkValid() method.
        return new result(true);
    }


    /** 通过申请创建贷款申请合同
     * @param $apply_id
     * @return bool|ormResult|result
     */
    public function createContractByApply($apply_id)
    {
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();
            $re = (new loan_baseClass())->createContractByApply($apply_id,$this->user_id,$this->user_name);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }
            $conn->submitTransaction();
            return new result(true,'success',array(
                'contract_id'
            ));

        }catch( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    /**
     * @param $contract_id
     * @param array $guarantor_list [1,2,3,5]
     * @param array $mortgage_list [2,5,6,5]
     * @param array $files_list  ['a.png','b.png']
     * @return result
     */
    public function editContractAndConfirmToExecute($contract_id,$guarantor_list=array(),$mortgage_list=array(),$files_list=array())
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }
        $conn = ormYo::Conn();
        try{
            $conn->startTransaction();

            // 担保人
            if( !empty($guarantor_list) ){
                $re = loan_contractClass::contractAddGuarantor($contract_id,$guarantor_list);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }
            }

            // 抵押物
            if( !empty($mortgage_list) ){
                $re = loan_contractClass::contractAddMortgage($contract_id,$mortgage_list);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }
            }

            // 合同扫描文件
            if( !empty($files_list) ){
                $re = loan_contractClass::contractAddFiles($contract_id,$files_list);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }
            }

            // 合同进入执行状态
            $re = loan_contractClass::contractConfirmToExecute($contract_id);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }

            $conn->submitTransaction();
            return new result(true,'success');



        }catch ( Exception $e ){
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }





}