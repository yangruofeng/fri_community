<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="collection-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                    <option value="855" <?php echo $output['phone_arr'][0] == 855 ? 'selected' : ''?>>+855</option>
                                    <option value="66" <?php echo $output['phone_arr'][0] == 66 ? 'selected' : ''?>>+66</option>
                                    <option value="86" <?php echo $output['phone_arr'][0] == 86 ? 'selected' : ''?>>+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $output['phone_arr'][1];?>" placeholder="">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                            </span>
                        </div>
                        <div class="search-other">
                            <img src="resource/img/member/phone.png">
                            <img src="resource/img/member/qr-code.png">
                            <img src="resource/img/member/bank-card.png">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                                <p class="account-head">
                                    <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                                </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
                                <input type="hidden" id="client_id" name="client_id" value="">
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Login Account</span>:
                                    <span class="marginleft10" id="login-account"><?php echo $client_info['login_account']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                    <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">English Name</span>:
                                    <span class="marginleft10" id="english-name"><?php echo $client_info['display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Member Grade</span>:
                                    <span class="marginleft10" id="member-grade"><?php echo $client_info['member_grade']?></span>
                                </p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="authentication-information">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Authentication Information</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal">
                        <div class="personal-info">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Personal Information: </label>
                                <div class="col-sm-8" id="identity_authentication">
                                    <?php if ($member_info['identity_authentication']) { ?>
                                        <i class="fa fa-check-square-o"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-square-o"></i>
                                    <?php } ?>
                                    <span>Identity Authentication</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="family_book">
                                    <?php if ($member_info['family_book']) { ?>
                                        <i class="fa fa-check-square-o"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-square-o"></i>
                                    <?php } ?>
                                    <span>Family Book</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="working_certificate">
                                    <?php if ($member_info['working_certificate']) { ?>
                                        <i class="fa fa-check-square-o"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-square-o"></i>
                                    <?php } ?>
                                    <span>Working Certificate</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="resident_book">
                                    <?php if ($member_info['resident_book']) { ?>
                                        <i class="fa fa-check-square-o"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-square-o"></i>
                                    <?php } ?>
                                    <span>Resident Book</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                        </div>
                        <div class="assets-certification">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Assets Certification: </label>
                                <div class="col-sm-8" id="vehicle_property">
                                    <span>Vehicle Property</span>
                                    <span class="num">0</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="land_property">
                                    <span>Land Property</span>
                                    <span class="num">0</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="housing_property">
                                    <span>Housing Property</span>
                                    <span class="num">0</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8" id="motorcycle_asset_certificate">
                                    <span>Motorcycle Asset Certificate</span>
                                    <span class="num">0</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                        </div>
                        <div class="relationships">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Relationships: </label>
                                <div class="col-sm-8" id="family_relation_authentication">
                                    <span>Family Relation Authentication</span>
                                    <span class="num">0</span>
                                    <a class="function">【Collect】</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        search_click();

        $('#btn_search').click(function () {
            search_click()
        })

        $('#identity_authentication .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'identityAuthentication', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#working_certificate .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'workAuthentication', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#family_book .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'familyBookAuthentication', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#resident_book .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'residentBookAuthentication', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#vehicle_property .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'vehiclePropertyAuthenticate', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#land_property .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'landPropertyAuthenticate', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#housing_property .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'housingPropertyAuthenticate', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#motorcycle_asset_certificate .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'motorcyclePropertyAuthenticate', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

        $('#family_relation_authentication .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'guaranteeAuthenticate', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })
    })

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientInfo',
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#english-name').html(data.display_name);
                    if (data.identity_authentication) {
                        $('#identity_authentication i').removeClass('fa-square-o').addClass('fa-check-square-o');
                    } else {
                        $('#identity_authentication i').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }
                    if (data.family_book) {
                        $('#family_book i').removeClass('fa-square-o').addClass('fa-check-square-o');
                    } else {
                        $('#family_book i').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }
                    if (data.working_certificate) {
                        $('#working_certificate i').removeClass('fa-square-o').addClass('fa-check-square-o');
                    } else {
                        $('#working_certificate i').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }
                    if (data.resident_book) {
                        $('#resident_book i').removeClass('fa-square-o').addClass('fa-check-square-o');
                    } else {
                        $('#resident_book i').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }

                    $('#vehicle_property .num').html(data.vehicle_property);
                    $('#land_property .num').html(data.land_property);
                    $('#housing_property .num').html(data.housing_property);
                    $('#motorcycle_asset_certificate .num').html(data.motorcycle_asset_certificate);
                    $('#family_relation_authentication .num').html(data.guarantee_num);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>