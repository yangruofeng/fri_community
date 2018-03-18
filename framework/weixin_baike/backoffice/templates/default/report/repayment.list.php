<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Amount';?></td>
            <td><?php echo 'Payer Name';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'Payer Phone';?></td>
            <td><?php echo 'Payer Account';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Teller Name';?></td>
            <td><?php echo 'Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['contract_sn'] . '(' . $row['scheme_name'] . ')' ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['amount']); ?>
                </td>
                <td>
                    <?php echo $row['payer_name']; ?>
                </td>
                <td>
                    <?php echo $row['payer_type']; ?>
                </td>
                <td>
                    <?php echo $row['payer_phone'];  ?>
                </td>
                <td>
                    <?php echo $row['payer_account'];  ?>
                </td>
                <td>
                    <?php echo $row['branch_name'];  ?>
                </td>
                <td>
                    <?php echo $row['teller_name'];  ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']);  ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

