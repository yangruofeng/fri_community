<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Code';?></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Role';?></td>
            <td><?php echo 'Position';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('user', 'showUserDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['user_code'] ?></a><br/>
                </td>
                <td>
                    <?php echo $row['user_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['branch_name'] . ' ' . $row['depart_name'] ?><br/>
                </td>
                <td>
                    <?php $i = 0;foreach ($row['role_group'] as $role) { ++$i?>
                        <?php echo ($i == 1 ? '' : '/ ') . $role ?>
                    <?php } ?>
                </td>
                <td>
                    <?php $user_position = my_json_decode($row['user_position']);$i = 0;foreach ($user_position as $position) { ++$i?>
                        <?php echo ($i == 1 ? '' : '/ ') . ucwords(str_replace('_', ' ', $position)) ?>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row['user_status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
                <td>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editUser', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('user', 'deleteUser', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
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

