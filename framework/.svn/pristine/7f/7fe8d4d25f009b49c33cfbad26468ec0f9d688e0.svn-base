<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    #auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 7px 15px;
    }

    #auth-list .auth_group {
        margin-bottom: 10px;
    }
    .form-filter {
      width: 49%;
      float: left;
    }
    .client-info {
      width: 49%;
      float: right;
    }
    .client-info .ibox-content {
      padding: 0;
    }
    .client-info .verification-info .ibox-content .item {
      padding: 15px 15px;
      width: 50%;
      float: left;
    }
    .client-info .verification-info .item span {
      font-size: 12px;
      margin-left: 5px;
      float: right;
    }
    .client-info .verification-info .item span.checked {
      color: #32BC61;
    }
    .client-info .verification-info .item span.checking {
      color: red;
    }
    .client-info .base-info {
      margin-top: 20px;
    }
    .activity-list .item {
      margin-top: 0;
      border-right: 1px solid #e7eaec;
    }
    .activity-list .item:nth-child(2n){
      border-right: 0;
    }

    .row-active-list .item{
        padding: 10px;
        border-bottom: 1px solid #e7eaec;
    }
</style>
<?php
$approval_info = $output['approval_info'];
$reference_value = $output['credit_reference_value'];
$credit_info = $output['credit_info'];
$info = $output['info'];
?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'credit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container clearfix">
        <div class="form-filter">
          <div class="form-wrap">
            <form class="form-horizontal" method="post" action="<?php echo getUrl('loan', 'editCredit', array(), false, BACK_OFFICE_SITE_URL) ?>">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="obj_guid" value="<?php echo $output['loan_info']['obj_guid']?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">CID</label>
                    <div class="col-sm-8" style="line-height: 30px;">
                      <?php echo $info['obj_guid'];?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-8" style="line-height: 30px;">
                      <?php echo $info['display_name'];?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Credit Limit'?></label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" name="credit" placeholder="" value="<?php if($approval_info['uid']){echo $approval_info['current_credit'];}else{echo $credit_info['credit'];}?>">
                        <div class="error_msg"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Valid Time';?></label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="number" class="form-control" name="valid_time" placeholder="" value="">
                            <span class="input-group-addon">Year</span>
                        </div>

                        <div class="error_msg"></div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Monthly Repayment Ability'?></label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" name="repayment_ability" placeholder="" value="<?php if($approval_info['uid']){echo $approval_info['repayment_ability'];}else{echo $output['loan_info']['repayment_ability'];}?>">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Remark'?></label>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="remark" rows="8" cols="80"><?php if($approval_info['uid']){ echo $approval_info['remark']; }?></textarea>
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-col-sm-8" style="padding-left: 15px">
                        <button type="button" class="btn btn-danger <?php if($approval_info['uid']){echo 'disabled';} ?>"><?php echo 'Save' ?></button>
                        <!--<button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><?php echo 'Back'?></button>-->
                        <?php if($approval_info['uid']){ ?><span style="color: red;">Auditing...</span><?php }?>
                    </div>
                </div>
            </form>
          </div>

            <!--建议授信金额-->
            <div class="credit-reference-value" style="margin-top: 20px;">
                <div>
                    <div class="ibox-title">
                        <h4>Credit Reference Value</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="row-active-list clearfix">
                            <div class="item row">
                                <div class="col-sm-4">
                                    Credit Amount
                                </div>
                                <div class="col-sm-8">
                                    Certification List
                                </div>
                            </div>
                            <?php if( !empty($reference_value) ){ foreach( $reference_value as $reference ){ ?>
                                <div class="item row">
                                    <div class="col-sm-4">
                                        <?php echo $reference['min_amount'].' - '.$reference['max_amount']; ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php
                                            echo join(',',$reference['cert_list']);
                                        ?>
                                    </div>
                                </div>
                            <?php } } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="client-info">
          <div class="verification-info">
            <div class="ibox-title">
              <h5>Verification Result</h5>
            </div>
            <div class="ibox-content">
              <div class="activity-list clearfix">

                <?php $verify_field = $output['verify_field']; $verifys = $output['verifys'];?>

                <?php foreach ($verify_field as $key => $value) { ?>

                    <div class="item">
                        <div>

                            <?php echo $value; ?>
                            <span>
                                <?php if( $verifys[$key] == 1 ){ ?>
                                    <i class="fa fa-check" aria-hidden="true" style="font-size: 18px;color:green;"></i>
                                <?php }else{ ?>
                                    <i class="fa fa-question" aria-hidden="true" style="font-size: 18px;color:red;"></i>
                                <?php } ?>
                                <i></i>
                            </span>
                        </div>
                    </div>

                <?php } ?>

              </div>
            </div>
          </div>
          <div class="base-info">

            <div class="ibox-title">
              <h5>Client Info</h5>
              <a class="pull-right" href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$info['uid'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>">More Detail</a>
            </div>
            <div class="ibox-content">
              <div class="activity-list clearfix">
                <table class="table">
                    <tbody class="table-body">
                        <tr>
                          <td><label class="control-label">CID</label></td><td><?php echo $info['obj_guid'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Name</label></td><td><?php echo $info['display_name'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Credit</label></td><td><?php echo $info['credit'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Account Type</label></td><td><?php echo 'Member';?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Phone</label></td><td><?php echo $info['phone_id'];?></td>
                        </tr>
                        <tr>
                          <td><label class="control-label">Email</label></td><td><?php echo $info['email'];?></td>
                        </tr>
                      </tbody>
                    </table>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
<script>
    $(function () {

    });

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function(error, element){
            var ele =$(element).closest('.form-group').find('.error_msg');
            error.appendTo(ele);
        },
        rules : {
            credit : {
                required : true
            },
            valid_time:{
                required: true
            },
            repayment_ability : {
                required : true
            },
            remark : {
                required : true
            }
        },
        messages : {
            credit : {
                required : '<?php echo 'Required';?>'
            },
            valid_time:{
                required: '<?php echo 'Required';?>'
            },
            repayment_ability : {
                required : '<?php echo 'Required';?>'
            },
            remark : {
                required : '<?php echo 'Required';?>'
            }
        }
    });

</script>
