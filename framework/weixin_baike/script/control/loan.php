<?php

class loanControl {
    public function __construct()
    {
    }


    public function disbursementTestOp()
    {
        $r = new ormReader();
        $sql = "select s.* from loan_disbursement_scheme s left join loan_contract c on c.uid=s.contract_id 
          where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' 
          and s.state in ('".schemaStateTypeEnum::CREATE."','".schemaStateTypeEnum::FAILURE."') 
           and s.disbursable_date <= '".date('Y-m-d H:i:s')."' order by s.uid desc ";

        $schema = $r->getRow($sql);

        print_r($schema);

        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );
        $rt = loan_baseClass::loanDisbursementSchemaExecute($schema['uid']);
        if (!$rt->STS) {
            logger::record("exec_disbursement_schema_script", $rt->MSG . "\n" . my_json_encode($schema) );
            $ret['failed'] += 1;
        } else {
            $ret['succeed'] += 1;
        }
        print_r($rt);
    }

    public function exec_disbursement_schemaOp() {

        $r = new ormReader();
        $sql = "select s.* from loan_disbursement_scheme s left join loan_contract c on c.uid=s.contract_id 
          where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' 
          and s.state in ('".schemaStateTypeEnum::CREATE."','".schemaStateTypeEnum::FAILURE."') 
           and s.disbursable_date <= '".date('Y-m-d H:i:s')."' ";

        $tasks = $r->getRows($sql);

        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );
        foreach ($tasks as $schema) {

            $rt = loan_baseClass::loanDisbursementSchemaExecute($schema['uid']);
            if (!$rt->STS) {
                logger::record("exec_disbursement_schema_script", $rt->MSG . "\n" . my_json_encode($schema) );
                $ret['failed'] += 1;
            } else {
                $ret['succeed'] += 1;
            }

        }

        return new result(true, null, $ret);
    }
}