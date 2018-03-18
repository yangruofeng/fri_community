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
            <td><?php echo 'Apply Time';?></td>
            <td><?php echo 'Apply Source';?></td>
            <td><?php echo 'State';?></td>
            <?php if ($data['type'] == 'processed') { ?>
                <td><?php echo 'Credit Officer';?></td>
                <td><?php echo 'Audit Remark';?></td>
                <td><?php echo 'Audit Time';?></td>
            <?php } ?>
            <td><?php echo 'Handler';?></td>
            <?php if ($data['type'] != 'processed') { ?>
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
                    <?php echo timeFormat($row['apply_time'])?>
                </td>
                <td>
                    <?php echo $loanApplySourceLang[$row['request_source']]; ?>
                </td>
                <td>
                    <?php echo $loanApplyStateLang[$row['state']]; ?>
                    <?php /*if($row['state'] == applyStateEnum::PROCESSING){
                      if($data['cur_uid'] == $row['handler_id']){
                        echo '<span class="locking"><i class="fa fa-gavel"></i>'.$lang['apply_state_' . $row['state']].'</span>';
                      }else{
                        echo '<span class="locking">'.$lang['apply_state_' . $row['state']].'</span>';
                      }

                          }else{
                            echo $lang['apply_state_' . $row['state']];
                          }
                    */?>
                </td>
                <?php if ($data['type'] == 'processed') { ?>
                    <td><?php echo $row['user_name']?:$row['user_code'];?></td>
                    <td><?php echo $row['operator_remark'];?></td>
                    <td><?php echo timeFormat($row['update_time']);?></td>
                <?php } ?>
                <td>
                    <?php echo $row['handler_name']?>
                </td>
                <?php if ($data['type'] != 'processed') { ?>
                    <td>
                        <?php if ($row['state'] == loanApplyStateEnum::CREATE || $row['state'] == loanApplyStateEnum::LOCKED) { ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'operatorAuditApply', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-check-circle-o"></i>Handle</span>
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
