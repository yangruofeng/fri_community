<style>
    .product-info {
        width: 98%;
        background: #FFF;
        margin: 15px;
    }

    .product-info .info {
        padding: 15px;
        position: relative;
        height: 85px;
        /*border-bottom: 1px solid #e7eaec;*/
    }

    .product-info .name {
        font-size: 20px;
        font-weight: 600;
    }

    .product-info .product-report {
        height: 90px;
    }

    .product-info .product-report .item {
        width: 25%;
        text-align: center;
        float: left;
        padding: 20px 0;
        max-height: 110px;
        border-top: 1px solid #e7eaec;
        border-right: 1px solid #e7eaec;
        font-weight: 600;
    }

    .product-info .product-report .item:nth-child(4n) {
        border-right: 0;
    }

    .product-info .product-report .item p {
        margin-bottom: 0;
        font-size: 20px;
        margin-top: 5px;
        color: #f60;
    }

    .product-info .custom-btn-group {
        position: absolute;
        right: 10px;
        top: 20px;
    }

    .custom-btn-group a {
        margin-left: 8px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'addProduct', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <?php foreach($output['product_list'] as $product){?>
                <div class="product-info">
                    <div class="info">
                        <div style="width: 60%;float: left">
                        <p class="name"><a style="text-decoration: none" href="<?php echo getUrl('loan', 'editProduct', array('uid' => $product['uid']), false, BACK_OFFICE_SITE_URL)?>"><?php echo $product['product_name']?></a></p>
                        <p>
                            <span>Code:  <?php echo $product['product_code']?></span>
                            <span style="margin-left: 50px">State:  <?php echo $lang['enum_loan_product_state_'.$product['state']]?></span>
                        </p>
                        </div>
                        <div class="custom-btn-group">
                            <?php if ($product['valid_id']) {?>
                                <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'showProduct', array('uid' => $product['valid_id'], 'is_edit' => false), false, BACK_OFFICE_SITE_URL)?>">
                                    <span><i class="fa fa-check-square-o"></i>Valid Version</span>
                                </a>
                            <?php }?>
                            <?php if ($product['count'] > 1) {?>
                                <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'showProductHistory', array('uid' => $product['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                    <span><i class="fa fa-reorder"></i>History Versions</span>
                                </a>
                            <?php }?>
                            <?php if ($product['state'] == 10) {?>
                                <a class="custom-btn custom-btn-secondary" href="javascrript:;" onclick="release_product('<?php echo $product['uid']?>')">
                                    <span><i class="fa fa-long-arrow-up"></i>Active</span>
                                </a>
                            <?php }?>
                            <?php if ($product['state'] == 20) {?>
                                <a class="custom-btn custom-btn-secondary" href="javascrript:;" onclick="unshelve_product('<?php echo $product['uid']?>')">
                                    <span><i class="fa fa-long-arrow-down"></i>Inactive</span>
                                </a>
                            <?php }?>
                            <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'editProduct', array('uid' => $product['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-edit"></i>Edit</span>
                            </a>
                        </div>
                    </div>
                    <div class="product-report clearfix">
                        <div class="item">
                            Loan Contract
                            <p><?php echo $product['loan_contract']?></p>
                        </div>
                        <div class="item">
                            Loan Client
                            <p><?php echo $product['loan_client']?></p>
                        </div>
                        <div class="item">
                            Loan Principal
                            <p><?php echo ncAmountFormat($product['loan_ceiling'])?></p>
                        </div>
                        <div class="item">
                            Loan Receive
                            <p><?php echo ncAmountFormat($product['loan_balance'])?></p>
                        </div>
                    </div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<script>
    function release_product(_uid) {
        if (!_uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseProduct",
            param: {uid: _uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function unshelve_product(_uid) {
        if (!_uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "unShelveProduct",
            param: {uid: _uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
