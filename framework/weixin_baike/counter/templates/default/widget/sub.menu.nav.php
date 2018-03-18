<div class="content-nav">
    <ul class="nav nav-tabs">
        <?php foreach($output['sub_menu'] as $menu){
            $args = explode(',', $menu['args']);
            ?>
            <li role="presentation" class="<?php echo ($_GET['op'] == $args[2] || $output['show_menu'] == $args[2]) ? 'active' : ''; ?>">
                <a href="<?php echo ($_GET['op'] == $args[2] || $output['show_menu'] == $args[2]) ? '#' : getUrl($args[1], $args[2], array(), false, C('site_root') . DS . $args[0]);?>"><?php echo $menu['title']?></a>
            </li>
        <?php }?>
    </ul>
</div>
