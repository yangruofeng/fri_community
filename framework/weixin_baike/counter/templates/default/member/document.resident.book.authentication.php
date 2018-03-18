<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="authenticate-div-1">
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Resident Book</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info" style="display: none">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <input type="hidden" name="resident_book_front" value="">
                        <input type="hidden" name="resident_book_back" value="">
                    </form>
                    <div class="snapshot_div" id="resident_book_front" onclick="callWin_snapshot_master('resident_book_front');">
                        <img src="resource/img/member/photo.png">
                        <div>Frontal resident book image</div>
                    </div>
                    <div class="snapshot_div" id="resident_book_back" onclick="callWin_snapshot_master('resident_book_back');">
                        <img src="resource/img/member/photo.png">
                        <div>Back resident book image</div>
                    </div>
                    <div class="snapshot_msg error_msg">
                        <div class="resident_book_front"></div>
                        <div class="resident_book_back"></div>
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
            _m: 'saveResidentBookAuthentication',
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
            if (name == 'resident_book_front' || name == 'resident_book_back') {
                error.appendTo($('.snapshot_msg .' + name));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }
        },
        rules : {
            resident_book_front : {
                required : true
            },
            resident_book_back : {
                required : true
            }
        },
        messages : {
            resident_book_front : {
                required : 'Frontal resident book image must be uploaded!'
            },
            resident_book_back : {
                required : 'Back resident book image must be uploaded!'
            }
        }
    });

</script>