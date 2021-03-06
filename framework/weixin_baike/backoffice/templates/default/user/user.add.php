<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 5px 15px;
    }

    .auth-list .auth_group {
        margin-bottom: 10px;
    }

    .role-select .col-sm-9 {
        padding-top: 8px;
    }

    .role-select .col-sm-9 label {
        padding-left: 0px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'user', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="user_code" placeholder="" value="<?php echo $_GET['user_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="user_name" placeholder="" value="<?php echo $_GET['user_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Password'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="password" value="<?php echo $_GET['password']?:'123456'?>" placeholder="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch'?></label>
                <div class="col-sm-9">
                    <div class="col-sm-6" style="padding-left: 0;padding-right: 10px">
                        <select name="branch_id" class="form-control">
                            <option value="0" selected="selected">Select Branch</option>
                            <?php foreach($output['branch_list'] as $branch){?>
                                <option value="<?php echo $branch['uid']?>"><?php echo $branch['branch_name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-sm-6" style="padding-left: 10px;padding-right: 0">
                        <select name="depart_id" class="form-control" disabled>
                            <option value="0" selected="selected">Select Department</option>
                        </select>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group role-select">
                <label class="col-sm-3 control-label"><?php echo 'Position'?></label>
                <div class="col-sm-9">
                    <?php foreach ($output['user_position'] as $key => $val) { ?>
                        <label class="col-sm-4"><input type="checkbox" name="user_position[]" value="<?php echo $key?>"><?php echo ucwords(strtolower($val))?></label>
                    <?php }?>
                </div>
            </div>
            <div class="form-group role-select">
                <label class="col-sm-3 control-label"><?php echo 'Role'?></label>
                <div class="col-sm-9">
                    <?php foreach($output['role_list'] as $val){?>
                        <label class="col-sm-4"><input type="checkbox" name="role_select[]" allow-back-office="<?php echo implode(',',$val['allow_back_office']['allow_auth'])?>" allow-counter="<?php echo implode(',',$val['allow_counter']['allow_auth'])?>" value="<?php echo $val['uid']?>"><?php echo $val['role_name']?></label>
                    <?php }?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Back Office Auth'?></label>
                <div class="col-sm-9 auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_back_office'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px;margin-bottom: 5px" class="back_office_auth_group clearfix">
                            <span><?php echo L('auth_'.strtolower($k_1))?></span>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <input type="checkbox" name="auth_select[]" value="<?php echo $v_2?>"><?php echo L('auth_'.strtolower($v_2))?>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Counter Auth'?></label>
                <div class="col-sm-9 auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_counter'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px;margin-bottom: 5px" class="counter_auth_group clearfix">
                            <span><?php echo L('auth_counter_'.strtolower($k_1))?></span>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <input type="checkbox" name="auth_select_counter[]" value="<?php echo $v_2?>"><?php echo L('auth_counter_'.strtolower($v_2))?>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="remark" placeholder="" value="<?php echo $_GET['remark']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="user_status" checked><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="user_status"><?php echo 'Invalid'?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><i class="fa fa-check"></i> <?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function () {
        $('.role-select input').click(function () {
            $('.auth-list input').prop('checked', false);
            $('.role-select input:checked').each(function () {
                var allow_back_office = $(this).attr('allow-back-office');
                var allow_counter = $(this).attr('allow-counter');
                if (allow_back_office) {
                    allow_back_office = allow_back_office.split(',');
                    for (var i = 0; i < allow_back_office.length; i++) {
                        $('.back_office_auth_group input[name="auth_select[]"][value="' + allow_back_office[i] + '"]').prop('checked', true);
                    }
                }
                if (allow_counter) {
                    allow_counter = allow_counter.split(',');
                    for (var i = 0; i < allow_counter.length; i++) {
                        $('.counter_auth_group input[name="auth_select_counter[]"][value="' + allow_counter[i] + '"]').prop('checked', true);
                    }
                }
            })
        })

        $('select[name="branch_id"]').change(function () {
            var _branch_id = $(this).val();
            $('select[name="depart_id"]').html('<option value="0" selected="selected">Select Department</option>').attr('disabled', true);
            if(_branch_id == 0){
                return;
            }
            yo.dynamicTpl({
                tpl: "user/depart.option",
                dynamic: {
                    api: "user",
                    method: "getDepartList",
                    param: {branch_id:_branch_id}
                },
                callback: function (_tpl) {
                    $('select[name="depart_id"]').html(_tpl).attr('disabled', false);
                }
            });
        })

    })

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            user_code: {
                required: true,
                checkNumAndStr: true
            },
            user_name: {
                required: true
            },
            password: {
                required: true
            },
            branch_id: {
                checkRequired: true
            },
            depart_id: {
                checkRequired: true
            }
        },
        messages: {
            user_code: {
                required: '<?php echo 'Required'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            },
            user_name: {
                required: '<?php echo 'Required'?>'
            },
            password: {
                required: '<?php echo 'Required'?>'
            },
            branch_id: {
                checkRequired: '<?php echo 'Required'?>'
            },
            depart_id: {
                checkRequired: '<?php echo 'Required'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        if (value == 0) {
            return false;
        } else {
            return true;
        }
    });


</script>