<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Bank</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('partner', 'addPartner', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <table class="table">
                    <thead>
                    <tr class="table-header">
                        <td><?php echo 'Bank Name'; ?></td>
                        <td><?php echo 'Bank Code'; ?></td>
                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                            <td><?php echo 'Balance(' . $key . ')'; ?></td>
                        <?php } ?>
                        <td><?php echo 'Last Check Time'; ?></td>
                        <td><?php echo 'Function'; ?></td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php foreach ($output['partner_list'] as $partner) { ?>
                        <tr>
                            <td>
                                <?php echo $partner['partner_name'] ?><br/>
                            </td>
                            <td>
                                <?php echo $partner['partner_code'] ?><br/>
                            </td>
                            <?php foreach ($partner['balance'] as $key => $balance) { ?>
                                <td>
                                    <?php echo ncAmountFormat($balance, false, $key) ?>
                                </td>
                            <?php } ?>
                            <td>
                                <?php echo timeFormat($partner['last_check_time']) ?><br/>
                            </td>
                            <td>
                                <a class="btn btn-default" style="padding: 6px 12px" href="<?php echo getUrl('partner', 'checkTrace', array('uid' => $partner['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <i class="fa fa-address-card-o"></i>
                                    Detail
                                </a>
                                <a class="btn btn-default" style="padding: 6px 12px" href="<?php echo getUrl('partner', 'editPartner', array('uid' => $partner['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <i class="fa fa-edit"></i>
                                    Edit
                                </a>
                                <a class="btn btn-default" style="padding: 6px 12px" href="<?php echo getUrl('partner', 'deletePartner', array('uid' => $partner['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <i class="fa fa-trash"></i>
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>