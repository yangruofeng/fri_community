<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="authenticate-div-1">
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Land Property</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info" style="display: none;">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <input type="hidden" name="land_property_card" value="">
                        <input type="hidden" name="land_trading_record" value="">
                    </form>
                    <div class="snapshot_div" id="land_property_card" onclick="callWin_snapshot_master('land_property_card');">
                        <img src="resource/img/member/photo.png">
                        <div>Land Property</div>
                    </div>
                    <div class="snapshot_div" id="land_trading_record" onclick="callWin_snapshot_master('land_trading_record');">
                        <img src="resource/img/member/photo.png">
                        <div>Land Transaction Table</div>
                    </div>
                    <div class="snapshot_msg error_msg">
                        <div class="land_property_card"></div>
                        <div class="land_trading_record"></div>
                    </div>
                </div>
            </div>
            <div class="operation" style="margin-bottom: 40px">
                <a class="btn btn-default" href="<?php echo getUrl('member', 'documentCollection', array('client_id' => $output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>

            <div class="certification-history">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Certification History</h5>
                </div>
                <div class="content">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.js"></script>

<script>
    var upyun_url = '<?php echo C('upyun_param')['upyun_url']?>/';

    $(document).ready(function () {
        btn_search_onclick();
    });

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

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 10;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var uid = $('input[name="client_id"]').val();
        var cert_type = '<?php echo certificationTypeEnum::LAND;?>';
        yo.dynamicTpl({
            tpl: "member/history.list",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getCertificationList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, uid: uid, cert_type: cert_type}
            },
            callback: function (_tpl) {
                $(".certification-history .content").html(_tpl);
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
            _m: 'saveLandPropertyAuthentication',
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
            if (name == 'land_property_card' || name == 'land_trading_record') {
                error.appendTo($('.snapshot_msg .' + name));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }
        },
        rules : {
            land_property_card : {
                required : true
            },
            land_trading_record : {
                required : true
            }
        },
        messages : {
            land_property_card : {
                required : 'Land property image must be uploaded!'
            },
            land_trading_record : {
                required : 'Land transaction table image must be uploaded!'
            }
        }
    });

</script>