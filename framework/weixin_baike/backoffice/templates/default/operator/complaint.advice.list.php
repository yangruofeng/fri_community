<?php
$complaintAdviceStateLang = enum_langClass::getComplaintAdviceStateLang();
?>

<div class="content guarantor" style="padding: 0;border-bottom: 1px solid #D5D5D5;">
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td>Type</td>
            <td>Contact Name</td>
            <td>Title</td>
            <td>Contact Phone</td>
            <td>Create Time</td>
            <td>State</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php  foreach ($data['data'] as $row) {?>
            <tr>
                <td>
                    <?php echo $row['type'] ?>
                </td>
                <td>
                    <?php echo $row['contact_name'] ?>
                </td>
                <td>
                    <?php echo $row['title'] ?>
                </td>
                <td>
                    <?php echo $row['contact_phone'] ?>
                </td>
                <td>
                    <?php echo $row['create_time'] ?>
                </td>
                <td>
                    <?php echo $complaintAdviceStateLang[$row['state']]?>
                </td>
                <td>
                    <a class="btn btn-default" href="<?php echo getUrl('operator','details',array('uid'=>$row['uid']),false, BACK_OFFICE_SITE_URL)?>"><?php echo 'Details'?></a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>

<?php include_once(template("widget/inc_content_pager"));?>