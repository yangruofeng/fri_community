<!--<script src="--><?php //echo GLOBAL_RESOURCE_SITE_URL; ?><!--/js/jquery.validation.min.js"></script>-->
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Function Switch</h3>
            <ul class="tab-base">
<!--                <li><a class="current"><span>Edit</span></a></li>-->
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close password reset' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_reset_password" value="1" <?php echo $output['function_switch']['close_reset_password'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close credit loan withdraw' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_credit_withdraw" value="1" <?php echo $output['function_switch']['close_credit_withdraw'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close register to send credit'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_register_send_credit" value="1" <?php echo $output['function_switch']['close_register_send_credit'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-6 col-sm-6">
                    <button type="button" class="btn btn-danger" style="margin-left: 0;min-width: 100px">
                        <i class="fa fa-check"></i>
                        <?php echo 'Submit' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btn-danger').click(function () {
        $('.form-horizontal').submit();
    })

</script>