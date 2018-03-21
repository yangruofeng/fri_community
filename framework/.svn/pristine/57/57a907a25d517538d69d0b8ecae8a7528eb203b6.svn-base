
<style>
    .approval-btn-group {
      float: inherit;
    }
    .custom-btn-pass {
      color: #28B779;
    }
    .color28B779 {
      color: #28B779;
    }
    .colorcc0000 {
      color: #cc0000;
    }
    .custom-btn-refuse {
      margin: 0 10px!important;
      color: #cc0000;
    }
    .approval-base-info {
      width: 100%;
    }
    .approval-history {
      width: 100%;
      background-color: #fff;
      margin-top: 20px;
    }
    .approval-history .ibox-content {
      padding: 0;
    }

</style>
<?php $info = $output['info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Approval</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'credit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container clearfix">
      <div class="approval-base-info">
        <table class="table">
          <tbody class="table-body">
            <tr>
              <td><label class="control-label">GUID</label></td><td><?php echo $info['obj_guid'];?></td>
              <td><label class="control-label">Name</label></td><td><?php echo $info['display_name'];?></td>
            </tr>
            <tr>
              <td><label class="control-label">Before Credit</label></td><td><?php echo $info['before_credit'];?></td>
              <td><label class="control-label">Apply Credit</label></td><td><?php echo $info['current_credit'];?></td>
            </tr>


            <tr>
                <td><label class="control-label">Credit Valid Time</label></td><td><?php echo $info['valid_time'].' '.$info['valid_time_unit'];?></td>
                <td><label class="control-label">Monthly Repayment Ability</label></td><td>$<?php echo $info['repayment_ability'];?></td>
            </tr>



            <tr>
                <td><label class="control-label">Creator</label></td><td><?php echo $info['creator_name'];?></td>
                <td><label class="control-label">Create Time</label></td><td><?php echo timeFormat($info['create_time']);?></td>
            </tr>

           <!-- <tr>
              <td><label class="control-label">Phone</label></td><td><?php /*echo $info['phone_id'];*/?></td>
              <td><label class="control-label">Email</label></td><td><?php /*echo $info['email'];*/?></td>
            </tr>-->

            <tr>
              <td><label class="control-label">Apply Type</label></td><td><?php if($info['type'] == 0){echo 'First Credit';}elseif($info['type'] == 1){echo 'Raise';}else{echo 'Down';}  ?></td>
              <td><label class="control-label">State</label></td><td><?php if($info['state'] == 0){echo 'Auditing';}elseif($info['state']==1){echo 'Passed';}else{echo 'Refuse';} ?></td>
            </tr>


            <tr>
                <td><label class="control-label">Remark</label></td><td><?php echo $info['remark'];?></td>
                <td colspan="2"></td>

            </tr>


            <tr>
              <td colspan="4" style="text-align: center;">
                <?php if($info['state']==1){?>
                  <span class="color28B779">Passed</span>
                  <div class="custom-btn-group approval-btn-group">
                    <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                    <button type="button" class="btn btn-success" onclick="javascript:window.location.href='<?php echo getUrl('client', 'clientDetail', array('uid'=>$info['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Member Detail</button>
                  </div>
                <?php }elseif($info['state']==-1){?>
                  <span class="colorcc0000">Refuse</span>
                  <div class="custom-btn-group approval-btn-group">
                    <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                    <button type="button" class="btn btn-success" onclick="javascript:window.location.href='<?php echo getUrl('client', 'clientDetail', array('uid'=>$info['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Member Detail</button>
                  </div>
                <?php }else{?>
                  <div class="custom-btn-group approval-btn-group">
                    <button type="button" class="btn btn-info" onclick="javascript:window.location.href='<?php echo getUrl('loan', 'approvalConfirm', array('uid'=>$info['uid'],'state'=>1), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa  fa-check"></i>Pass</button>
                    <button type="button" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo getUrl('loan', 'approvalConfirm', array('uid'=>$info['uid'],'state'=>'-1'), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa  fa-times"></i>Refuse</button>
                    <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                    <button type="button" class="btn btn-success" onclick="javascript:window.location.href='<?php echo getUrl('client', 'clientDetail', array('uid'=>$info['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Member Detail</button>
                  </div>
                <?php } ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="approval-history">
        <div class="ibox-title">
          <h5>Approval History</h5>
        </div>
        <div class="ibox-content">
          <table class="table">
              <thead>
              <tr class="table-header">
                  <td><?php echo 'Before Credit';?></td>
                  <td><?php echo 'Current Credit';?></td>
                  <td><?php echo 'Create Time';?></td>
                  <td><?php echo 'Operate ID';?></td>
                  <td><?php echo 'Operate Time';?></td>
                  <td><?php echo 'Type';?></td>
                  <td><?php echo 'State';?></td>
                  <td><?php echo 'Remark';?></td>
              </tr>
              </thead>
              <tbody class="table-body">
                <?php $list = $output['list'];?>
                <?php foreach($list as $row){?>
                  <tr>
                    <td><?php echo $row['before_credit'];?></td>
                    <td><?php echo $row['current_credit'];?></td>
                    <td><?php echo timeFormat($row['create_time']);?></td>
                    <td><?php echo $row['operator_id'];?></td>
                    <td><?php echo timeFormat($row['operator_time']);?></td>
                    <td><?php if($row['type'] == 0){echo 'First Credit';}elseif($row['type'] == 1){echo 'Raise';}else{echo 'Down';}  ?></td>
                    <td><?php if($row['state'] == 0){echo 'Auditing';}elseif($row['state']==1){echo 'Passed';}else{echo 'Refuse';} ?></td>
                    <td><?php echo $row['remark'];?></td>
                  </tr>
                <?php }?>
              </tbody>
            </table>
        </div>
      </div>
    </div>
</div>
<script>
    $(function () {

    })
</script>
