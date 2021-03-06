<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="member_image" value="">
                        <input type="hidden" name="verify_id" value="">
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Mobile Phone</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                     <span class="input-group-addon" style="padding: 0;border: 0;">
                                        <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                            <option value="855">+855</option>
                                            <option value="66">+66</option>
                                            <option value="86">+86</option>
                                        </select>
                                     </span>
                                    <input type="text" class="form-control" name="phone" value="">
                                    <span class="input-group-addon" style="padding: 0;border: 0;" >
                                        <a class="btn btn-default" id="btnSendCode" style="width: 60px;height: 30px;padding:5px 12px;border-radius: 0" onclick="send_verify_code()">Send</a>
                                    </span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Login Password</label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="login_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Verify Code</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="verify_code" value="" maxlength="6">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Confirm Login Password</label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="confirm_login_password" confirm_name="login_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Login Account</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="login_account" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Trading Password</label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="trading_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Marital Status</label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="1" checked>Married
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="0">Single
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="2">Divorce
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Confirm Trading Password</label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="confirm_trading_password" confirm_name="trading_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Scene Photo</h5>
                </div>
                <div class="content">
                    <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                        <img id="img_slave" src="resource/img/member/photo.png">
                    </div>
                    <div class="snapshot_msg error_msg" style="margin-left: 15px;float: left;background-color: #FFF"></div>
                </div>
            </div>
            <div class="operation">
                <button class="btn btn-default" onclick="reset_form()">Reset</button>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    var upyun_url = '<?php echo C('upyun_param')['upyun_url']?>/';
    var InterValObj; //timer变量，控制时间
    var count = 30; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    function callWin_snapshot_slave() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    _img_path = getUPyunImgUrl(_img_path);
                    $("#img_slave").attr("src", _img_path);
                    $('input[name="member_image"]').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

    function getUPyunImgUrl(_img_path) {
        return upyun_url + _img_path;
    }

    function send_verify_code() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('input[name="phone"]').val();
        phone = $.trim(phone);
        if (!phone) {
            return;
        }

        curCount = count;
        $("#btnSendCode").attr("disabled", "true");
        $("#btnSendCode").html(curCount + "S");
        InterValObj = window.setInterval(SetRemainTime, 1000);

        yo.loadData({
            _c: "member",
            _m: "sendVerifyCodeForRegister",
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    $('input[name="verify_id"]').val(_o.DATA.verify_id);
                } else {
                    alert(_o.MSG);
                    window.clearInterval(InterValObj);//停止计时器
                    $("#btnSendCode").attr("disabled", false);//启用按钮
                    $("#btnSendCode").html("Send");
                }
            }
        });
    }

    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $("#btnSendCode").attr("disabled", false);//启用按钮
            $("#btnSendCode").html("Send");
        } else {
            curCount--;
            $("#btnSendCode").html(curCount + "s");
        }
    }

    function reset_form() {
        $('#basic-info input[type!="radio"]').val('');
        $('#basic-info input[name="civil_status"][value="1"]').prop('checked', true);
        $('#basic-info select[name="country_code"]').val(855);
        $("#img_slave").attr("src", "resource/img/member/photo_default.png");
    }

    function submit_form() {
        if (!$("#basic-info").valid()) {
            return;
        }

        var values = $('#basic-info').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'registerClient',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    reset_form();
                    alert(_o.MSG);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#basic-info').validate({
        errorPlacement: function(error, element){
            if ($(element).attr('name') == 'member_image') {
                error.appendTo($('.snapshot_msg'));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }

        },
        rules : {
            phone : {
                required : true
            },
            verify_code : {
                required : true
            },
            login_account : {
                required : true,
                chkAccount : true
            },
            member_image : {
                required : true
            },
            login_password : {
                required : true,
                checkPwd : true
            },
            confirm_login_password : {
                required : true,
                verifyPwd : true
            },
            trading_password : {
                required : true,
                checkPwd : true
            },
            confirm_trading_password : {
                required : true,
                verifyPwd : true
            }
        },
        messages : {
            phone : {
                required : 'Required'
            },
            verify_code : {
                required : 'Required'
            },
            login_account : {
                required : 'Required',
                chkAccount : 'Account number one must be the letter, and the length is between 5 and 12.'
            },
            member_image : {
                required : 'Required'
            },
            login_password : {
                required : 'Required',
                checkPwd : 'The password must be 6-18 digits or letters.'
            },
            confirm_login_password : {
                required : 'Required',
                verifyPwd : 'Confirm password error.'
            },
            trading_password : {
                required : 'Required',
                checkPwd : 'The password must be 6-18 digits or letters.'
            },
            confirm_trading_password : {
                required : 'Required',
                verifyPwd : 'Confirm password error.'
            }
        }
    });

    jQuery.validator.addMethod("chkAccount", function (value, element) {
        value = $.trim(value);
        if (!/^[a-zA-z][A-Za-z0-9]{4,11}$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkPwd", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]{6,18}$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("verifyPwd", function (value, element) {
        var confirm_name = $(element).attr('confirm_name');
        var new_password = $.trim($('input[name="' + confirm_name + '"]').val());
        value = $.trim(value);
        if (new_password == value) {
            return true;
        } else {
            return false;
        }
    });

</script>