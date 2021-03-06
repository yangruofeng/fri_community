<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('financial', 'bankAccount', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a href="<?php echo getUrl('financial', 'addReceiveAccount', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form  class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['account_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Bank Name'; ?></label>
                <div class="col-sm-8">
                    <select name="bank_code" class="form-control">
                        <?php foreach ($output['bank_list'] as $key => $val){?>
                            <option value="<?php echo $val['bank_code']; ?>" <?php echo $output['account_info']['bank_code'] == $key ? 'selected' : ''?>><?php echo $val['bank_name'];?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Account No'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_no" placeholder="" value="<?php echo $output['account_info']['bank_account_no']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Account Name'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_name" placeholder="" value="<?php echo $output['account_info']['bank_account_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Account Phone'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_phone" placeholder="" value="<?php echo $output['account_info']['bank_account_phone']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'; ?></label>
                <div class="col-sm-8">
                    <select name="currency" class="form-control">
                        <?php foreach($output['currency_list'] as $key => $val) { ?>
                            <option value="<?php echo $key; ?>" <?php echo $output['account_info']['currency'] == $key ? 'selected' : ''?>><?php echo $val;?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo 'Branch';?></label>
                <div class="col-sm-8">
                    <?php foreach ($output['branch_list'] as $val) { ?>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="<?php echo $val['uid']?>" name="branch_id[]" <?php echo in_array($val['uid'],$output['branch_id']) ? 'checked' : ''?>>
                            <?php echo $val['branch_name']?>
                        </label>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo 'Allow Client Deposit';?></label>
                <div class="col-sm-8">
                    <label class="checkbox-inline"><input type="checkbox" value="1" name="allow_client_deposit" <?php echo intval($output['account_info']['allow_client_deposit']) == 1 ? 'checked' : ''?>></label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo 'State ';?></label>
                <div class="col-sm-8" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="account_state" <?php echo !isset($output['account_info']['account_state']) ? 'checked' : ($output['account_info']['account_state'] == 1 ? 'checked' : '')?>><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="account_state" <?php echo (isset($output['account_info']['account_state']) && $output['account_info']['account_state'] == 0) ? 'checked' : '' ?>><?php echo 'Invalid'?></label>
                </div>
            </div>

            <div class="form-group">
                <div style="text-align: center">
                    <button type="button" class="btn btn-danger" style="min-width: 80px"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="min-width: 80px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return false;
        }

        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            bank_account_no: {
                required: true
            },
            bank_account_name: {
                required: true
            }
        },
        messages: {
            bank_account_no: {
                required: '<?php echo 'Required'?>'
            },
            bank_account_name: {
                required: '<?php echo 'Required'?>'
            }
        }
    });


</script>