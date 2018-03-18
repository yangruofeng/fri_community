<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 13:23
 */
class insuranceContractDetailApiDocument extends apiDocument
{

    public function __construct()
    {
        $this->name = "Insurance Contract Detail";
        $this->description = "保险合同详细";
        $this->url = C("bank_api_url") . "/insurance.contract.detail.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("contract_id", '合同id', 1, true);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $str = '{"STS":true,"MSG":"success","DATA":{"insurance_contract":{"uid":"30","account_id":"32","contract_sn":"2-1000040-001-3","create_time":"2017-12-29 16:23:28","creator_id":"0","creator_name":"System","product_id":"21","product_item_id":"36","start_date":"2017-12-29 16:23:28","end_date":null,"start_insured_amount":"500.00","floating_amount":"0.00","price":"5.00","tax_fee":"0.00","officer_id":"0","officer_name":"","process_id":"0","bind_biz_id":"0","state":"30","loan_contract_id":"209","currency":"USD"},"insurance_product":{"uid":"21","product_code":"CA","product_name":"Car Accident","product_description":"","product_feature":"","product_required":"","product_notice":"","state":"20","create_time":"2017-12-14 14:01:14","creator_id":"1","creator_name":"admin","update_time":"2017-12-14 14:02:59","product_key":""},"insurance_product_item":{"uid":"1","contract_id":"30","scheme_idx":"1","scheme_name":"Period 1","payable_date":"2017-12-29","expire_date":null,"amount":"5.00","actual_payment_amount":"0.00","account_handler_id":"24","state":"0","create_time":"2017-12-29 16:23:28","execute_time":null,"done_time":null},"beneficiary":{"30":{"uid":"30","contract_id":"30","benefit_index":"1","benefit_name":"Huang li","benefit_phone":"+8618902461905","benefit_addr":""}},"insurance_payment_schema":[{"uid":"1","contract_id":"30","scheme_idx":"1","scheme_name":"Period 1","payable_date":"2017-12-29","expire_date":null,"amount":"5.00","actual_payment_amount":"0.00","account_handler_id":"24","state":"0","create_time":"2017-12-29 16:23:28","execute_time":null,"done_time":null}],"loan_contract":{"uid":"209","account_id":"39","contract_sn":"1-1000040-001-2","product_id":"14","product_sub_id":"62","product_special_rate_id":"0","currency":"USD","apply_amount":"1000.00","application_id":"0","propose":"Business","due_date":"29","repayment_period":"monthly","repayment_type":"annuity_scheme","loan_cycle":"1","loan_term_day":"365","loan_period_value":"1","loan_period_unit":"year","mortgage_type":null,"guarantee_type":null,"installment_frequencies":"12","interest_rate":"20.000","interest_rate_type":"0","interest_rate_unit":"yearly","penalty_rate":"4.000","grace_days":"10","is_balloon_payment":"0","is_advance_interest":"0","is_advance_annual_fee":"0","is_first_repayment_annual_fee":"0","is_insured":"1","ref_interest":"20.000","ref_admin_fee":"10.00","ref_operation_fee":"28.05","receivable_principal":"1000.00","receivable_interest":"112.43","receivable_admin_fee":"10.00","receivable_operation_fee":"28.05","receivable_insurance_fee":"15.00","receivable_annual_fee":"0.00","receivable_penalty":"0.00","loss_principal":"0.00","loss_interest":"0.00","loss_admin_fee":"0.00","loss_operation_fee":"0.00","loss_annual_fee":"0.00","loss_penalty":"0.00","invoice_date":null,"start_date":"2017-12-29 16:23:28","end_date":"2018-12-29 16:23:28","creator_id":"0","creator_name":"","create_time":"2017-12-29 16:23:28","process_id":"0","state":"20"}},"CODE":200,"logger":[]}';


        $example = @json_decode($str,true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'insurance_contract' => '保险合同基础信息',
                'insurance_product' => '保险产品主产品信息',
                'insurance_product_item' => '保险产品购买项信息',
                'beneficiary' => '受益人列表',
                'insurance_payment_schema' => '缴费计划',
                'loan_contract' => '关联贷款合同信息',
                '例如' => $example
            )
        );

    }


}