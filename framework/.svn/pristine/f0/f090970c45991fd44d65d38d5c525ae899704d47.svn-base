<style>
  .verify-table .locking {
    color: red;
    font-style: normal;
  }
  .verify-table .locking i {
    margin-right: 3px;
  }
</style>
<?php
$loanApplySourceLang = enum_langClass::getLoanApplySourceLang();
$loanApplyStateLang = enum_langClass::getLoanApplyStateLang();
?>
<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Loan Product';?></td>
            <td><?php echo 'Applied Amount';?></td>
            <td><?php echo 'Loan Purpose';?></td>
            <td><?php echo 'Loan Mortgage';?></td>
            <td><?php echo 'Contact Phone';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Apply Source';?></td>
            <td><?php echo 'Apply Time';?></td>
            <?php if ($data['verify_state'] != loanApplyStateEnum::CREATE) { ?>
                <td><?php echo 'Operator';?></td>
            <?php } ?>
            <?php if ($data['verify_state'] > loanApplyStateEnum::CREATE) { ?>
                <td><?php echo 'Operator Remark';?></td>
            <?php } ?>
            <?php if ($data['verify_state'] > loanApplyStateEnum::OPERATOR_REJECT) { ?>
                <td><?php echo 'Credit Officer';?></td>
            <?php } ?>
            <?php if ($data['verify_state'] < loanApplyStateEnum::OPERATOR_REJECT) { ?>
                <td><?php echo 'Function';?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php if( $row['member_id'] ){ ?>
                        <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['applicant_name'] ?></a>
                    <?php }else{ ?>
                        <span><?php echo $row['applicant_name'] ?></span>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row['product_name']?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['apply_amount'])?>
                </td>
                <td>
                    <?php echo $row['loan_purpose']?>
                </td>
                <td>
                    <?php echo $row['mortgage']; ?>
                </td>
                <td>
                    <?php echo $row['contact_phone']?>
                </td>
                <td>
                    <?php echo $loanApplyStateLang[$row['state']]; ?>
                </td>
                <td>
                    <?php echo $loanApplySourceLang[$row['request_source']]; ?>
                </td>
                <td>
                    <?php echo timeFormat($row['apply_time'])?>
                </td>
                <?php if ($data['verify_state'] != loanApplyStateEnum::CREATE) { ?>
                    <td>
                        <?php echo $row['operator_name']?>
                    </td>
                <?php } ?>
                <?php if ($data['verify_state'] > loanApplyStateEnum::CREATE) { ?>
                    <td>
                        <?php echo $row['operator_remark']?>
                    </td>
                <?php } ?>
                <?php if ($data['verify_state'] > loanApplyStateEnum::OPERATOR_REJECT) { ?>
                    <td><?php echo $row['co_name']; ?></td>
                <?php } ?>
                <?php if ($data['verify_state'] < loanApplyStateEnum::OPERATOR_REJECT) { ?>
                    <td>
                        <?php if ($row['state'] == loanApplyStateEnum::CREATE || $row['operator_id'] == $data['current_user']) { ?>
                            <div class="custom-btn-group">
                                <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('operator', 'operateRequestLoan', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                    <span><i class="fa fa-check-circle-o"></i><?php echo $row['state'] == loanApplyStateEnum::CREATE ? 'Get' : 'Handle'?></span>
                                </a>
                            </div>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
