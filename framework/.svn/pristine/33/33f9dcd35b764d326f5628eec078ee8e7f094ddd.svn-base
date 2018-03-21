<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Icon'; ?></td>
            <td><?php echo 'CID'; ?></td>
            <td><?php echo 'Name'; ?></td>
            <td><?php echo 'Phone'; ?></td>
            <td><?php echo 'Email'; ?></td>
            <td><?php echo 'Account Type'; ?></td>
            <td><?php echo 'State'; ?></td>
            <?php if ($data['verify_state'] != newMemberCheckStateEnum::CREATE) { ?>
            <td><?php echo 'Operator'; ?></td>
            <?php }?>
            <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                <td><?php echo 'Operator Remark'; ?></td>
            <?php } ?>
            <?php if ($data['verify_state'] == newMemberCheckStateEnum::ALLOT) { ?>
                <td><?php echo 'Credit Officer'; ?></td>
            <?php } ?>
            <td><?php echo 'Create Time'; ?></td>
            <?php if (in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                <td><?php echo 'Function'; ?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php if ($row['member_icon']) { ?>
                            <a href="<?php echo $row['member_icon'] ?>">
                                <img class="avatar-icon" src="<?php echo $row['member_icon'] ?>">
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $row['obj_guid'] ?>
                    </td>
                    <td>
                        <?php echo $row['display_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
                    <td>
                        <?php echo $row['email'] ?>
                    </td>
                    <td>
                        <?php if ($row['account_type'] == 0) {
                            echo 'Member';
                        } ?>
                    </td>
                    <td>
                        <?php echo $lang['operator_task_state_' . $row['operate_state']] ?>
                    </td>
                    <?php if ($data['verify_state'] != newMemberCheckStateEnum::CREATE) { ?>
                        <td>
                            <?php echo $row['operator_name'] ?>
                        </td>
                    <?php } ?>
                    <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                        <td>
                            <?php echo $row['operate_remark'] ?>
                        </td>
                    <?php } ?>
                    <?php if ($data['verify_state'] == newMemberCheckStateEnum::ALLOT) { ?>
                        <td>
                            <?php echo $row['co_name'] ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php echo timeFormat($row['create_time']); ?>
                    </td>
                    <?php if (in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                        <td>
                            <?php if($row['operate_state'] == newMemberCheckStateEnum::CREATE || ($row['operate_state'] == newMemberCheckStateEnum::LOCKED && $row['operator_id'] == $data['current_user'])){?>
                                <div class="custom-btn-group">
                                    <a class="custom-btn custom-btn-secondary"
                                       href="<?php echo getUrl('operator', 'checkNewClient', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <span><i class="fa fa-vcard-o"></i><?php echo $row['operate_state'] == newMemberCheckStateEnum::CREATE ? 'Get' : 'Handle';?></span>
                                    </a>
                                </div>
                            <?php }?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20">
                    Null
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
