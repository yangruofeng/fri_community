<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="authenticate-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Working Certificate</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Company Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="company_name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Company Address</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="company_address" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Position</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="position" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Company Employee</label>
                            <div class="col-sm-8">
                                <label class="radio-inline"><input type="radio" name="is_government" value="1">Yes</label>
                                <label class="radio-inline"><input type="radio" name="is_government" value="0" checked>No</label>
                            </div>
                        </div>
                        <input type="hidden" name="working_certificate" value="">
                        <input type="hidden" name="work_employment_certification" value="">
                    </form>
                </div>
            </div>
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Scene Photo</h5>
                </div>
                <div class="content">
                    <div class="snapshot_div" id="working_certificate" onclick="callWin_snapshot_master('working_certificate');">
                        <img src="resource/img/member/photo.png">
                        <div>Working Certificate</div>
                    </div>
                    <div class="snapshot_div" id="work_employment_certification" onclick="callWin_snapshot_master('work_employment_certification');">
                        <img src="resource/img/member/photo.png">
                        <div>Hold Certificate of Employment</div>
                    </div>
                    <div class="snapshot_msg error_msg">
                        <div class="working_certificate"></div>
                        <div class="work_employment_certification"></div>
                    </div>

                </div>
            </div>
            <div class="operation">
                <a class="btn btn-default" href="<?php echo getUrl('member', 'documentCollection', array('client_id' => $output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script>
    var upyun_url = '<?php echo C('upyun_param')['upyun_url']?>/';
    function callWin_snapshot_master(type){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("0");
                if(_img_path!="" && _img_path!=null){
                    _img_path = getUPyunImgUrl(_img_path);
                    $("#" + type + " img").attr("src", _img_path);
                    $('input[name="' + type + '"]').val(_img_path);
                }
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function getUPyunImgUrl(_img_path) {
        return upyun_url + _img_path;
    }

    function submit_form() {
        if (!$("#basic-info").valid()) {
            return;
        }

        var values = $('#basic-info').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'saveWorkAuthentication',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(window.location.href = _o.DATA.url, 1000);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#basic-info').validate({
        errorPlacement: function(error, element){
            var name = $(element).attr('name');
            if (name == 'working_certificate' || name == 'work_employment_certification') {
                error.appendTo($('.snapshot_msg .' + name));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }
        },
        rules : {
            company_name : {
                required : true
            },
            company_address : {
                required : true
            },
            position : {
                required : true
            },
            company_employee : {
                required : true
            },
            working_certificate : {
                required : true
            },
            work_employment_certification : {
                required : true
            }
        },
        messages : {
            company_name : {
                required : 'Required'
            },
            company_address : {
                required : 'Required'
            },
            position : {
                required : 'Required'
            },
            company_employee : {
                required : 'Required'
            },
            working_certificate : {
                required : 'Working certificate must be uploaded!'
            },
            work_employment_certification : {
                required : 'Hold Certificate of Employment must be uploaded!'
            }
        }
    });

</script>