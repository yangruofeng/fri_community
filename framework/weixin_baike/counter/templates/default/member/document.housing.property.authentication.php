<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css" rel="stylesheet" type="text/css"/>
<style>
    .snapshot_div {
        height: 160px!important;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="authenticate-div-1">
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Housing Property</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info" style="display: none;">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <input type="hidden" name="house_property_card" value="">
                        <input type="hidden" name="house_relationships_certify" value="">
                        <input type="hidden" name="house_front" value="">
                        <input type="hidden" name="house_side_face" value="">
                        <input type="hidden" name="house_front_road" value="">
                        <input type="hidden" name="house_inside" value="">
                    </form>
                    <div class="snapshot_div" id="house_property_card" style="height: 160px" onclick="callWin_snapshot_master('house_property_card');">
                        <img src="resource/img/member/photo.png">
                        <div>House proprietary certificate</div>
                    </div>
                    <div class="snapshot_div" id="house_relationships_certify" onclick="callWin_snapshot_master('house_relationships_certify');">
                        <img src="resource/img/member/photo.png">
                        <div>House relationship</div>
                    </div>
                    <div class="snapshot_div" id="house_front" onclick="callWin_snapshot_slave('house_front');">
                        <img src="resource/img/member/photo.png">
                        <div>Front view of house</div>
                    </div>
                    <div class="snapshot_div" id="house_side_face" onclick="callWin_snapshot_slave('house_side_face');">
                        <img src="resource/img/member/photo.png">
                        <div>Side view of house</div>
                    </div>
                    <div class="snapshot_div" id="house_front_road" onclick="callWin_snapshot_slave('house_front_road');">
                        <img src="resource/img/member/photo.png">
                        <div>Front road of house</div>
                    </div>
                    <div class="snapshot_div" id="house_inside" onclick="callWin_snapshot_slave('house_inside');">
                        <img src="resource/img/member/photo.png">
                        <div>Inside view of house</div>
                    </div>
                    <div class="snapshot_msg error_msg">
                        <div class="house_property_card"></div>
                        <div class="house_relationships_certify"></div>
                        <div class="house_front"></div>
                        <div class="house_side_face"></div>
                        <div class="house_front_road"></div>
                        <div class="house_inside"></div>
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

    function callWin_snapshot_slave(type) {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    _img_path = getUPyunImgUrl(_img_path);
                    $("#" + type + " img").attr("src", _img_path);
                    $('input[name="' + type + '"]').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

    function callWin_snapshot_master(type){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("0");
                if (_img_path != "" && _img_path != null) {
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
        var cert_type = '<?php echo certificationTypeEnum::HOUSE;?>';
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
            _m: 'saveHousingPropertyAuthentication',
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
            if (name == 'house_property_card' || name == 'house_relationships_certify' || name == 'house_front' || name == 'house_side_face' || name == 'house_front_road' || name == 'house_inside') {
                error.appendTo($('.snapshot_msg .' + name));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }
        },
        rules : {
            house_property_card : {
                required : true
            },
            house_relationships_certify : {
                required : true
            },
            house_front : {
                required : true
            },
            house_side_face : {
                required : true
            },
            house_front_road : {
                required : true
            },
            house_inside : {
                required : true
            }
        },
        messages : {
            house_property_card : {
                required : 'Housing ownership certificates required!'
            },
            house_relationships_certify : {
                required : 'House relationship required!'
            },
            house_front : {
                required : 'Front view of house required!'
            },
            house_side_face : {
                required : 'Side view of house required!'
            },
            house_front_road : {
                required : 'Front road of house required!'
            },
            house_inside : {
                required : 'Inside view of house required!'
            }
        }
    });

</script>