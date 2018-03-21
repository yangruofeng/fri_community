<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Member Name';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Relationship';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Update Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']) {?>
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['display_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['phone_id'] ?><br/>
                </td>
                <td>
                    <?php echo $row['relation_type'] ?><br/>
                </td>
                <td>
                    <?php echo $row['relation_state'] == 0 ? 'Create' : 'Pass'; ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['update_time']) ?><br/>
                </td>
            </tr>
        <?php }?>
        <?php } else { ?>
            <tr>
                <td colspan="6">Null</td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>

