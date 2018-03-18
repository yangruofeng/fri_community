<div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
        <div id="breadcrumb">
            <?php if (!$output['is_operator']) { ?>
                <a href="#" class="tip-bottom" style="cursor: default">
                    <i class="fa fa-home"></i>
                    <span>Home</span>
                    <i class="fa fa-angle-right"></i>
                    <span class="title-2"></span>
                    <i class="fa fa-angle-right"></i>
                    <span class="title-3"></span>
                </a>
            <?php } else { ?>
                <a href="#" class="tip-bottom processing_task" link="<?php echo $output['processing_task']['url']?>" style="cursor: pointer;color: red;font-weight: 600">
                    <i class="fa fa-tasks"></i>
                    <span>Processing Task: </span>
                    <span id="task_name"><?php echo $output['processing_task']['title'] ?></span>
                </a>
            <?php } ?>
        </div>
    </div>
    <!--End-breadcrumbs-->
    <?php if ($output['is_operator']) { ?>
        <video id="task-hint" src="resource/video/hint.mp3" style="display: none" preload="auto"></video>
    <?php }?>
    <iframe src="" id="iframe-main" frameborder='0' style="width:100%;"></iframe>
</div>
