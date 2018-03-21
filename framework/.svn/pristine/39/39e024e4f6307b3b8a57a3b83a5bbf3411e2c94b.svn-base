<div>
    <table class="table verify-table">
        <tbody class="table-body">
          <?php foreach($data['data'] as $key => $row){ ?>
            <tr>
              <td class="magnifier<?php echo $key;?>" style="width: 380px;">
                <div class="magnifier" index="<?php echo $key;?>">
                  <div class="magnifier-container" style="display:none;">
              			<div class="images-cover"></div>
              			<div class="move-view"></div>
              		</div>
              		<div class="magnifier-assembly">
              			<div class="magnifier-btn">
              				<span class="magnifier-btn-left">&lt;</span>
              				<span class="magnifier-btn-right">&gt;</span>
              			</div>
              			<!--按钮组-->
              			<div class="magnifier-line">
              				<ul class="clearfix animation03">
                        <?php foreach( $row['cert_images'] as $value ){ ?>
                            <li>
                  						<a target="_blank" href="<?php echo $value['image_url']; ?>">
                                <div class="small-img">
                    							<img src="<?php echo $value['image_url']; ?>" />
                    						</div>
                              </a>
                  					</li>
                        <?php } ?>

              				</ul>
              			</div>
              			<!--缩略图-->
              		</div>
              		<div class="magnifier-view"></div>
              		<!--经过放大的图片显示容器-->
              	</div>
              </td>
              <td>
                <div class="cert-info">
                  <p><label class="lab-name">Member Name :</label><a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['login_code'] ?></a></p>
                  <p><label class="lab-name">Source Type :</label><?php if($row['source_type'] == 0){echo 'Self Submission';}else{echo 'Teller Submission';} ?></p>
                  <p><label class="lab-name">Submit Time :</label><?php echo timeFormat($row['create_time']); ?></p>
                  <p><label class="lab-name">Remark :</label><?php echo $row['verify_remark']?:'/'; ?></p>
                </div>
              </td>
              <td>
                <div class="cert-type">
                  <h3><?php echo $output['verify_field'][$row['cert_type']]; ?></h3>
                  <?php if($row['cert_type'] == certificationTypeEnum::ID && $row['cert_sn'] ){ ?>
                    <p><label class="lab-name">Cert Name :</label><?php echo $row['cert_name']; ?></p>
                    <p><label class="lab-name">Cert Sn :</label><?php echo $row['cert_sn']; ?></p>
                  <?php } ?>
                </div>
              </td>
              <td>
                <div class="verify-state">
                  <div class="title">
                    Verify State
                  </div>
                  <div class="content">
                      <?php if($row['verify_state'] == 0){ ?>
                        <div class="state">Not Verified</div>
                      <?php }elseif($row['verify_state'] == 10){ ?>
                        <div class="state">Have Passed</div>
                      <?php }elseif($row['verify_state'] == 100){ ?>
                        <div class="state">Refuse</div>
                      <?php }else{ ?>
                          <?php if($data['cur_uid'] == $row['auditor_id']){ ?>
                                <div class="state"><span class="locking"><i class="fa fa-gavel"></i>Auditing</span></div>
                          <?php }else{ ?>
                                <div class="state other"><p><i class="fa fa-user"></i><?php echo $row['auditor_name'];?></p><span class="locking">Auditing</span></div>
                          <?php } ?>
                      <?php } ?>
                    <div class="custom-btn-group">
                      <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'cerificationDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <span><i class="fa  fa-vcard-o"></i>Audit</span>
                      </a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php }?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
<script>
  $('.magnifier-btn-left').on('click',function(){
    var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
    move(el, thumbnail, index);
  });
  $('.magnifier-btn-right').on('click',function(){
    var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
    move(el, thumbnail, index);
  });

  function move(magnifier, thumbnail, _boole){
    magnifier.index = _boole;
    (_boole) ? magnifier.index++ : magnifier.index--;
    var thumbnailImg = thumbnail.find('>*'), lineLenght = thumbnailImg.length;
    var _deviation = Math.ceil(magnifier.width() / thumbnailImg.width() /2);
    if(lineLenght < _deviation){
      return false;
    }
    (magnifier.index < 0) ? magnifier.index = 0 : (magnifier.index > lineLenght-_deviation) ? magnifier.index = lineLenght - _deviation : magnifier.index;
    var endLeft = (thumbnailImg.width() * magnifier.index) - thumbnailImg.width();
    thumbnail.css({
      'left' : ((endLeft > 0) ? -endLeft : 0)+'px'
    });
  }
</script>
