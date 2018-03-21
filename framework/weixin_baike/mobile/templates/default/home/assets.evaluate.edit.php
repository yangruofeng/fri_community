<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap assets-evalute-wrap">
<?php $data = $output['data']['asset_detail']; ?>
<form id="" method="post">
    <div class="cerification-input aui-margin-b-10">
      <div class="loan-form">
        <ul class="aui-list aui-form-list loan-item">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Valuation
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="valuation" id="valuation" value="<?php echo $data['valuation'];?>" placeholder="Enter" />
                <span class="p-unit">USD</span>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Remark
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="remark" id="remark" value="<?php echo $data['remark'];?>" placeholder="Enter" />
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div style="padding: 0 .8rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">Submit</div>
    </div>
  </form>
</div>
<script type="text/javascript">
$('#submit').on('click', function(){
  var id = '<?php echo $_GET['uid'];?>';
      valuation = $.trim($('#valuation').val()),
      remark = $.trim($('#remark').val());
  if(!valuation){
    verifyFail('<?php echo 'Please input valuation.';?>');
    return;
  }
  if(!remark){
    verifyFail('<?php echo 'Please input remark.';?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'get',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxEditEvalute',
    data: {id: id,valuation: valuation, currency: 'USD',remark: remark},
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href="<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=assetsEvaluate&cid=<?php $_GET['cid'];?>&id=<?php echo $_GET['mid']?>&back=search";
      }else{
        verifyFail(data.MSG);
      }

    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });
});
</script>
