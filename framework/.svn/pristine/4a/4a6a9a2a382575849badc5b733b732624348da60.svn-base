<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .btn-release,.btn-unshelve{
        position: absolute;
        top: 0px;
        right: 0px;
        height: 30px;
        line-height: 30px;
        padding: 0px 15px;
    }
    .base-info .size-info .content{
        overflow: auto;
        padding: 5px 0 10px;
        margin: 5px 15px;
    }
</style>
<?php $product_info = $output['product_info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'addProduct', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>Info</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <ul class="tab-top clearfix">
            <li class="active" page="page-1"><a>Base Info</a></li>
            <li page="page-2"><a>Condition</a></li>
            <li page="page-3"><a>Details</a></li>
        </ul>
        <button class="btn btn-default btn-release" style="display:<?php echo $product_info['state'] == 30 ? 'block;' : 'none;' ?>">Putaway</button>
        <button class="btn btn-default btn-unshelve" style="display:<?php echo $product_info['state'] == 20 ? 'block;' : 'none;' ?>">Inactive</button>
        <div class="page-1">
            <div class="base-info clearfix">
                <div class="product-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Loan Info</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div class="wrap1">
                            <table>
                                <tr style="font-weight: bold">
                                    <input type="hidden" id="uid" value="<?php echo $product_info['uid'];?>">
                                    <td>Product Name：</td>
                                    <td id="product_name"><?php echo $product_info['product_name']?></td>
                                </tr>

                                <tr>
                                    <td>Is credit loan: </td>
                                    <td id="is_credit_loan" val="<?php echo $product_info['is_credit_loan']; ?>"><?php  if( isset($product_info['is_credit_loan']) ){ echo $product_info['is_credit_loan']?'YES':'NO'; }  ?></td>
                                </tr>

                                <tr>
                                    <td>Advance Interest：</td>
                                    <td id="is_advance_interest" val="<?php echo $product_info['is_advance_interest']?>"><?php echo (isset($product_info['is_advance_interest'])?($product_info['is_advance_interest']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Editable Grace Days：</td>
                                    <td id="is_editable_grace_days" val="<?php echo $product_info['is_editable_grace_days']?>"><?php echo (isset($product_info['is_editable_grace_days'])?($product_info['is_editable_grace_days']==1?'YES':'NO'):'')?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="wrap2">
                            <table>
                                <tr style="font-weight: bold">
                                    <td>Product Code：</td>
                                    <td id="product_code"><?php echo $product_info['product_code']?></td>
                                </tr>
                                <tr>
                                    <td>Multi Contract：</td>
                                    <td id="is_multi_contract" val="<?php echo $product_info['is_multi_contract']?>"><?php echo (isset($product_info['is_multi_contract'])?($product_info['is_multi_contract']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Editable Interest：</td>
                                    <td id="is_editable_interest" val="<?php echo $product_info['is_editable_interest']?>"><?php echo (isset($product_info['is_editable_interest'])?($product_info['is_editable_interest']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>State：</td>
                                    <td id="state" val="<?php echo $product_info['state']?>"><?php echo $lang['enum_loan_product_state_'.$product_info['state']]?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="penalty-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Penalty</h5></div>
                    </div>
                    <div class="content">
                        <table>
                            <tr>
                                <td>Penalty On：</td>
                                <td id="penalty_on" val="<?php echo $product_info['penalty_on']?>"><?php echo ucwords(strtolower($output['penalty_on'][$product_info['penalty_on']]))?></td>
                            </tr>
                            <tr>
                                <td>Penalty Rate：</td>
                                <td id="penalty_rate" val="<?php echo $product_info['penalty_rate'] > 0 ? $product_info['penalty_rate'] : ""?>"><?php echo $product_info['penalty_rate'] > 0 ? ($product_info['penalty_rate'] . '%') : ''?></td>
                            </tr>
                            <tr>
                                <td>Divisor Days：</td>
                                <td id="penalty_divisor_days" val="<?php echo $product_info['penalty_divisor_days'] > 0 ? $product_info['penalty_divisor_days'] : ""?>"><?php echo $product_info['penalty_divisor_days'] > 0 ? ($product_info['penalty_divisor_days'].'Days'):''?></td>
                            </tr>
                            <tr>
                                <td>Editable：</td>
                                <td id="is_editable_penalty" val="<?php echo $product_info['penalty_on']?$product_info['is_editable_penalty']:''?>"><?php echo $product_info['penalty_on']?($product_info['is_editable_penalty']==1?'YES':'NO'):''?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="size-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Size Rate</h5></div>
                    </div>
                    <div class="content clearfix">

                    </div>
                </div>
            </div>
        </div>
        <div class="base-info clearfix page-2">
            <div class="condition-info">
                <div class="ibox-title">
                    <div class="col-sm-8"><h5>Condition</h5></div>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="condition_form">
                        <?php
                        $condition = $product_info['condition'];
                        $condition_new = array();
                        foreach ($condition as $val) {
                            $condition_new[] = $val['definition_category'] . ',' . $val['definition_id'];
                        }
                        ?>
                        <?php foreach ($output['condition_list'] as  $key => $condition) { ?>
                            <div class="form-group col-sm-6">
                                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $output['condition_arr'][$key] ?></label>
                                <div class="col-sm-9 checkbox-div">
                                    <?php foreach($condition as $val){?>
                                        <label class="col-sm-4"><input type="checkbox" <?php echo in_array($key . ',' . $val['uid'], $condition_new)?'checked':''?> name="<?php echo $key . ',' . $val['uid'] ?>"><?php echo $val['item_name']?></label>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="page-3">
            <div class="base-info clearfix">
                <div class="description">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Description</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_description']?></div>
                        <textarea name="description" id="description" style="display: none;"><?php echo $product_info['product_description']?></textarea>
                    </div>
                </div>
                <div class="qualification">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Qualification</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_qualification']?></div>
                        <textarea name="qualification" id="qualification" style="display: none;"><?php echo $product_info['product_qualification']?></textarea>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="feature">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Feature</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_feature']?></div>
                        <textarea name="feature" id="feature" style="display: none;"><?php echo $product_info['product_feature']?></textarea>
                    </div>
                </div>
                <div class="required">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Required</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_required']?></div>
                        <textarea name="required" id="required" style="display: none;"><?php echo $product_info['product_required']?></textarea>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="notice">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Notice</h5></div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_required']?></div>
                        <textarea name="notice" id="notice" style="display: none;"><?php echo $product_info['product_notice']?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        var height = $('.product-info .content').height();
        $('.penalty-info .content').height(height);
        $('.tab-top li').click(function () {
            var _page = $(this).attr('page');
            $('.tab-top li').removeClass('active');
            $(this).addClass('active');
            $('.page-1,.page-2,.page-3').hide();
            $('.' + _page).show();
        })
    });

    var uid = '<?php echo intval($product_info['uid'])?>';
    if (uid != 0) {
        getSizeRateList(uid)
    }

    function getSizeRateList(product_id) {
        if(product_id == 0) return;
        yo.dynamicTpl({
            tpl: "loan/size_rate.list",
            dynamic: {
                api: "loan",
                method: "getSizeRateList",
                param: {product_id: product_id, type: 'info'}
            },
            callback: function (_tpl) {
                $(".size-info .content").html(_tpl);
            }
        });
    }

    $('.btn-release').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-unshelve').show();
                    $('#state').html('Valid');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('.btn-unshelve').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "unShelveProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-release').show();
                    $('#state').html('Invalid');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })
</script>
