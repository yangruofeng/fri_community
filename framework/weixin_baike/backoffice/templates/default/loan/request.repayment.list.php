<style>
    .verify-table .locking {
        color: red;
        font-style: normal;
    }

    .verify-table .locking i {
        margin-right: 3px;
    }

</style>
<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Amount'; ?></td>
<!--            <td>--><?php //echo 'Payer Name'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Payer Account'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Bank Name'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Bank Account'; ?><!--</td>-->
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Create Time'; ?></td>
            <td><?php echo 'Handler'; ?></td>
<!--            <td>--><?php //echo 'Handle Remark'; ?><!--</td>-->
            <td><?php echo 'Handle Time'; ?></td>
            <td><?php echo 'Function'; ?></td>

        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['contract_sn'] . ' ' . $row['scheme_name']; ?>
                </td>
                <td>
                    <?php echo $row['type'] == 'schema' ? "Schema" : "Prepayment"; ?>
                </td>
                <td>
                    <?php echo $row['currency']; ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['amount'], false, $row['currency']); ?>
                </td>
<!--                <td>-->
<!--                    --><?php //echo $row['payer_name']; ?>
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php //echo $row['payer_account']; ?>
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php //echo $row['bank_name']; ?>
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php //echo $row['bank_account_no']; ?>
<!--                </td>-->
                <td>

                    <?php if ($data['state'] != requestRepaymentStateEnum::PROCESSING){
                        echo '<span>' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                        } elseif ($data['cur_uid'] == $row['handler_id']) {
                            echo '<span class="locking"><i class="fa fa-gavel"></i>' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                        } else {
                            echo '<span class="locking">' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                        }
                    ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?>
                </td>
                <td>
                    <?php echo $row['handler_name'] ?>
                </td>
<!--                <td>--><?php //echo $row['remark']; ?><!--</td>-->
                <td><?php echo timeFormat($row['handle_time']); ?></td>
                <td>

                    <?php if( $row['state'] == requestRepaymentStateEnum::SUCCESS ){ ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'viewRequestRepaymentDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-eye"></i>View</span>
                            </a>
                        </div>
                    <?php }else{ ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'auditRequestRepayment', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-check-circle-o"></i>Handle</span>
                            </a>
                        </div>
                    <?php } ?>

                    <?php /*if(in_array($row['state'],array(requestRepaymentStateEnum::CREATE,requestRepaymentStateEnum::PROCESSING))){ */?><!--
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php /*echo getUrl('loan', 'auditRequestRepayment', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) */?>">
                                <span><i class="fa fa-check-circle-o"></i>Handle</span>
                            </a>
                        </div>
                    <?php /*}*/?>
                    <?php /*if(in_array($row['state'],array(requestRepaymentStateEnum::FAILED,requestRepaymentStateEnum::SUCCESS))){ */?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php /*echo getUrl('loan', 'viewRequestRepaymentDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) */?>">
                                <span><i class="fa fa-eye"></i>View</span>
                            </a>
                        </div>
                    --><?php /*}*/?>

                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
