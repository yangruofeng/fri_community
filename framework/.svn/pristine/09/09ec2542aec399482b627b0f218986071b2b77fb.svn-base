<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Receive Account</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('financial', 'addReceiveAccount', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <div>
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td><?php echo 'Bank Name';?></td>
                            <td><?php echo 'Account No';?></td>
                            <td><?php echo 'Account Name';?></td>
                            <td><?php echo 'Account Phone';?></td>
                            <td><?php echo 'Currency';?></td>
                            <td><?php echo 'State';?></td>
                            <td><?php echo 'Function';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php $i = 0;foreach ($output['account_list'] as $key => $row) { ++$i;?>
                            <tr>
                                <td>
                                    <?php echo $i; ?><br/>
                                </td>
                                <td>
                                    <?php echo $row['bank_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['bank_account_no']; ?>
                                </td>
                                <td>
                                    <?php echo $row['bank_account_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['bank_account_phone']; ?>
                                </td>
                                <td>
                                    <?php echo $row['currency']; ?>
                                </td>
                                <td>
                                    <?php echo $row['account_state'] == 1 ? 'Valid' : 'Invalid'; ?>
                                </td>
                                <td>
                                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('financial', 'editReceiveAccount', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>" style="margin-right: 5px" >
                                        <i class="fa fa-edit"></i>
                                        Edit
                                    </a>
                                    <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('financial', 'deleteReceiveAccount', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL); ?>">
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>