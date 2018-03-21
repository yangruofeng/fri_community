<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Before Credit';?></td>
            <td><?php echo 'Approval Credit';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Operator';?></td>
            <td><?php echo 'Operate Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="#" onclick="search_name('<?php echo $row['display_name'] ?>')"><?php echo $row['display_name'] ?></a>
                </td>
                <td>
                    <?php echo $row['before_credit']; ?>
                </td>
                <td>
                    <?php echo $row['current_credit']; ?>
                </td>
                <td>
                    <?php echo $lang['loan_credit_type_' . $row['type']];  ?>
                </td>
                <td>
                    <?php echo $row['remark']; ?>
                </td>
                <td>
                    <?php echo $lang['loan_credit_state_' . $row['state']];  ?>
                </td>
                <td><?php echo $row['user_name'] ;?></td>
                <td><?php echo timeFormat($row['operate_time']) ;?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

