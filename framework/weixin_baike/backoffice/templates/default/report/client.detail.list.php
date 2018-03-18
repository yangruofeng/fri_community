<div class="important-info-1 clearfix">
    <div class="info">
        <p class="name">
            <?php echo $data['client_info']['display_name']?:$data['client_info']['login_code']?>
        </p>
        <p>
            <span>Credit Line:
                <span style="font-weight: 700"> <?php echo ncAmountFormat($data['client_info']['credit']) ?></span>
            </span>
            <span style="margin-left: 50px">Create Time: <?php echo timeFormat($data['client_info']['create_time'])?></span>
        </p>
    </div>
    <div class="statistical-report clearfix">
        <div class="item">
            Loan Number
            <p><?php echo $data['client_info']['loan_number']?:0?></p>
        </div>
        <div class="item">
            Loan Amount
            <p><?php echo ncAmountFormat($data['client_info']['loan_amount'])?></p>
        </div>
        <div class="item">
            Interest Amount
            <p><?php echo ncAmountFormat($data['client_info']['total_interest'])?></p>
        </div>
        <div class="item">
            Admin Fee Amount
            <p><?php echo ncAmountFormat($data['client_info']['total_admin_fee'])?></p>
        </div>
        <div class="item">
            Unpaid Amount
            <p><?php echo ncAmountFormat($data['client_info']['unpaid_amount'])?></p>
        </div>
    </div>
</div>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'SN'; ?></td>
            <td><?php echo 'Loan Product'; ?></td>
            <td><?php echo 'Apply Amount'; ?></td>
            <td><?php echo 'Disbursement Amount'; ?></td>
            <td><?php echo 'Receive Amount'; ?></td>
            <td><?php echo 'Received Amount'; ?></td>
            <td><?php echo 'Unreceived Amount'; ?></td>
            <td><?php echo 'Loan time'; ?></td>
            <td><?php echo 'State'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){?>
            <?php foreach($data['data'] as $row){?>
                <tr>
                    <td><?php echo $row['contract_sn']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo ncAmountFormat($row['apply_amount']); ?></td>
                    <td><?php echo ncAmountFormat($row['disbursement_amount']); ?></td>
                    <td><?php echo ncAmountFormat($row['receive_amount']); ?></td>
                    <td><?php echo ncAmountFormat($row['received_amount']); ?></td>
                    <td><?php echo ncAmountFormat($row['unreceived_amount']); ?></td>
                    <td><?php echo dateFormat($row['start_date']) . '--' . dateFormat($row['end_date']); ?></td>
                    <td>
                        <?php echo $lang['loan_contract_state_' . $row['state']]; ?>
                    </td>
                </tr>
            <?php }?>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

