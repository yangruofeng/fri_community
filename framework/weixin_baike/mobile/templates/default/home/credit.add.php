<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  
</header>
<div class="wrap loan-wrap">
  <?php $data = $output['data'];?>
  <form id="" method="post">
    
    <div class="cerification-input aui-margin-b-10">
      <div class="loan-form request-credit-form">
        <ul class="aui-list aui-form-list loan-item">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Evalution of assets
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="member_id" id="member_id" value="<?php echo $data['asset_evaluation'];?>" readonly />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
              Evalution of business profitabilily
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="member_name" id="member_name" value="<?php echo $data['business_profitability'];?>" readonly />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Monthly Repayment Ability
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="monthly_repayment_ability" id="monthly_repayment_ability" value="" placeholder="Enter" />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Request for credit
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="suggest_credit" id="suggest_credit" value="" placeholder="Enter" />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Remark
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="remark" id="remark" value="" placeholder="Enter" />
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
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
if(window.operator){
  window.operator.showTitle('<?php echo $output['header_title'];?>');
}
  var type = '<?php echo $_GET['source']?>', l = '<?php echo $_GET['lang']?>';
  if (type == 'app') {
    app_show(type);
  }
  function app_show(type) {
    if (type == 'app') {
      $('#header').hide();
    } else {
      $('#header').show();
    }
  }
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  var client_id = '<?php echo $_GET['id'];?>';
      monthly_repayment_ability = $.trim($('#monthly_repayment_ability').val()),
      suggest_credit = $.trim($('#suggest_credit').val()),
      remark = $.trim($('#remark').val());
  if(!client_id){
    verifyFail('<?php echo 'Please reselect client.';?>');
    return;
  }
  if(!monthly_repayment_ability){
    verifyFail('<?php echo 'Please input monthly repayment ability.';?>');
    return;
  }
  if(!suggest_credit){
    verifyFail('<?php echo 'Please input request for credit.';?>');
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
    type: 'POST',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxAddCreditRequest',
    data: {member_id: client_id,monthly_repayment_ability: monthly_repayment_ability,suggest_credit: suggest_credit,remark: remark},
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        $('.upload-success').show();
        var count = $('#count').text();
        var times = setInterval(function(){
          count--;
          $('#count').text(count);
          if(count <= 1){
            clearInterval(times);
            $('.back').click();
          }
        },1000);
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
