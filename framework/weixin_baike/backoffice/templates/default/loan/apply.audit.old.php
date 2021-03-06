<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request To Loan</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'apply', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL)?>"><span>Unprocessed</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'apply', array('type' => 'processed'), false, BACK_OFFICE_SITE_URL)?>"><span>Processed</span></a></li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['apply_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['apply_info']['applicant_name']?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Product'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['apply_info']['product_name']?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Applied Amount'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo ncAmountFormat($output['apply_info']['apply_amount'])?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Use'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['apply_info']['loan_propose']?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Contact Phone'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['apply_info']['contact_phone']?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Apply Time'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo timeFormat($output['apply_info']['apply_time'])?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Audit Remark'?></label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="remark" style="height: 100px;"></textarea>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 15px">
                    <button type="button" class="btn btn-danger" style="min-width:80px"><?php echo 'Audit' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);" style="min-width:80px"><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function(){
        $('.btn-danger').click(function () {
            $('.form-horizontal').submit();
        })


        /*window.onbeforeunload = function(){
            var uid = $('input[name="uid"]').val();
            if(!uid){
                return false;
            }

            yo.loadData({
                _c: 'loan',
                _m: 'unlockedApply',
                param: {uid: uid},
                callback: function (_o) {
                    if (!_o.STS) {
                        return false;
                    }
                }
            })
        }*/
    })

</script>
