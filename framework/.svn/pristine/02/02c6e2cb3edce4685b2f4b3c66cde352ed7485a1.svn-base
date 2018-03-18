<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<?php $data = $output['data'];$list = $data['list'];?>
<div class="wrap assets-evalute-wrap">
  <div class="evalute-amount">
    <p class="amount"><?php echo $data['total_amount'];?> <em>USD</em></p>
    <p class="title">Total Amount</p>
  </div>
  <div class="assets-list">
    <ul class="aui-list assets-ul aui-margin-b-10">
      <li class="aui-list-item assets-title">
        <div class="i">Index</div>
        <div>Type</div>
        <div>Remark</div>
        <div>Evalution</div>
      </li>
      <?php if(count($list) > 0){ ?>
        <?php foreach($list as $k => $v){ ?>
          <li class="aui-list-item assets-item">
            <div class="i"><?php echo $k+1;?></div>
            <div>
              <?php 
                $str = '';
                switch ($v['asset_type']) {
                  case certificationTypeEnum::CAR :
                    $str = 'Car';
                    break;
                  case certificationTypeEnum::HOUSE :
                    $str = 'House';
                    break;
                  case certificationTypeEnum::LAND :
                    $str = 'Land';
                    break;
                  case certificationTypeEnum::MOTORBIKE :
                    $str = 'Motorbike';
                    break;
                  default:
                  $str = 'Car';
                    break;
                }
                echo $str;
              ?>
            </div>
            <div><?php echo $v['remark'];?></div>
            <div>
              <?php if($v['valuation'] <= 0){ ?>
                <a class="evalute-edit" href="<?php echo getUrl('home', 'editAssetsEvaluate', array('cid'=>$_GET['cid'],'uid'=>$v['uid'],'mid'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>">Edit ></a>
              <?php }else{ ?>
                <?php echo $v['valuation'];?>
                <a class="evalute-edit" href="<?php echo getUrl('home', 'editAssetsEvaluate', array('cid'=>$_GET['cid'],'uid'=>$v['uid'],'mid'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>"><i class="aui-iconfont aui-icon-edit"></i></a>
              <?php } ?>
            </div>
          </li>
        <?php } ?>
      <?php }else{ ?>
        <div class="no-record"><?php echo $lang['label_no_data'];?></div>
      <?php } ?>
    </ul>
  </div>
</div>
<script type="text/javascript">
var back = '<?php echo $_GET['back']?>';

</script>
