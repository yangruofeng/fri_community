<div id="header">
    <h1><a href="dashboard.html"><?php echo $output['system_title'] ?: 'Loan Admin'?></a></h1>
</div>
<!--close-Header-part-->

<!--start-top-serch-->
<div id="search">
    <i class="fa fa-align-justify fa-lg" style="margin-right: 20px;cursor: pointer;"></i>
    <input style="height: 28px;width: 270px" type="text"id="search-text" placeholder="Search by client id or contract sn."/>
    <button style="height: 28px" type="submit" class="tip-bottom" title="Search" id="search-btn">
        <i class="fa fa-search"></i>
    </button>
</div>
<!--close-top-serch-->

<!--start-top-serch-->
<div id="tools">
    <div class="calculator" title="Loan Calculator">
        <img src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/calculator.png' ?>">
        <img src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/calculator-hover.png' ?>">
    </div>
</div>
<!--close-top-serch-->

<!--top-Header-menu-->
<div id="user-nav" class="navbar">
<!--    <ul class="nav">-->
    <ul class="">
<!--        <li class=""><a title="" href="#"><i class="fa fa-cog"></i> <span class="text">&nbsp;Setting</span></a></li>-->
<!--        <li class="dropdown" id="menu-messages">-->
<!--            <a href="#" data-toggle="dropdown" data-target="#menu-messages" class="dropdown-toggle">-->
<!--                <i class="fa fa-envelope"></i>&nbsp;-->
<!--                <span class="text">Message</span>&nbsp;-->
<!--                <span class="label label-important">0</span>&nbsp;-->
<!--                <b class="caret"></b>-->
<!--            </a>-->
<!--            <ul class="dropdown-menu">-->
<!--                <li><a class="sAdd" title="" href="#"><i class="fa fa-plus"></i> New Message</a></li>-->
<!--                <li class="divider"></li>-->
<!--                <li><a class="sInbox" title="" href="#"><i class="fa fa-envelope"></i> Inbox</a></li>-->
<!--                <li class="divider"></li>-->
<!--                <li><a class="sOutbox" title="" href="#"><i class="fa fa-arrow-up"></i> Outbox</a></li>-->
<!--                <li class="divider"></li>-->
<!--                <li><a class="sTrash" title="" href="#"><i class="fa fa-trash"></i> Recycle</a></li>-->
<!--            </ul>-->
<!--        </li>-->
        <li  class="dropdown" id="profile-messages">
            <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle" style="padding: 15px 20px">
                <img alt="Avatar" class="img-circle" src="<?php echo getUserIcon($output['user_info']['user_icon'])?>">
                <span class="text user_name"><?php echo $output['user_info']['user_name']?:$output['user_info']['user_code']?></span>&nbsp;
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu" style="left:auto;right: 0;">
                <li><a href="#" id="my_profile"><i class="fa fa-user"></i> My Profile</a></li>
                <li class="divider"></li>
                <li><a href="#" id="change_password"><i class="fa fa-tasks"></i> Change Password</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo getUrl('login','loginOut', array(), false, ENTRY_DESKTOP_SITE_URL)?>"><i class="fa fa-key"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</div>
<!--close-top-Header-menu-->