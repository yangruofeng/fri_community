<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Loan Name';?></td>
            <td><?php echo 'loan Code';?></td>
            <td><?php echo 'Version Number';?></td>
            <td><?php echo 'Contract Number';?></td>
            <td><?php echo 'Loan Client';?></td>
            <td><?php echo 'Principal Amount';?></td>
            <td><?php echo 'Interest Amount';?></td>
            <td><?php echo 'Admin Fee';?></td>
            <td><?php echo 'Unreceived';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['product_name'] ?>
                </td>
                <td>
                    <?php echo $row['product_code']; ?>
                </td>
                <td>
                    <?php echo $row['count']; ?>
                </td>
                <td>
                    <?php echo $row['loan_contract']; ?>
                </td>
                <td>
                    <?php echo $row['loan_client'];  ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['loan_ceiling']); ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['loan_interest']); ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['admin_fee']); ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['loan_balance']);?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

