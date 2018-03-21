<div id="sidebar" style="OVERFLOW-Y: auto; OVERFLOW-X:hidden;">
    <ul>
        <?php foreach ($output['menu_items'] as $k_c => $s_item) {
            $first_child = array_shift($s_item['child']);
            $args = explode(',', $first_child['args']); ?>
            <li class="submenu">
                <a href="#" class="menu_a"
                   link="<?php echo getUrl($args[1], $args[2], array(), false, C('site_root') . DS . $args[0]); ?>">
                <img class="tab-default"
                     src="<?php echo ENTRY_COUNTER_SITE_URL . '/resource/img/counter-icon/tab_' . $k_c . '.png' ?>">
                <img class="tab-active"
                     src="<?php echo ENTRY_COUNTER_SITE_URL . '/resource/img/counter-icon/tab_' . $k_c . '_active.png' ?>">
                <span><?php echo $s_item['title'] ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<!--sidebar-menu-->