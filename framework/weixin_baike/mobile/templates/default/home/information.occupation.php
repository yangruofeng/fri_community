<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap information-wrap">
  <?php $work_info = $output['work_info'];?>
  <div>
    <ul class="aui-list request-detail-ul">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Company Name
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $work_info['company_name'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Company Address
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $work_info['company_addr'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Position
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $work_info['position'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Government Staff
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo ['is_government']?'Yes':'No';?>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">

</script>
