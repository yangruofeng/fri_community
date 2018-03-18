<style>
    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    #coModal .modal-dialog {
        margin-top: 10px!important;
    }

    #coModal .easyui-panel {
        border: 1px solid #DDD;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>New Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'newClient', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <?php $client_info = $output['client_info']?>
    <div class="container">
        <form class="form-horizontal">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Member Icon</label></td>
                    <td>
                        <a target="_blank" href="<?php echo $client_info['member_icon'] ?>">
                            <img style="max-height: 200px;max-width: 100%" src="<?php echo $client_info['member_icon'] ?>">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">CID</label></td>
                    <td><?php echo $client_info['obj_guid'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Login Account</label></td>
                    <td><?php echo $client_info['login_code'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Display Name</label></td>
                    <td><?php echo $client_info['display_name'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Phone</label></td>
                    <td><?php echo $client_info['phone_id'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Email</label></td>
                    <td><?php echo $client_info['email'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Member Grade</label></td>
                    <td><?php echo $client_info['grade_code'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Member State</label></td>
                    <td><?php echo $lang['client_member_state_' . $client_info['member_state']];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Check State</label></td>
                    <td><?php echo $lang['operator_task_state_' .$client_info['operate_state']];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Register Time</label></td>
                    <td><?php echo timeFormat($client_info['create_time']);?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Verify State</label></td>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="verify_state" value="pass" checked/> Pass
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="verify_state" value="close"/> Close
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="verify_state" value="allot"/> Allot To Oc
                        </label>
                    </td>
                </tr>
                <tr id="credit_officer" style="display: none">
                    <td><label class="control-label">Credit Officer</label></td>
                    <td>
                        <span class="credit_officer_name"></span>
                        <input type="hidden" name="credit_officer_id" value=""/>
                        <button type="button" class="btn btn-primary" style="padding: 4px 10px" onclick="showSelectOc();"><i class="fa fa-arrow-right"></i><?php echo 'Select' ?></button>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Check Remark</label></td>
                    <td>
                        <textarea class="form-control" name="remark" style="width: 300px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <div class="custom-btn-group approval-btn-group">
                            <button type="button" class="btn btn-danger" onclick="checkSubmit();"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                            <button type="button" class="btn btn-info" onclick="checkAbandon();"><i class="fa fa-arrow-right"></i><?php echo 'Abandon' ?></button>
                            <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $client_info['uid']; ?>">
        </form>
    </div>
</div>

<div class="modal" id="coModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Credit Officer'?></h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="business-condition">
                    <form class="form-inline" id="frm_search_condition">
                        <table class="search-table">
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for co" style="min-width: 150px">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                                <i class="fa fa-search"></i>
                                                <?php echo 'Search'; ?>
                                            </button>
                                         </span>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control" name="branch_id">
                                        <option value="0">Select Branch</option>
                                        <?php foreach ($output['branch_list'] as $branch) { ?>
                                            <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="modal-table">
                    <div>
                        <table class="table table-bordered">
                            <thead>
                            <tr class="table-header" style="background-color: #EEE">
                                <td>Branch</td>
                                <td>Co Name</td>
                                <td>Tasks(Client)</td>
                                <td>Tasks(Request Loan)</td>
                                <td>Function</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=2"></script>
<script>
    $(function () {
        $('input[name="verify_state"]').click(function () {
            var verify_state = $(this).val();
            if (verify_state == 'allot') {
                $('#credit_officer').show();
            } else {
                $('#credit_officer').hide();
            }
        })
    })

    function showSelectOc() {
        btn_search_onclick();
        $('#coModal').modal('show');
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();
        var _branch_id = $('select[name="branch_id"]').val();
        yo.dynamicTpl({
            tpl: "operator/co.list",
            dynamic: {
                api: "operator",
                method: "getCoList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text,
                    branch_id: _branch_id
                }
            },
            callback: function (_tpl) {
                $("#coModal .modal-table").html(_tpl);
            }
        });
    }

    function selectOc(co_id, co_name) {
        $('#credit_officer .credit_officer_name').text(co_name);
        $('#credit_officer input[name="credit_officer_id"]').val(co_id);
        $('#coModal').modal('hide');
    }

    function checkSubmit(){
        if (!$(".form-horizontal").valid()) {
            return;
        }
        var values = $(".form-horizontal").getValues();
        submitCheck(values);
    }

    function checkAbandon() {
        var values = $(".form-horizontal").getValues();
        values.verify_state = 'abandon';
        submitCheck(values);
    }

    function submitCheck(values){
        yo.loadData({
            _c: 'operator',
            _m: 'submitCheckClient',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.href = '<?php echo getUrl('operator', 'newClient', array(), false, BACK_OFFICE_SITE_URL);?>';
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: true
            },
            credit_officer_id: {
                checkRequired: true
            }
        },
        messages: {
            remark: {
                required: '<?php echo 'Required'?>'
            },
            credit_officer_id: {
                checkRequired: '<?php echo 'Required'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        var verify_state = $('input[name="verify_state"]:checked').val();
        if (verify_state == 'allot' && !value) {
            return false;
        } else {
            return true;
        }
    });
</script>
