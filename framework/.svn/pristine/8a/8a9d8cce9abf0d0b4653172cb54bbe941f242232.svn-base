<div id="header">
    <div>
        <h1>SAMRITHISAK</h1>
    </div>
</div>
<div id="top-tools">
    <ul class="list-inline">
        <li title="Full Screen" class="full_screen"><i class="fa fa-arrows-alt"></i>Screen</li>
        <li title="Lock Screen" onclick="callWin_lock_screen()"><i class="fa fa-lock"></i>Lock</li>
        <li><i class="fa fa-wrench"></i>Tools</li>
        <li class="dropdown open" id="profile-messages">
            <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle" style="padding-right: 15px" aria-expanded="true">
                <span class="text user_name"><i class="fa fa-cog"></i>Settings</span>&nbsp;
                <b class="caret" style="margin-left: 0"></b>
            </a>
            <ul class="dropdown-menu" style="left:auto;right: 0;">
                <li><a href="#" id="my_profile"><i class="fa fa-user"></i> My Profile</a></li>
                <li class="divider"></li>
                <li><a href="#" id="change_password"><i class="fa fa-tasks"></i> Change Password</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo getUrl('login','loginOut', array(), false, ENTRY_COUNTER_SITE_URL)?>"><i class="fa fa-key"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</div>
<div id="top-counter">
    <div class="counter-icon">
        <img src="<?php echo getUserIcon($output['user_info']['user_icon'])?>">
    </div>
    <div class="counter-info">
        <div class="name"><?php echo $output['user_info']['user_code'] ."/". $output['user_info']['user_name'] ?></div>
        <div class="department"><?php echo $output['department_info']['branch_name'] . '  ' . $output['department_info']['depart_name'];?></div>
    </div>
    <div class="balance">
        <div class="cash_in_hand">
            <div class="col-sm-3">Cash On Hand:</div>
            <div class="col-sm-3">USD00.00</div>
            <div class="col-sm-3">KHR00.00</div>
            <div class="col-sm-3">CNY00.00</div>
<!--            <div class="col-sm-3">-->
<!--                <select class="form-control" style="font-size: 12px;border-radius: 0">-->
<!--                    <option>USD</option>-->
<!--                    <option>KHR</option>-->
<!--                    <option>CNY</option>-->
<!--                </select>-->
<!--            </div>-->
        </div>
        <div class="outstanding">
            <div class="col-sm-3">Outstanding:</div>
            <div class="col-sm-3">USD00.00</div>
            <div class="col-sm-3">KHR00.00</div>
            <div class="col-sm-3">CNY00.00</div>
<!--            <div class="col-sm-3">-->
<!--                <select class="form-control" style="font-size: 12px;border-radius: 0">-->
<!--                    <option>USD</option>-->
<!--                    <option>KHR</option>-->
<!--                    <option>CNY</option>-->
<!--                </select>-->
<!--            </div>-->
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#top-tools').on('click', ' .full_screen', function () {
            callWin_set_fullscreen();
            $(this).removeClass('full_screen').addClass('un_full_screen').attr('title', 'Exit full screen');
        })

        $('#top-tools').on('click', ' .un_full_screen', function () {
            callWin_unset_fullscreen();
            $(this).removeClass('un_full_screen').addClass('full_screen').attr('title', 'Full screen');
        })

        $('#top-tools .full_screen').click();
    })

    function callWin_set_fullscreen(){
        if(window.external){
            try{
                window.external.setFullScreen();
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function callWin_unset_fullscreen(){
        if(window.external){
            try{
                window.external.unsetFullScreen();
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function callWin_lock_screen(){
        if(window.external){
            try{
                window.external.lockScreen();
            }catch (ex){
                alert(ex.Message);
            }
        }
    }
</script>
