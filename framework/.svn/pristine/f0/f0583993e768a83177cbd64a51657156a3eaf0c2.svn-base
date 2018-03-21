<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>
    #auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 7px 15px;
    }

    #auth-list .auth_group {
        margin-bottom: 10px;
    }

    .form-filter {
        width: 49%;
        float: left;
    }

    .client-info {
        width: 49%;
        float: right;
    }

    .client-info .ibox-content {
        padding: 0;
    }

    .client-info .verification-info .ibox-content .item {
        padding: 15px 15px;
        width: 50%;
        float: left;
    }

    .client-info .verification-info .item span {
        font-size: 12px;
        margin-left: 5px;
        float: right;
    }

    .client-info .verification-info .item span.checked {
        color: #32BC61;
    }

    .client-info .verification-info .item span.checking {
        color: red;
    }

    .client-info .base-info {
        margin-top: 20px;
    }

    .activity-list .item {
        margin-top: 0;
        border-right: 1px solid #e7eaec;
    }

    .activity-list .item:nth-child(2n) {
        border-right: 0;
    }

    .row-active-list .item {
        padding: 10px;
        border-bottom: 1px solid #e7eaec;
    }

    .base-info .ibox-content tbody tr td label {
        padding-left: 10px;
    }

    .loan-exp {
        float: left;
        margin-left: 10px;
        position: relative;
        margin-top: 3px;
    }

    .loan-exp > span {
        color: #5b9fe2;
    }

    .loan-exp > span:hover {
        color: #ea544a;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 24px;
        right: 10px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-hover .loan-exp-wrap {
        filter: alpha(enabled=false);
        opacity: 1;
        visibility: visible;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 18px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    .triangle-up {
        left: auto!important;
        right: 30px;
    }

    .loan-exp-table .t {
        height: 20px;
    }

    .loan-exp-table .a {
        font-size: 14px;
        height: 30px;
    }
</style>
<?php
$approval_info = $output['approval_info'];
$reference_value = $output['credit_reference_value'];
$credit_info = $output['credit_info'];
$info = $output['info'];
$certificationTypeEnumLangLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('operator', 'grantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container clearfix">
        <div class="form-filter">
            <div class="form-wrap">
                <form class="form-horizontal" method="post" action="<?php echo getUrl('operator', 'editCredit', array(), false, BACK_OFFICE_SITE_URL) ?>">
                    <input type="hidden" name="form_submit" value="ok">
                    <input type="hidden" name="obj_guid" value="<?php echo $output['loan_info']['obj_guid'] ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">CID</label>
                        <div class="col-sm-8" style="line-height: 30px;">
                            <?php echo $info['obj_guid']; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-8" style="line-height: 30px;">
                            <?php echo $info['display_name']; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?php echo 'Credit Limit' ?>
                        </label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="credit" value="<?php echo $approval_info['uid'] ? $approval_info['current_credit'] : $credit_info['credit'] ?>">
                            <div class="error_msg"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?php echo 'Valid Time'; ?>
                        </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="number" class="form-control" name="valid_time" value="<?php echo $approval_info['uid'] ? $approval_info['valid_time'] : '';?>">
                                <span class="input-group-addon" style="min-width: 70px">Year</span>
                            </div>
                            <div class="error_msg"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?php echo 'Monthly Repayment Ability' ?>
                        </label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="repayment_ability" value="<?php echo $approval_info['uid'] ? $approval_info['repayment_ability'] : $output['loan_info']['repayment_ability'];?>">
                            <div class="error_msg"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?php echo 'Remark' ?>
                        </label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="remark" rows="8" cols="80"><?php echo $approval_info['uid'] ? $approval_info['remark'] : '';?></textarea>
                            <div class="error_msg"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-col-sm-8" style="padding-left: 15px">
                            <button type="button" style="min-width: 80px" class="btn btn-danger <?php echo $approval_info['uid'] ? 'disabled' : ""?>"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                            <?php if ($approval_info['uid']) { ?><span style="color: red;">Auditing...</span><?php } ?>
                        </div>
                    </div>
                </form>
            </div>

            <!--建议授信金额-->
            <div class="credit-reference-value" style="margin-top: 20px;">
                <div>
                    <div class="ibox-title">
                        <h4>Credit Level</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="row-active-list clearfix">
                            <div class="item row" style="font-weight: 600">
                                <div class="col-sm-4">
                                    Credit Amount
                                </div>
                                <div class="col-sm-8">
                                    Certification List
                                </div>
                            </div>
                            <?php if (!empty($reference_value)) {
                                foreach ($reference_value as $reference) { ?>
                                    <div class="item row">
                                        <div class="col-sm-4">
                                            <?php echo $reference['min_amount'] . ' - ' . $reference['max_amount']; ?>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php echo join(',', $reference['cert_list']);?>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="client-info">
            <div class="base-info" style="margin-top: 0">
                <div class="ibox-title">
                    <h4>Check Result</h4>
                </div>
                <div class="ibox-content">
                    <div class="activity-list clearfix">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Credit Officer</label></td>
                                <td><?php echo $info['co_name']; ?></td>
                            </tr>
<!--                            <tr>-->
<!--                                <td><label class="control-label">Is Check</label></td>-->
<!--                                <td>--><?php //echo $info['co_state'] > 0 ? "YES" : "NO"; ?><!--</td>-->
<!--                            </tr>-->
                            <tr>
                                <td><label class="control-label">Assets Valuation</label></td>
                                <td>
                                    <div style="float: left"><em style="font-weight: 600;font-size: 18px;color: red"><?php echo ncAmountFormat($output['assets_valuation']); ?></em></div>
                                    <?php if ($output['assets_valuation_type']){ ?>
                                        <div class="loan-exp">
                                            <span class="loan-plan-detail">Detail</span>
                                            <div class="loan-exp-wrap">
                                                <div class="pos">
                                                    <em class="triangle-up"></em>
                                                    <table class="loan-exp-table" style="width:300px;">
                                                        <tr class="t">
                                                            <td>Asset Type</td>
                                                            <td>Valuation</td>
                                                        </tr>
                                                        <?php foreach($output['assets_valuation_type'] as $assets_valuation){?>
                                                            <tr class="a">
                                                                <td><?php echo $certificationTypeEnumLangLang[$assets_valuation['asset_type']]; ?></td>
                                                                <td><?php echo ncAmountFormat($assets_valuation['assets_valuation']); ?></td>
                                                            </tr>
                                                        <?php }?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Evaluate Time</label></td>
                                <td><?php echo $output['credit_suggest']['create_time']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Suggest Credit </label></td>
                                <td><em style="font-weight: 600;font-size: 16px"><?php echo ncAmountFormat($output['credit_suggest']['suggest_credit']); ?></em></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Repayment Ability(Monthly)</label></td>
                                <td><em style="font-weight: 600;font-size: 16px"><?php echo ncAmountFormat($output['credit_suggest']['monthly_repayment_ability']); ?></em></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Business Profitability</label></td>
                                <td><span style="font-weight: 600;font-size: 16px"><?php echo 'None'; ?></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="verification-info">
                <div class="ibox-title" style="margin-top: 20px">
                    <h4>Check List</h4>
                </div>
                <div class="ibox-content">
                    <div class="activity-list clearfix">
                        <?php $verify_field = $output['verify_field']; $verifys = $output['verifys']; ?>
                        <?php foreach ($verify_field as $key => $value) { ?>
                            <div class="item">
                                <div>
                                    <?php echo $value; ?>
                                    <span>
                                        <?php if ($verifys[$key] == 1) { ?>
                                            <i class="fa fa-check" aria-hidden="true" style="font-size: 18px;color:green;"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-question" aria-hidden="true" style="font-size: 18px;color:red;"></i>
                                        <?php } ?>
                                        <i></i>
                                    </span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="base-info">
                <div class="ibox-title">
                    <h4>Client Info</h4>
                </div>
                <div class="ibox-content">
                    <div class="activity-list clearfix">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">CID</label></td>
                                <td><?php echo $info['obj_guid']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Name</label></td>
                                <td><?php echo $info['display_name']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Credit</label></td>
                                <td><?php echo $info['credit']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Account Type</label></td>
                                <td><?php echo 'Member'; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Phone</label></td>
                                <td><?php echo $info['phone_id']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Email</label></td>
                                <td><?php echo $info['email']; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.loan-exp').hover(function () {
            $(this).addClass('loan-exp-hover');
        }, function () {
            $(this).removeClass('loan-exp-hover');
        });
    });

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            var ele = $(element).closest('.form-group').find('.error_msg');
            error.appendTo(ele);
        },
        rules: {
            credit: {
                required: true
            },
            valid_time: {
                required: true
            },
            repayment_ability: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            credit: {
                required: '<?php echo 'Required';?>'
            },
            valid_time: {
                required: '<?php echo 'Required';?>'
            },
            repayment_ability: {
                required: '<?php echo 'Required';?>'
            },
            remark: {
                required: '<?php echo 'Required';?>'
            }
        }
    });
</script>
