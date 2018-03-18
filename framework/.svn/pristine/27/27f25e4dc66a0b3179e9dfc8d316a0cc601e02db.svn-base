<style>
    .rightbox,.prepayment_amount,.prepayment_periods,#prepayment_type,#pay_type{
        text-align: right;
        padding-right: 10px;

    }
    .redstar{
        color: red;
    }

    .title{
        font-size: 15px;
        font-weight: bold;
    }
    tr{
        border-bottom:1px solid gainsboro;
        margin-bottom: 3px;
    }
    #prepayment_type,#pay_type,.prepayment_amount,.prepayment_periods{
        border: none;
    }


</style>

<?php $info = $data['data']; ?>


<?php if($info){?>
    <table class="table contract-table">
        <tbody class="table-body">
        <tr>
            <td><label class="control-label">Client-ID</label></td>
            <td><?php echo $info['member_id'] ?></td>
            <td colspan="2" class="contract-btn">
                <button class="btn btn-default" onclick="prepayment_btn()">Prepayment</button>
                <button class="btn btn-default" onclick="repayment_btn()">Repayment</button>
                <a class="btn btn-default" href="<?php echo getUrl('member', 'showMortgage',array('uid'=>$info['uid']), false, ENTRY_COUNTER_SITE_URL);?>">Mortgage</a>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Client-Name</label></td>
            <td><?php echo $info['display_name'] ?></td>
            <td><label class="control-label">Disbursement</label></td>
            <td style="position: relative">
                <em style="padding-left: 0px"><?php echo count($info['disbursement']) . ' Periods' ?></em>
                <div class="loan-exp-wrap">
                    <div class="pos">
                        <em class="triangle-up"></em>
                        <table class="loan-exp-table">
                            <tr class="t">
                                <td>Amount</td>
                                <td></td>
                                <td>Principal</td>
                                <td></td>
                                <td>Annual Fee</td>
                                <td></td>
                                <td>Interest</td>
                                <td></td>
                                <td>Admin Fee</td>
                                <td></td>
                                <td>Operation Fee</td>
                                <td></td>
                                <td>Insurance Fee</td>
                                <td></td>
                                <td>Execute Time</td>
                            </tr>

                            <?php foreach ($info['disbursement'] as $key => $value) { ?>
                                <tr class="a">
                                    <td class="y"><?php echo $value['amount']; ?></td>
                                    <td>&nbsp;=&nbsp;</td>
                                    <td><?php echo $value['principal']; ?></td>
                                    <td>&nbsp;-&nbsp;</td>
                                    <td><?php echo $value['deduct_annual_fee']; ?></td>
                                    <td>&nbsp;-&nbsp;</td>
                                    <td><?php echo $value['deduct_interest']; ?></td>
                                    <td>&nbsp;-&nbsp;</td>
                                    <td><?php echo $value['deduct_admin_fee']; ?></td>
                                    <td>&nbsp;-&nbsp;</td>
                                    <td><?php echo $value['deduct_operation_fee']; ?></td>
                                    <td>&nbsp;-&nbsp;</td>
                                    <td><?php echo round($value['deduct_insurance_fee'], 2); ?></td>
                                    <td>&nbsp;&nbsp;</td>
                                    <td><?php echo timeFormat($value['execute_time']); ?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Client-Phone</label></td>
            <td><?php echo $info['phone_id'] ?></td>
            <td><label class="control-label">Installment</label></td>
            <td style="position:relative;">
                <em style="padding-left: 0px"><?php echo count($info['installment']) . ' Periods' ?></em>
                <div class="loan-exp-wrap">
                    <div class="pos">
                        <em class="triangle-up"></em>
                        <table class="loan-exp-table">
                            <tr class="t">
                                <td>Amount</td>
                                <td></td>
                                <td>Principal</td>
                                <td></td>
                                <td>Interest</td>
                                <td></td>
                                <td>Admin Fee</td>
                                <td></td>
                                <td>Operation Fee</td>
                                <td></td>
                                <td>Penalties</td>
                                <td></td>
                                <td>Repayment Time</td>
                            </tr>

                            <?php foreach ($info['installment'] as $key => $value) { ?>
                                <tr class="a">
                                    <td class="y"><?php echo $value['amount']; ?></td>
                                    <td>&nbsp;=&nbsp;</td>
                                    <td><?php echo $value['receivable_principal']; ?></td>
                                    <td>&nbsp;+&nbsp;</td>
                                    <td><?php echo $value['receivable_interest']; ?></td>
                                    <td>&nbsp;+&nbsp;</td>
                                    <td><?php echo $value['receivable_admin_fee']; ?></td>
                                    <td>&nbsp;+&nbsp;</td>
                                    <td><?php echo $value['receivable_operation_fee']; ?></td>
                                    <td>&nbsp;+&nbsp;</td>
                                    <td><?php echo round($value['penalties'], 2) ?></td>
                                    <td>&nbsp;&nbsp;</td>
                                    <td><?php echo timeFormat($value['receivable_date']); ?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Contract No.</label></td>
            <td colspan="3"><?php echo $info['contract_sn'] ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Status</label></td>
            <td colspan="3">
                <?php switch ($info['state']) {
                    case loanContractStateEnum::CREATE :
                        $label = 'Create';
                        break;
                    case loanContractStateEnum::PENDING_APPROVAL :
                        $label = 'Pending Approval';
                        break;
                    case loanContractStateEnum::PENDING_DISBURSE :
                        $label = 'Pending Disburse';
                        break;
                    case loanContractStateEnum::PROCESSING :
                        $label = 'Ongoing';
                        break;
                    case loanContractStateEnum::PAUSE :
                        $label = 'Pause';
                        break;
                    case loanContractStateEnum::COMPLETE :
                        $label = 'Complete';
                        break;
                    case loanContractStateEnum::WRITE_OFF :
                        $label = 'Write Off';
                        break;
                    default:
                        $label = 'Write Off';
                        break;
                }
                echo $label;
                ?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Product Name</label></td>
            <td colspan="3"><?php echo $info['product_name'] ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Loan Limit</label></td>
            <td colspan="3" class="money-style"><?php echo ncAmountFormat($info['apply_amount']) ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Outstanding Balance</label></td>
            <td colspan="3" class="money-style"><?php echo '$1000.00' ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Period</label></td>
            <td colspan="3"><?php echo $info['loan_period_value'] . ' ' . $info['loan_period_unit'] ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Repayment Type</label></td>
            <td colspan="3"><?php echo ucwords(str_replace('_', ' ', $info['repayment_type'])) ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Interest Rate</label></td>
            <td colspan="3"><?php echo ($info['interest_rate_type'] == 1 ? "$" . $info['interest_rate'] : $info['interest_rate'] . '%') . ' Per ' . $info['interest_rate_unit']  ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Operation Fee</label></td>
            <td colspan="3"><?php echo ($info['operation_fee_type'] == 1 ? "$" . $info['operation_fee'] : $info['operation_fee'] . '%') . ' Per ' . $info['operation_fee_unit']  ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Admin Fee</label></td>
            <td colspan="3"><?php echo $info['admin_fee_type'] == 1 ? "$" . $info['admin_fee'] : $info['admin_fee'] . '%' ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Loan Fee</label></td>
            <td colspan="3"><?php echo $info['loan_fee_type'] == 1 ? "$" . $info['loan_fee'] : $info['loan_fee'] . '%' ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Insurance Fee</label></td>
            <td colspan="3"><?php echo ncAmountFormat($info['insurance'][$info['uid']]['price']) ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Due Date</label></td>
            <td colspan="3"><?php echo dateFormat($info['end_date']) ?></td>
        </tr>
        </tbody>
    </table>

    <!--prepaymentModal-->
    <div class="modal" id="prepaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 700px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo 'Prepayment'?></h4>
                </div>
                <div>
                    <form class="col-sm-12" id="prepayment_form">
                        <input type="hidden" id='contract_id' name="contract_id" value="<?php $info['uid']?>">
                        <table class="table">
                            <tr>
                              <td><span class="title"><?php echo 'Need Prepayment' ?></span></td>
                              <td class="rightbox"><span><?php echo 8?></span></td>
                            </tr>
                            <tr>
                                <td><span class="title"><?php echo 'Rest Periods' ?></span></td>
                                <td class="rightbox"><span><?php echo 6?></span></td>
                            </tr>
                            <tr>
                                <td><span class="title"><?php echo 'Rest Principal' ?></span></td>
                                <td class="rightbox"><span><?php echo 50?></span></td>
                            </tr>
                            <tr>
                                <td><span class="redstar">*</span><span class="title"><?php echo 'Prepayment Type' ?></span></td>
                                <td class="rightbox">
                                    <select name="prepayment_type" id="prepayment_type" onchange="chooseType()">
                                        <option  id="full" value="1">Full Payment </option>
                                        <option id="partial" value="0">Partial Payment </option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="part_prepayment">
                                <td><span class="redstar">*</span><span class="title"><?php echo 'Partial Payment' ?></span></td>
                                <td class="rightbox">
                                    <select id="pay_type" name='prepayment_type' onchange="chooseMethod()">
                                        <option id="pay_type_periods">Partial By Periods</option>
                                        <option id="pay_type_amount">Partial By Amount</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="prepayment_periods">
                                <td><span class="required-options-xing">*</span><span class="title"><?php echo 'Prepayment Periods' ?></span></td>
                                <td class="rightbox"><input class='prepayment_periods' type="text" value="" name="repay_period" placeholder="Please input Periods"></td>
                            </tr>
                            <tr id="prepayment_amount">
                                <td><span class="redstar">*</span><span class="title"><?php echo 'Prepayment Amount' ?></span></td>
                                <td class="rightbox"><input class='prepayment_amount' type="text" value="" name="amount" placeholder="Please input Amount"></td>
                            </tr>

                        </table>
                    </form>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                    <button type="button" class="btn btn-danger" onclick="submit_prepayment()"><?php echo 'Submit'?></button>
                </div>
            </div>
        </div>
    </div>

<!--repaymentModal-->
    <div class="modal" id="repaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 700px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo 'Repayment'?></h4>
                </div>
                <div class="modal-body">
                    <div class="modal-form">
                        <form class="form-horizontal" id="repayment_form">
                            <input name="uid" value="<?php echo $info['uid']?>" type="hidden">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Contract Sn' ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $info['contract_sn']?>" id="scheme_name" readonly>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Expired Repayment'; ?></label>
                                <div class="col-sm-9">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr class="table-header" style="background: #EFEFEF">
                                            <td>Scheme Name</td>
                                            <td>Repayment Total</td>
                                            <td>Principal And Interest</td>
                                            <td>Penalties</td>
                                        </tr>
                                        </thead>
                                        <tbody class="table-body">
                                        <?php if(!$info['repayment_arr']){?>
                                            <tr>
                                                <td colspan="4">Null</td>
                                            </tr>
                                        <?php }else{ ?>
                                            <?php
                                            $repayment_total = 0;
                                            $principal_interest_total = 0;
                                            $penalties_total = 0;
                                            foreach ($info['repayment_arr'] as $scheme){?>
                                                <tr>
                                                    <td>
                                                        <?php echo $scheme['scheme_name']?>
                                                    </td>
                                                    <td>
                                                        <?php $repayment = $scheme['amount'] + $scheme['penalties'] - $scheme['actual_payment_amount'];
                                                        $repayment_total += $repayment;
                                                        echo ncAmountFormat($repayment)?>
                                                    </td>
                                                    <td>
                                                        <?php $principal_interest = ($scheme['amount'] - $scheme['actual_payment_amount']) > 0 ? $scheme['amount'] - $scheme['actual_payment_amount'] : 0;
                                                        $principal_interest_total += $principal_interest;
                                                        echo ncAmountFormat($principal_interest)?>
                                                    </td>
                                                    <td>
                                                        <?php $penalties_total += $scheme['penalties'];
                                                        echo ncAmountFormat($scheme['penalties'])?>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            <tr style="font-weight: 700">
                                                <td>
                                                    <?php echo 'Total' ?>
                                                </td>
                                                <td>
                                                    <?php echo ncAmountFormat($repayment_total) ?>
                                                </td>
                                                <td>
                                                    <?php echo ncAmountFormat($principal_interest_total) ?>
                                                </td>
                                                <td>
                                                    <?php echo ncAmountFormat($penalties_total) ?>
                                                </td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Repayment Total' ?></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="repayment_total" value="">
                                        <span class="input-group-addon" style="min-width: 55px;border-left: 0;border-radius: 0">$</span>
                                    </div>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remark' ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="" name="remark">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                    <button type="button" class="btn btn-danger"  onclick="submit_repayment()"><?php echo 'Submit'?></button>
                </div>
            </div>
        </div>
    </div>
<?php }else{?>
    <div style="min-height: 200px;padding: 5px 20px">Null</div>
<?php }?>


<script>
   $(function () {
       $('#part_prepayment').hide();
       $('#prepayment_amount').hide();
       $('#prepayment_periods').hide();
   })

   function chooseType(){
       if( $("#partial").prop('selected')){
           $("#part_prepayment").show();
           if($('#pay_type_periods').prop('selected')){
               $("#prepayment_periods").show();
           }else{
               $("#prepayment_amount").show();
           }

       }else {
           $('#part_prepayment').hide();
           $("#prepayment_amount").hide();
           $("#prepayment_periods").hide();
       }

   }

   function chooseMethod(){
       if($('#pay_type_amount').prop('selected')){
           $("#prepayment_amount").show();
       }else{
           $("#prepayment_amount").hide();
       }

       if($('#pay_type_periods').prop('selected')){
           $("#prepayment_periods").show();
       }else{
           $("#prepayment_periods").hide();
       }
   }

//   function submit_prepayment() {
//       var values = $("#prepayment_form").getValues();
//       yo.loadData({
//           _c: "member",
//           _m: "submitRepayment",
//           param: values,
//           callback: function (_o) {
//               alert(_o.MSG);
//               if (_o.STS) {
//                   btn_search_onclick();
//               }
//           }
//
//   })
</script>

