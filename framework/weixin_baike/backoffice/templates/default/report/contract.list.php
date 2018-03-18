<div class="important-info clearfix">
    <div class="item">
        <p>Apply Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['amount']['apply_amount'])?></div>
    </div>
    <div class="item">
        <p>Disbursement Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['amount']['disbursement_amount'])?></div>
    </div>
    <div class="item">
        <p>Receive Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['amount']['receive_amount'])?></div>
    </div>
    <div class="item">
        <p>Received Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['amount']['received_amount'])?></div>
    </div>
    <div class="item">
        <p>Unreceived Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['amount']['unreceived_amount'])?></div>
    </div>
</div>

<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'SN'; ?></td>
            <td><?php echo 'Member'; ?></td>
            <td><?php echo 'Product Name'; ?></td>
            <td><?php echo 'Apply Amount'; ?></td>
            <td><?php echo 'Disbursement Amount'; ?></td>
            <td><?php echo 'Receive Amount'; ?></td>
            <td><?php echo 'Received Amount'; ?></td>
            <td><?php echo 'Unreceived Amount'; ?></td>
            <td><?php echo 'Loan time'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){?>
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td><?php echo $row['contract_sn']; ?></td>
                <td><?php echo $row['display_name']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo ncAmountFormat($row['apply_amount']); ?></td>
                <td><?php echo ncAmountFormat($row['disbursement_amount']); ?></td>
                <td><?php echo ncAmountFormat($row['receive_amount']); ?></td>
                <td><?php echo ncAmountFormat($row['received_amount']); ?></td>
                <td><?php echo ncAmountFormat($row['unreceived_amount']); ?></td>
                <td><?php echo dateFormat($row['start_date']) . '--' . dateFormat($row['end_date']); ?></td>
                <td>
                    <?php echo $lang['loan_contract_state_' . $row['state']]; ?> </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="#" >
                            <span><i class="fa  fa-search"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
            <tr style="font-weight: 700">
                <td colspan="2"><?php echo 'Current Total'; ?></td>
                <td><?php echo ncAmountFormat($data['current_amount']['apply_amount']); ?></td>
                <td><?php echo ncAmountFormat($data['current_amount']['disbursement_amount']); ?></td>
                <td><?php echo ncAmountFormat($data['current_amount']['receive_amount']); ?></td>
                <td><?php echo ncAmountFormat($data['current_amount']['received_amount']); ?></td>
                <td><?php echo ncAmountFormat($data['current_amount']['unreceived_amount']); ?></td>
                <td colspan="3"></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

