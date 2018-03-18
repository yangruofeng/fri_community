<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=4">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=4">
<header class="top-header" id="header">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-btn" onclick="javascript:location.href='<?php echo getUrl('member', 'loanContract', array(), false, WAP_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>
<div class="wrap credit-loan-wrap">
  <?php $credit_info = $output['credit_info'];$ace_info = $output['ace_info']; $insurance_info = $output['insurance_info'];?>
  <?php $purpose = $output['purpose'];?>
  <div class="credit-loan-balance">
    <div class="b"><?php echo $credit_info['balance'];?></div>
    <div class="l"><?php echo $lang['label_credit_balance'];?></div>
  </div>
  <div class="credit-loan-form">
    <ul class="aui-list aui-form-list credit-loan-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_withdrawal_amount'];?>
          </div>
          <div class="aui-list-item-input">
            <input type="number" name="amount" id="amount" placeholder="<?php echo $lang['label_enter'];?>">
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_loan_period'];?>
          </div>
          <div class="aui-list-item-input input-period">
            <input type="number" name="loan_period" id="loan_period" placeholder="<?php echo $lang['label_enter'];?>">
            <select class="" name="loan_period_unit" id="loan_period_unit">
              <option value="year"><?php echo $lang['label_year'];?></option>
              <option value="month" selected><?php echo $lang['label_month'];?></option>
              <option value="day"><?php echo $lang['label_day'];?></option>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_loan_purpose'];?>
          </div>
          <div class="aui-list-item-input input-select">
            <select class="" name="propose" id="propose">
              <option value="0"><?php echo $lang['label_select'];?></option>
              <?php foreach ($purpose as $key => $value) { ?>
                <option value="<?php echo $value['item_code'];?>"><?php echo $value['item_name'];?></option>
              <?php } ?>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_repayment_method'];?>
          </div>
          <div class="aui-list-item-input input-select">
            <select class="" name="repayment_type" id="repayment_type">
              <option value="0"><?php echo $lang['label_select'];?></option>
              <option value="single_repayment"><?php echo 'Single';?></option>
              <option value="annuity_scheme"><?php echo 'Installment';?></option>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
      <li class="aui-list-item" id="repaymentFrequency">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_repayment_frequency'];?>
          </div>
          <div class="aui-list-item-input input-select">
            <select name="repayment_period" id="repayment_period">
              <option value="0"><?php echo $lang['label_select'];?></option>
              <option value="monthly"><?php echo $lang['label_once_a_month'];?></option>
              <option value="weekly"><?php echo $lang['label_once_a_week'];?></option>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
      <li class="aui-list-item select-insurance">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_select_insurance'];?>
          </div>
          <div class="aui-list-item-input input-select">
            <input type="hidden" name="insurance_item_id" id="insurance_item_id" value="">
            <input type="text" name="select_insurance" id="select_insurance" readonly placeholder="<?php echo $lang['label_select'];?>">
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
    </ul>
    <div style="padding: .8rem 0;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="withdraw"><?php echo $lang['label_withdraw'];?></div>
    </div>
  </div>
  <div class="insurance-list-wrap" style="display: none;">
    <div class="content">
      <div class="title">
        <?php echo $lang['label_select_insurance'];?>
        <span class="cancel" id="iCancel"><?php echo $lang['act_cancel'];?></span>
        <span class="confirm" id="iConfirm"><?php echo $lang['act_confirm'];?></span>
      </div>
      <div class="list">
        <ul class="aui-list aui-media-list insurance-ul">
          <?php foreach ($insurance_info as $key => $value) { ?>
            <li class="aui-list-item">
              <div class="aui-list-item-label-icon insurance-icon">
                <span class="item-ck" data-uid="<?php echo $value['uid'];?>" data-name="<?php echo $value['item_name'];?>"></span>
              </div>
              <div class="aui-list-item-inner insurance-text">
                <div class="name"><?php echo $value['item_name'];?></div>
                <div class="info"><?php echo $lang['label_coverage'].$lang['label_colon'];?> <?php echo $value['fixed_amount'];?><span><?php echo $lang['label_price'].$lang['label_colon'];?> <?php echo $value['fixed_price'];?></span></div>
              </div>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#select_insurance').on('click', function(){
  $('.insurance-list-wrap').show();
});
$('#iCancel').on('click', function(){
  $('.insurance-list-wrap').hide();
});
$('#iConfirm').on('click', function(){
  $('.insurance-list-wrap').hide();
  var ck = document.getElementsByClassName('item-ck'), i = 0, len = ck.length, uids = [], names = [];
  for (i; i < len; i++) {
    var flag = $(ck[i]).hasClass('active');
    if(flag){
      var uid = $(ck[i]).attr('data-uid'), name = $(ck[i]).attr('data-name');
      uids.push(uid);
      names.push(name);
    }
  }
  uids = uids.join(',');
  names = names.join(',');
  $('#insurance_item_id').val(uids);
  $('#select_insurance').val(names);
});
$('.insurance-icon').on('click', function(e){
  $(this).find('.item-ck').hasClass('active') ? $(this).find('.item-ck').removeClass('active') : $(this).find('.item-ck').addClass('active');
});
$('#repayment_type').on('change', function(){
  var repayment_type = $.trim($('#repayment_type').val());
  if(repayment_type == 'single_repayment'){
    $('#repaymentFrequency').hide();
  }else{
    $('#repaymentFrequency').show();
  }
});
$('#withdraw').on('click', function(){
  //amount loan_period loan_period_unit propose repayment_type repayment_period insurance_item_id
  var amount = $.trim($('#amount').val()), loan_period = $.trim($('#loan_period').val()),
      loan_period_unit = $.trim($('#loan_period_unit').val()), propose = $.trim($('#propose').val()),
      repayment_type = $.trim($('#repayment_type').val()), repayment_period = $.trim($('#repayment_period').val()),
      insurance_item_id = $.trim($('#insurance_item_id').val()), param = {};
  if(!amount){
    verifyFail('<?php echo $lang['tip_please_input_withdrawal_amount'];?>');
    return;
  }
  param.amount = amount;
  if(!loan_period){
    verifyFail('<?php echo $lang['tip_please_input_withdrawal_amount'];?>');
    return;
  }
  param.loan_period = loan_period;
  param.loan_period_unit = loan_period_unit;
  if(!repayment_type){
    verifyFail('<?php echo $lang['tip_please_input_repayment_method'];?>');
    return;
  }
  param.repayment_type = repayment_type;
  if(repayment_type != 'single_repayment'){
    if(repayment_period == 0) {
      verifyFail('<?php echo $lang['tip_please_input_repayment_method'];?>');
      return;
    }
    param.repayment_period = repayment_period;
  }
  if(propose){
    param.propose = propose;
  }
  if(insurance_item_id){
    param.insurance_item_id = insurance_item_id;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=submitWithdraw',
    data: param,
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=withdrawConfirm&contract_id='+data.DATA.contract_id;
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
