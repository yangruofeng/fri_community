<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    #select_area .col-sm-6:nth-child(2n+1) {
        padding-left: 0;
        padding-right: 3px;
        margin-bottom: 10px;
    }

    #select_area .col-sm-6:nth-child(2n) {
        padding-right: 0;
        padding-left: 3px;
        margin-bottom: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="address_id" value="">
            <input type="hidden" name="address_region" value="">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="branch_code" placeholder="" value="<?php echo $_GET['branch_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="branch_name" placeholder="" value="<?php echo $_GET['branch_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Contact phone' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="contact_phone" value="<?php echo $_GET['contact_phone'] ?>" placeholder="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Manager' ?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="manager">
                        <option value="0">Please Select</option>
                        <?php foreach($output['user_list'] as $user){?>
                            <option value="<?php echo $user['uid']?>"><?php echo $user['user_code']?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Limit Loan' ?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="number" class="form-control" name="limit_loan[max_per_time]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                        <input type="number" class="form-control" name="limit_loan[max_per_day]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Limit Deposit' ?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="number" class="form-control" name="limit_deposit[max_per_time]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                        <input type="number" class="form-control" name="limit_deposit[max_per_day]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Limit Exchange' ?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="number" class="form-control" name="limit_exchange[max_per_time]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                        <input type="number" class="form-control" name="limit_exchange[max_per_day]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Limit Withdraw' ?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="number" class="form-control" name="limit_withdraw[max_per_time]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                        <input type="number" class="form-control" name="limit_withdraw[max_per_day]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Limit Transfer' ?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="number" class="form-control" name="limit_transfer[max_per_time]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                        <input type="number" class="form-control" name="limit_transfer[max_per_day]" value="">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Bank' ?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="bank_id">
                        <option value="0">Please Select</option>
                        <?php foreach($output['bank_list'] as $bank){?>
                            <option value="<?php echo $bank['uid']?>"><?php echo $bank['bank_code']?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Location' ?></label>
                <div class="col-sm-9" id="select_area"></div>
                <div class="col-sm-9 col-sm-offset-3">
                    <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Map</label>
                <div class="col-sm-9">
                    <div id="map" style="width: 500px;height: 300px;border: 1px solid #9e9e9e"></div>
                    <input type="hidden" id="coord_x" name="coord_x" value="">
                    <input type="hidden" id="coord_y" name="coord_y" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="status" checked><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="status"><?php echo 'Invalid'?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&callback=initMap"></script>
<script type="text/javascript">

    //Google map start
    var geocoder;
    var map;
    var marker;
    function initMap() {
        if($('#map').length==0){
            return;
        }
        //地图初始化
        var coord_x = '11.54461675917885';
        var coord_y = '104.89746106250004';
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(coord_x, coord_y);
        var myOptions = {
            zoom: 13,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        //引入marker
        marker = new google.maps.Marker({
            position: latlng,
            map: map,
            draggable:true,
            title:"Drag me!"
        });

        // 获取坐标
        google.maps.event.addListener(marker, "dragend", function () {
            $('#coord_x').val(marker.getPosition().lat());
            $('#coord_y').val(marker.getPosition().lng());
        });
    }

    //根据地址获取经纬度
    function codeAddress(address,zoom) {
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                map.setZoom(zoom);
                marker.setPosition(results[0].geometry.location);
                $('#coord_x').val(marker.getPosition().lat());
                $('#coord_y').val(marker.getPosition().lng());
            }
        });
    }
    //google map end
</script>
<script>
    var _address_region;
    $(function () {
        getArea(0);
        $('#area_reset').click(function () {
            $('.area_reset').show();
        })

        $('#select_area').delegate('select', 'change', function () {
            var _value = $(this).val();
            $('input[name="address_id"]').val(_value);
            $(this).closest('div').nextAll().remove();
            _address_region = '';
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region += $(this).find('option:selected').text() + ' ';
                }
            })
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
            if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
                getArea(_value);
            }
        })

        $('input[name="address_detail"]').change(function () {
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
        })

        $('.btn-danger').click(function () {
            if (!$(".form-horizontal").valid()) {
                return;
            }

            $('input[name="address_region"]').val(_address_region);

            $('.form-horizontal').submit();
        })
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            branch_code: {
                required: true,
                checkNumAndStr:true
            },
            branch_name: {
                required: true
            }
        },
        messages: {
            branch_code: {
                required: '<?php echo 'Required!'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            },
            branch_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "setting/area.list",
            dynamic: {
                api: "setting",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        })
    }

    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });
</script>