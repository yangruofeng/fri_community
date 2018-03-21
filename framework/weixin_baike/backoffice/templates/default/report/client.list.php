<div class="important-info clearfix">
    <div class="item">
        <p>Client Quantity</p>
        <div class="c"><?php echo $data['statistics']['client_quantity']?:0?></div>
    </div>
    <div class="item">
        <p>Credit Line(AVG)</p>
        <div class="c"><?php echo ncAmountFormat($data['statistics']['avg_credit'])?></div>
    </div>
    <div class="item">
        <p>Loan Number(AVG)</p>
        <div class="c"><?php echo $data['statistics']['avg_loan_number']?:0?></div>
    </div>
    <div class="item">
        <p>Loan Amount(AVG)</p>
        <div class="c"><?php echo ncAmountFormat($data['statistics']['avg_loan_amount'])?></div>
    </div>
    <div class="item">
        <p>New Client</p>
        <div class="c"><?php echo $data['statistics']['new_client'] ?: 0?></div>
    </div>
</div>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Cid'; ?></td>
            <td><?php echo 'Name'; ?></td>
            <td><?php echo 'Credit Line(Used)'; ?></td>
            <td><?php echo 'Loan Number'; ?></td>
            <td><?php echo 'Loan Amount'; ?></td>
            <td><?php echo 'Interest Amount'; ?></td>
            <td><?php echo 'Admin Fee Amount'; ?></td>
            <td><?php echo 'Unpaid Amount'; ?></td>
            <td><?php echo 'Create Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['obj_guid'] ?><br/>
                </td>
                <td>
                    <?php echo $row['display_name'] ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['credit']) . '(' . ncAmountFormat($row['credit'] - $row['receivable_principal']) . ')' ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['loan_number']?:0 ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['loan_amount']) ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['total_interest']) ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['total_admin_fee']) ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['unpaid_amount']) ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('report', 'clientDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-vcard-o"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

