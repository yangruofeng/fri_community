<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Branch Code';?></td>
            <td><?php echo 'Branch Name';?></td>
            <td><?php echo 'Manager';?></td>
            <td><?php echo 'Limit Loan';?></td>
            <td><?php echo 'Limit Deposit';?></td>
            <td><?php echo 'Limit Exchange';?></td>
            <td><?php echo 'Limit Withdraw';?></td>
            <td><?php echo 'Limit Transfer';?></td>
            <td><?php echo 'Contact Phone';?></td>
            <td><?php echo 'Address';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['branch_code'] ?><br/>
                </td>
                <td>
                    <?php echo $row['branch_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['user_code'] ?><br/>
                </td>
                <td>
                    <?php echo $row['limit_arr']['limit_loan']['max_per_time'] ? $row['limit_arr']['limit_loan']['max_per_time'] . '$ Per Time' : '' ?>
                    <br/>
                    <?php echo $row['limit_arr']['limit_loan']['max_per_time'] ? $row['limit_arr']['limit_loan']['max_per_day'] . '$ Per Day' : '' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['limit_arr']['limit_deposit']['max_per_time'] ? $row['limit_arr']['limit_deposit']['max_per_time'] . '$ Per Time' : '' ?>
                    <br/>
                    <?php echo $row['limit_arr']['limit_deposit']['max_per_time'] ? $row['limit_arr']['limit_deposit']['max_per_day'] . '$ Per Day' : '' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['limit_arr']['limit_exchange']['max_per_time'] ? $row['limit_arr']['limit_exchange']['max_per_time'] . '$ Per Time' : '' ?>
                    <br/>
                    <?php echo $row['limit_arr']['limit_exchange']['max_per_time'] ? $row['limit_arr']['limit_exchange']['max_per_day'] . '$ Per Day' : '' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['limit_arr']['limit_withdraw']['max_per_time'] ? $row['limit_arr']['limit_withdraw']['max_per_time'] . '$ Per Time' : '' ?>
                    <br/>
                    <?php echo $row['limit_arr']['limit_withdraw']['max_per_time'] ? $row['limit_arr']['limit_withdraw']['max_per_day'] . '$ Per Day' : '' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['limit_arr']['limit_transfer']['max_per_time'] ? $row['limit_arr']['limit_transfer']['max_per_time'] . '$ Per Time' : '' ?>
                    <br/>
                    <?php echo $row['limit_arr']['limit_transfer']['max_per_time'] ? $row['limit_arr']['limit_transfer']['max_per_day'] . '$ Per Day' : '' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['contact_phone'] ?><br/>
                </td>
                <td>
                    <?php echo $row['address_region'] . ' ' . $row['address_detail'] ?><br/>
                </td>
                <td>
                    <?php echo $row['status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
                <td>
                    <a title="<?php echo 'Department' ;?>" href="<?php echo getUrl('user', 'department', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Department
                    </a>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editBranch', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('user', 'deleteBranch', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                        <i class="fa fa-trash"></i>
                        Delete
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

