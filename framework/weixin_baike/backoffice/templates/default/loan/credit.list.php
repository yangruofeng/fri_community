<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Multi Contract';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'Update Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
              <td>
                  <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['uid'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['obj_guid'] ?></a>
                  <br/>
              </td>
              <td>
                  <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['uid'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['display_name'] ?></a>
              </td>
              <td>
                  <?php echo $row['credit']?:0; ?><br/>
              </td>
              <td>
                  <?php echo ($row['allow_multi_contract'] == 1)?'Yes':'No'; ?><br/>
              </td>
              <td>
                  <?php switch ($row['account_type']) {
                    case 0:
                      echo 'member';
                      break;
                    case 10:
                      echo 'partner';
                      break;
                    case 20:
                      echo 'dealer';
                      break;
                    case 30:
                      echo 'Legal person';
                      break;
                    default:
                      echo 'member';
                      break;
                  }  ?><br/>
              </td>
              <td>
                  <?php echo timeFormat($row['update_time']) ?><br/>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="<?php echo $lang['common_delete'];?>" class="custom-btn custom-btn-primary" href="<?php echo getUrl('loan', 'editCredit', array('obj_guid'=>$row['obj_guid']), false, BACK_OFFICE_SITE_URL)?>" >
                        <span><i class="fa fa-send-o"></i>Credit</span>
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
