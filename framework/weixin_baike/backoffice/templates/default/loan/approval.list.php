<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Before Credit';?></td>
            <td><?php echo 'Approval Credit';?></td>
            <td><?php echo 'Repayment Ability';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
              <td>
                  <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['obj_guid'] ?></a>
              </td>
              <td>
                  <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['display_name'] ?></a>
              </td>
              <td>
                  <?php echo $row['before_credit'] ?>
              </td>
              <td>
                  <?php echo $row['current_credit'] ?>
              </td>
              <td>
                  <?php echo $row['approval_repayment_ability'] ?>
              </td>
              <td>
                  <?php echo $row['remark'] ?>
              </td>
              <td>
                  <?php if($row['type'] == 0){echo 'First Credit';}elseif($row['type'] == 1){echo 'Raise';}else{echo 'Down';}  ?>
              </td>
              <td>
                <?php if($row['state'] == 0){
                  echo '<span class="locking">Auditings</span>';
                }elseif($row['state']==1){
                  echo 'Passed';
                }else{
                  echo 'Refuse';
                } ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'approvalEdit', array('uid'=>$row['a_uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <span><i class="fa  fa-vcard-o"></i>Audit</span>
                    </a>
                  </div>
              </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
