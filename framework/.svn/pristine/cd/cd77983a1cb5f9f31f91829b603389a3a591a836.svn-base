<style>
    .fa.fa-exchange, .fa.fa-angle-double-right, .fa.fa-angle-double-left {
        color: #32BC61;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Currency</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('financial', 'setExchangeRate', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Setting</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <div>
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td><?php echo 'Currency';?></td>
                            <td><?php echo 'Buy Rate';?></td>
                            <td><?php echo 'Sell Rate';?></td>
                            <td><?php echo 'Update Name';?></td>
                            <td><?php echo 'Update Time';?></td>
                            <td>Function</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach($output['exchange_rate_list'] as $key => $row){ ?>
                            <tr>
                                <td>
                                    <?php echo $row['uid']; ?><br/>
                                </td>
                                <td>
                                    <?php echo $row['first_currency']; ?>
                                    <i class="fa fa-exchange"></i>
                                    <?php echo $row['second_currency']; ?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($row['buy_rate_unit']); ?>
                                    <i class="fa fa-angle-double-right"></i>
                                    <?php echo ncPriceFormat($row['buy_rate']); ?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($row['sell_rate']); ?>
                                    <i class="fa fa-angle-double-left"></i>
                                    <?php echo ncPriceFormat($row['sell_rate_unit']); ?>
                                </td>
                                <td>
                                    <?php echo $row['update_name']; ?>
                                </td>
                                <td>
                                    <?php echo timeFormat($row['update_time']); ?>
                                </td>
                                <td>
                                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('financial', 'setExchangeRate', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                        <i class="fa fa-edit"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




