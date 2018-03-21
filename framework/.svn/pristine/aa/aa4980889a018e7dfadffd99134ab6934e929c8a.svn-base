<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="authenticate-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Guarantee Relation</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Relationship</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="relationship">
                                    <option value="0">Please Select</option>
                                    <?php foreach($output['guarantee_relationship']['item_list'] as $key => $val){?>
                                        <option value="<?php echo $key?>"><?php echo $val;?></option>
                                    <?php }?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Member Account</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="member_account" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Member Account</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon" style="padding: 0;border: 0;">
                                        <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                            <option value="855">+855</option>
                                            <option value="66">+66</option>
                                            <option value="86">+86</option>
                                            <option value="84">+84</option>
                                        </select>
                                    </span>
                                    <input type="text" class="form-control" id="phone" name="phone" value="" placeholder="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="operation" style="margin-bottom: 40px">
                <a class="btn btn-default" href="<?php echo getUrl('member', 'documentCollection', array('client_id' => $output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>

            <div class="certification-history">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Guarantor</h5>
                </div>
                <div class="content guarantor" style="padding: 0;border-bottom: 1px solid #D5D5D5;">

                </div>
            </div>

            <div class="certification-history">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Apply Guarantor</h5>
                </div>
                <div class="content apply_guarantor" style="padding: 0;border-bottom: 1px solid #D5D5D5;">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
    $(document).ready(function () {
        btn_search_onclick('guarantor');
        btn_search_onclick('apply_guarantor');
    });

    function btn_search_onclick(type) {
        var uid = $('input[name="client_id"]').val();
        yo.dynamicTpl({
            tpl: "member/guarantee.list",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getGuarantorList",
                param: {uid: uid, type: type}
            },
            callback: function (_tpl) {
                if(type == 'guarantor'){
                    $(".certification-history .guarantor").html(_tpl);
                }else {
                    $(".certification-history .apply_guarantor").html(_tpl);
                }
            }
        });
    }

    function submit_form() {
        if (!$("#basic-info").valid()) {
            return;
        }

        var values = $('#basic-info').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'saveGuarantorAuthentication',
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
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules : {
            relationship : {
                chkRelationship : true
            },
            member_account : {
                required : true
            },
            phone : {
                required : true
            }
        },
        messages : {
            relationship : {
                chkRelationship : 'Required!'
            },
            member_account : {
                required : 'Required!'
            },
            phone : {
                required : 'Required!'
            }
        }
    });

    jQuery.validator.addMethod("chkRelationship", function (value, element) {
        if (!value || value == 0) {
            return false;
        } else {
            return true;
        }
    });

</script>