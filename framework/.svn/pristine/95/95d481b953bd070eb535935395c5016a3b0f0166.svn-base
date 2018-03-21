<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=10" rel="stylesheet" type="text/css"/>
<style>
.custom-btn-group {
  float: inherit;
}
.cerification-history {
  margin-top: 20px;
}
.verify-table img {
  width: 80px;
}
.cerification-history .table .table-header {
    background: none;
}
.verify-img {
  width: 300px;
  margin-bottom:5px;
}
#select_area .col-sm-6 {
  width: 200px;
  padding-left: 0;
}
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Verification</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
      <?php $info = $output['info'];$IDInfo = $output['IDInfo'];?>
      <?php if(!$output['lock'] && $info['cert_type'] == certificationTypeEnum::ID){ ?>
        <input type="hidden" name="submit" id="submit" value="1">
      <?php }else{ ?>
        <input type="hidden" name="submit" id="submit" value="2">
      <?php } ?>
      <form class="demoform" id="validIDForm" action="index.php?act=client&op=cerifycationConfirm" method="post">
        <input type="hidden" name="validate" value="" />
        <input type="hidden" name="uid" value="<?php echo $info['uid'];?>" />
        <table class="table">
            <tbody class="table-body">
                <tr>
                  <td><label class="control-label">Member Name</label></td><td><?php echo $info['display_name'];?></td>
                </tr>
                <?php if( $output['cert_sample_images'][$info['cert_type']] ){ ?>
                    <tr>
                        <td><label class="control-label">Sample</label></td>
                        <td>
                            <?php foreach( $output['cert_sample_images'][$info['cert_type']] as $sample ){  ?>
                                <div style="display:inline-block;width: 200px;text-align: center;margin-right: 5px;">
                                    <a target="_blank" href="<?php echo $sample['image']; ?>">
                                        <img src="<?php echo $sample['image']; ?>" alt="" style="width: 200px;height: 200px" />
                                    </a>
                                    <h5 style="color:red;">
                                        <?php echo $sample['des']; ?>
                                    </h5>
                                </div>
                            <?php }  ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                  <td><label class="control-label">Mug Shot</label></td>
                  <td>

                      <?php foreach( $info['cert_images'] as $value ){ ?>
                          <a target="_blank" href="<?php echo $value['image_url']; ?>"><img src="<?php echo $value['image_url']; ?>" class="verify-img" alt=""></a>
                      <?php } ?>


                   <!-- <?php /*if($info['cert_photo']){ */?>
                      <a target="_blank" href="<?php /*echo $info['cert_photo'] */?>"><img src="<?php /*echo $info['cert_photo'] */?>" class="verify-img" alt=""></a>
                    <?php /*} */?>
                    <?php /*if($info['cert_photo1']){ */?>
                      <a target="_blank" href="<?php /*echo $info['cert_photo1'] */?>"><img src="<?php /*echo $info['cert_photo1'] */?>" class="verify-img" alt=""></a>
                    <?php /*} */?>
                    <?php /*if($info['cert_photo2']){ */?>
                      <a target="_blank" href="<?php /*echo $info['cert_photo2'] */?>"><img src="<?php /*echo $info['cert_photo2'] */?>" class="verify-img" alt=""></a>
                    <?php /*} */?>
                    <?php /*if($info['cert_photo3']){ */?>
                      <a target="_blank" href="<?php /*echo $info['cert_photo3'] */?>"><img src="<?php /*echo $info['cert_photo3'] */?>" class="verify-img" alt=""></a>
                    --><?php /*} */?>

                  </td>
                </tr>
                <?php if($info['cert_type'] == certificationTypeEnum::ID){ ?>
                  <tr>
                    <td><label class="control-label">English Name</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <?php echo $info['cert_name'];?>
                          <div class="cerification-form clearfix" style="margin-top: 5px;">
                            <span class="n-label">Family Name</span><input type="text" class="form-control" name="en_family_name" value="">
                            <span class="validate-checktip"></span>
                          </div>
                          <div class="cerification-form clearfix" style="margin-top: 5px;">
                            <span class="n-label">Given Name</span><input type="text" class="form-control" name="en_given_name" value="">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo $info['cert_name'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['cert_name'];?>
                      <?php } ?>
                    </td>
                  </tr>

                    <tr>
                        <td>
                            <label class="control-label">Khmer Name</label>
                        </td>
                        <td>
                            <?php $name_json = @json_decode($info['cert_name_json'],true);?>
                            <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                              <?php if(!$output['lock']){ ?>
                                <?php echo $name_json['kh'];?>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                  <span class="n-label">Family Name</span><input type="text" class="form-control" name="kh_family_name" value="">
                                  <span class="validate-checktip"></span>
                                </div>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                  <span class="n-label">Given Name</span><input type="text" class="form-control" name="kh_given_name" value="">
                                  <span class="validate-checktip"></span>
                                </div>
                              <?php }else{ ?>
                                <?php echo $name_json['kh'];?>
                              <?php } ?>
                            <?php }else{ ?>
                              <?php echo $name_json['kh'];?>
                            <?php } ?>
                        </td>
                    </tr>
                  <tr>
                    <td><label class="control-label">Cert Sn</label></td>
                    <td>

                        <?php echo $info['cert_sn'];?>

                     <!-- <?php /*if(!$info['verify_state'] || $info['verify_state'] == -1){ */?>
                        <?php /*if(!$output['lock']){ */?>
                          <div class="cerification-form">
                            <input type="text" class="form-control" name="cert_sn" value="">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php /*}else{ */?>
                          <?php /*echo $info['cert_sn'];*/?>
                        <?php /*} */?>
                      <?php /*}else{ */?>
                        <?php /*echo $info['cert_sn'];*/?>
                      --><?php /*} */?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Type</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <label class="radio-inline">
                            <input type="radio" name="id_type" value="0" checked/> 国内
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="id_type" value="1" /> 国外
                          </label>
                        <?php }else{ ?>
                          <?php echo $info['gender'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['gender'];?>

                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Gender</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <label class="radio-inline">
                            <input type="radio" name="gender" value="1" checked/> Male
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="gender" value="0" /> Female
                          </label>
                        <?php }else{ ?>
                          <?php echo $info['gender'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['gender'];?>

                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Date of Birth</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <div class="cerification-form">
                            <input type="text" class="form-control" name="birthday" id="birthday">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo dateFormat($info['birthday']);?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo dateFormat($info['birthday']);?>
                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Nationality</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <div class="cerification-form">
                            <input type="text" class="form-control" name="nationality" value="">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo $info['nationality'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['nationality'];?>

                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Address</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <div class="cerification-form" id="select_area">
                            <div id="select_area"></div>
                            <input type="hidden" name="id_address1" id="id_address1" value="">
                            <input type="hidden" name="id_address2" id="id_address2" value="">
                            <input type="hidden" name="id_address3" id="id_address3" value="">
                            <input type="hidden" name="id_address4" id="id_address4" value="">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo $info['cert_addr'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['cert_addr'];?>

                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Addr</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <div class="cerification-form">
                            <input type="text" class="form-control" name="cert_addr" id="cert_addr" value="">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo $info['cert_addr'];?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo $info['cert_addr'];?>

                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Expire Time</label></td>
                    <td>
                      <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                        <?php if(!$output['lock']){ ?>
                          <div class="cerification-form">
                            <input type="text" class="form-control" name="cert_expire_time" id="date">
                            <span class="validate-checktip"></span>
                          </div>
                        <?php }else{ ?>
                          <?php echo timeFormat($info['cert_expire_time']);?>
                        <?php } ?>
                      <?php }else{ ?>
                        <?php echo timeFormat($info['cert_expire_time']);?>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php if($info['cert_type'] == certificationTypeEnum::FAIMILYBOOK){ ?>
                  <tr>
                    <td><label class="control-label">Cert Name</label></td><td><?php echo $IDInfo['cert_name']; ?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Sn</label></td><td><?php echo $IDInfo['cert_sn']; ?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Address</label></td><td><?php echo $IDInfo['cert_addr']; ?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Cert Expire Time</label></td><td><?php echo dateFormat($IDInfo['cert_expire_time']); ?></td>
                  </tr>
                <?php } ?>
                <tr>
                  <td><label class="control-label">Source Type</label></td><td><?php if($info['source_type'] == 0){echo 'Self Submission';}else{echo 'Teller Submission';} ?></td>
                </tr>
                <tr>
                  <td><label class="control-label">Verify State</label></td><td><?php if($info['verify_state'] == -1){echo 'Auditing...';}elseif($info['verify_state'] == 0){echo 'Not Verified';}elseif($info['verify_state'] == 10){echo 'Passed';}else{echo '未通过';} ?></td>
                </tr>
                <tr>
                  <td><label class="control-label">Cert Type</label></td>
                  <td>
                      <?php echo $output['verify_field'][$info['cert_type']]; ?>
                  </td>
                </tr>
                <tr>
                  <td><label class="control-label">Auditor Name</label></td><td><?php echo $info['auditor_name'] ?></td>
                </tr>
                <tr>
                  <td><label class="control-label">Auditor Time</label></td><td><?php echo timeFormat($info['auditor_time']) ?></td>
                </tr>
                <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                  <?php if(!$output['lock']){ ?>
                    <tr>
                      <td><label class="control-label">Verify State</label></td>
                      <td>
                        <label class="radio-inline">
                          <input type="radio" name="verify_state" value="10" checked/> Pass
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="verify_state" value="100" /> Refuse
                        </label>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } ?>
                <tr>
                  <td><label class="control-label">Remark</label></td>
                  <td>
                    <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                      <?php if(!$output['lock']){ ?>
                        <div class="cerification-form">
                          <textarea name="remark" class="form-control"></textarea><span class="validate-checktip"></span>
                        </div>
                      <?php }else{ ?>
                        <?php echo $info['verify_remark'];?>
                      <?php } ?>
                    <?php }else{ ?>
                      <?php echo $info['verify_remark'];?>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align: center;">
                    <?php if($output['lock']){?>
                      <span class="color28B779">Auditing...</span>
                      <div class="custom-btn-group approval-btn-group">
                        <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                      </div>
                    <?php }elseif($info['verify_state'] == 10){?>
                      <span class="color28B779">Passed</span>
                      <div class="custom-btn-group approval-btn-group">
                        <button type="button" class="btn btn-info" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                      </div>
                    <?php }elseif($info['verify_state'] == 100){?>
                      <span class="color28B779">Refuse</span>
                      <div class="custom-btn-group approval-btn-group">
                        <button type="button" class="btn btn-info" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                      </div>
                    <?php }else{?>
                      <div class="custom-btn-group approval-btn-group">
                        <button type="button" class="btn btn-danger" onclick="submitIDForm();"><i class="fa  fa-check"></i>Submit</button>
                        <button type="button" class="btn btn-info" onclick="javascript:window.location.href='<?php echo getUrl('client', 'cerifycationCancel', array('uid'=>$info['uid']), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Cancel Audit</button>
                      </div>
                      <!--<div class="custom-btn-group approval-btn-group">
                        <?php if($info['cert_type'] == certificationTypeEnum::ID){ ?>
                          <button type="button" class="btn btn-info" onclick="submitIDForm();"><i class="fa  fa-check"></i>Submit</button>
                          <button type="button" class="btn btn-danger" onclick="javascript:window.location.href='<?php echo getUrl('client', 'cerifycationCancel', array('uid'=>$info['uid']), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Cancel Audit</button>
                        <?php }else{?>
                          <button type="button" class="btn btn-info" onclick="javascript:window.location.href='<?php echo getUrl('client', 'cerifycationConfirm', array('uid'=>$info['uid'],'verify_state'=>10), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa  fa-check"></i>Pass</button>
                          <button type="button" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo getUrl('client', 'cerifycationConfirm', array('uid'=>$info['uid'],'verify_state'=>100), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa  fa-times"></i>Refuse</button>
                          <button type="button" class="btn btn-danger" onclick="javascript:window.location.href='<?php echo getUrl('client', 'cerifycationCancel', array('uid'=>$info['uid']), false, BACK_OFFICE_SITE_URL)?>'"><i class="fa fa-vcard-o"></i>Cancel Audit</button>
                        <?php } ?>
                      </div>-->
                    <?php } ?>
                  </td>
                </tr>
            </tbody>
        </table>
      </form>
      <div class="cerification-history">
        <?php $history = $output['history'];$count = count($history);?>
        <div class="ibox-title">
          <h5>Cerification History</h5>
        </div>
        <div class="ibox-content" style="padding:0;">
          <?php if($count > 0){ ?>
            <table class="table verify-table">
                <thead>
                <tr class="table-header">
                    <td><?php echo 'Member Name';?></td>
                    <td  style="text-align: left;width: 300px;"><?php echo 'Mug Shot';?></td>
                    <td><?php echo 'Cert Name';?></td>
                    <td><?php echo 'Cert Sn';?></td>
                    <td><?php echo 'Verify State';?></td>
                    <td><?php echo 'Cert Type';?></td>
                    <td><?php echo 'Source Type';?></td>
                    <td><?php echo 'Auditor Name';?></td>
                    <td><?php echo 'Auditor Time';?></td>
                    <td><?php echo 'Remark';?></td>
                </tr>
                </thead>
                <tbody class="table-body">
                <?php foreach($history as $row){?>
                    <tr>
                      <td><?php echo $info['display_name'] ?></td>
                        <td>
                            <?php foreach( $row['cert_images'] as $value ){ ?>
                                <span>
                                    <img src="<?php echo $value['image_url']; ?>" alt="" style="width: 80px;height: 70px;">
                                </span>
                            <?php } ?>
                        </td>

                      <td><?php echo $row['cert_name'] ?></td>
                      <td><?php echo $row['cert_sn'] ?></td>
                      <td><?php if($row['verify_state'] == 0){echo 'Not Verified';}elseif($row['verify_state'] == 10){echo 'Have Passed';}elseif($row['verify_state'] == 100){echo 'Refuse';}else{echo 'Audit...';} ?></td>
                      <td><?php if($row['cert_type'] == certificationTypeEnum::ID){echo 'ID';}elseif($row['cert_type'] == certificationTypeEnum::FAIMILYBOOK){echo 'Faimily Book';}elseif($row['cert_type'] == certificationTypeEnum::PASSPORT){echo 'Passport';}elseif($row['cert_type'] == certificationTypeEnum::HOUSE){echo 'House';}elseif($row['cert_type'] == certificationTypeEnum::CAR){echo 'Car';} ?></td>
                      <td><?php if($row['source_type'] == 0){echo 'Self Submission';}else{echo 'Teller Submission';} ?></td>
                      <td><?php echo $row['auditor_name'] ?></td>
                      <td><?php echo timeFormat($row['auditor_time']) ?></td>
                      <td><?php echo $row['verify_remark'] ?></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
          <?php }else{ ?>
            <div class="no-record">
              No Record
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/jquery.validate.min.js?v=2"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=20"></script>
<script>
$(function () {
  getArea(0);
  $('#birthday').datepicker({
    format: 'yyyy-mm-dd'
  });
  $('#date').datepicker({
    format: 'yyyy-mm-dd'
  });
});

$('#select_area').delegate('select', 'change', function () {
    var _value = $(this).val(), len = $('#select_area select').index(this)+1;
    _value == 0 ? $('#id_address'+len).val('') : $('#id_address'+len).val(_value);
    len < 4 ? $('#id_address4').val('') : '';
    $('input[name="address_id"]').val(_value);
    $(this).closest('div').nextAll().remove();
    _address_region = '';
    $('#select_area select').each(function () {
        if ($(this).val() != 0) {
            _address_region += $(this).find('option:selected').text() + ' ';
        }
    })
    var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
    //codeAddress(_address, 14);
    if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
        getArea(_value);
    }
})

function getArea(uid) {
  yo.dynamicTpl({
    tpl: "setting/area.list",
    dynamic: {
      api: "setting",
      method: "getAreaList",
      param: {uid: uid}
    },
    callback: function (_tpl) {
      $("#select_area").append(_tpl);
    }
  });
}

function submitIDForm(){
  $('#validIDForm').submit();
}
var submit = $('#submit').val();

var validIDParam = {
  ele: '#validIDForm', //表单id
  params: [{
    field: 'en_family_name',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the english family name.'
    }
  },{
    field: 'en_given_name',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the english given name.'
    }
  },{
    field: 'kh_family_name',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the khmer family name.'
    }
  },{
    field: 'kh_given_name',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the khmer given name.'
    }
  },{
    field: 'birthday',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the birthday.'
    }
  },{
    field: 'nationality',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the nationality.'
    }
  },{
    field: 'id_address4',
    rules: {
      required: true
    },
    messages: {
      required: 'Please select the cert address.'
    }
  },{
    field: 'cert_addr',
    rules: {
      required: true
    },
    messages: {
      required: 'Please select the detail cert address.'
    }
  },{
    field: 'cert_expire_time',
    rules: {
      required: true
    },
    messages: {
      required: 'Please select the cert expire time.'
    }
  },{
    field: 'remark',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the remark.'
    }
  }]
};
var validParam = {
  ele: '#validIDForm', //表单id
  params: [{
    field: 'remark',
    rules: {
      required: true
    },
    messages: {
      required: 'Please input the remark.'
    }
  }]
};
if(submit == 1){
  validform(validIDParam);
}else{
  validform(validParam);
}
</script>
