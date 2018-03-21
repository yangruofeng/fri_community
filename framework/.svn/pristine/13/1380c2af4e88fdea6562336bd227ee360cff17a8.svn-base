<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table tr td:first-child {
        width: 200px;
    }

    .audit-table textarea {
        width: 300px;
        height: 80px;
        float: left;
    }

    .custom-btn-group {
        float: inherit;
    }
</style>
<?php
$loanApplySourceLang = enum_langClass::getLoanApplySourceLang();
$unit_lang = enum_langClass::getLoanTimeUnitLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request To Loan</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'apply', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Unprocessed</span></a>
                </li>
                <li>
                    <a href="<?php echo getUrl('loan', 'apply', array('type' => 'processed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Processed</span></a>
                </li>
                <li><a class="current"><span>Handle</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Name</label></td>
                    <td><?php echo $output['apply_info']['applicant_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Product</label></td>
                    <td><?php echo $output['apply_info']['product_name']?:'No'; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Amount</label></td>
                    <td><?php echo ncPriceFormat($output['apply_info']['apply_amount']).$output['apply_info']['currency']; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Time</label></td>
                    <td><?php  echo $output['apply_info']['loan_time'].' '.$unit_lang[$output['apply_info']['loan_time_unit']]; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Purpose</label></td>
                    <td><?php echo $output['apply_info']['loan_purpose'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Mortgage</label></td>
                    <td><?php echo $output['apply_info']['mortgage'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Contact Phone</label></td>
                    <td><?php echo $output['apply_info']['contact_phone'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Address</label></td>
                    <td><?php echo $output['apply_info']['applicant_address'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Time</label></td>
                    <td><?php echo timeFormat($output['apply_info']['apply_time']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Source</label></td>
                    <td><?php echo $loanApplySourceLang[$output['apply_info']['request_source']]; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Allot Credit Officer</label></td>
                    <td>

                        <?php if( !empty($output['bound_credit_officer']) ){ ?>
                            <?php echo $output['bound_credit_officer']['user_name']; ?>
                        <?php }else{ ?>
                            <select name="credit_officer_id" class="form-control" style="width: 300px" <?php echo $output['lock']?"disabled":"" ?>>
                                <option value="0" selected="selected"><?php echo $lang['common_select']?></option>
                                <?php foreach($output['credit_officer_list'] as $val){?>
                                    <option value="<?php echo $val['uid'] ?>"><?php echo $val['user_name'] ?: $val['user_code'] ?></option>
                                <?php }?>
                            </select>
                        <?php } ?>

                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Audit Remark</label></td>
                    <td>
                        <?php if ($output['lock']) {
                            echo '<span class="color28B779">Auditing...</span>';
                        } else { ?>
                            <textarea class="form-control" name="remark"></textarea><span
                                class="validate-checktip"></span>
                        <?php } ?>

                    </td>
                </tr>
                <?php if ($output['lock']) { ?>
                    <tr>
                        <td><label class="control-label">Handler</label></td>
                        <td><span class="color28B779"><?php echo $output['apply_info']['handler_name']; ?></span></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <?php if ($output['lock']) { ?>
                            <span class="color28B779">Auditing...</span>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i
                                        class="fa fa-vcard-o"></i>Back
                                </button>
                            </div>
                        <?php } else { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" style="min-width:80px;"
                                        onclick="applyApproved();"><i class="fa fa-check"></i><?php echo 'Approve' ?>
                                </button>

                                <button type="button" class="btn btn-default" style="min-width:80px;"
                                        onclick="applyReject();"><i class="fa fa-close"></i><?php echo 'Reject' ?>
                                </button>

                                <button type="button" class="btn btn-info" onclick="javascript:history.go(-1);"
                                        style="min-width:80px"><i class="fa fa-vcard-o"></i><?php echo 'Back' ?>
                                </button>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="approve_state" value="0">
            <input type="hidden" name="uid" value="<?php echo $output['apply_info']['uid']; ?>">
        </form>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=20"></script>
<script>
    $(function () {
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
    });

    function applyApproved()
    {
        $('input[name="approve_state"]').val(1);
        submitForm();
    }

    function applyReject()
    {
        $('input[name="approve_state"]').val(0);
        submitForm();
    }

    function submitForm() {
        $('#validForm').submit();
    }

    var validParam = {
        ele: '#validForm', //表单id
        params: [{
            field: 'remark',
            rules: {
                required: true
            },
            messages: {
                required: 'Please input the remark.'
            }
        }]
    };
    validform(validParam);
</script>
