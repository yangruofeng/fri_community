<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap information-wrap">
  <?php $data = $output['data'];$work_info = $output['work_info'];?>
  <div>
    <ul class="aui-list request-detail-ul">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Khmer Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php $name = json_decode($data['id_kh_name_json'], true);echo $name['family_name'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Family Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php $name = json_decode($data['id_en_name_json'], true);echo $name['family_name'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Given Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php $name = json_decode($data['id_en_name_json'], true);echo $name['given_name'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Second Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php $name = json_decode($data['id_en_name_json'], true);echo $name['given_name'];?>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-margin-t-10">
    <ul class="aui-list info-list aui-margin-b-10">
      <li class="aui-list-item operator-item" onclick="<?php if($data['id_sn']){?>javascript:location.href='<?php echo getUrl('home', 'idCardInfomation', array('cid'=>$_GET['cid']), false, WAP_OPERATOR_SITE_URL)?>'<?php }?>">
        <div class="aui-list-item-inner content <?php if($data['id_sn']){?>aui-list-item-arrow<?php }?>">
          <?php echo 'ID Card';?>
          <?php if(!$data['id_sn']){?>
            <span class="tip">No Verify</span>
          <?php }?>
        </div>
      </li>
      <li class="aui-list-item info-item" onclick="<?php if($work_info){?>javascript:location.href='<?php echo getUrl('home', 'occupationInfomation', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'<?php }?>">
        <div class="aui-list-item-inner content <?php if($work_info){?>aui-list-item-arrow<?php }?>">
          <?php echo 'Occupation';?>
          <?php if(!$work_info){?>
            <span class="tip">No Verify</span>
          <?php }?>
        </div>
      </li>
      <li class="aui-list-item info-item" onclick="editAddress();">
        <div class="aui-list-item-inner content aui-list-item-arrow">
          <?php echo 'Place of residence';?>
        </div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">
  function editAddress(){
    if(window.operator){
      window.operator.memberPlaceOfResidence('<?php echo $_GET['id'];?>');
      return;
    }
  }
</script>
