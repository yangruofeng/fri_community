<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header" style="background-color: #EEE">
            <td>Branch</td>
            <td>Co Name</td>
            <td>Tasks(Client)</td>
            <td>Tasks(Request Loan)</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['branch_name'] . ' ' . $row['depart_name']; ?><br>
                    </td>
                    <td>
                        <?php echo $row['user_name']; ?><br>
                    </td>
                    <td>
                        <?php echo $row['member_num']?><br>
                    </td>
                    <td>
                        <?php echo $row['apply_num']?><br>
                    </td>
                    <td>
                        <a href="#" style="margin-right: 5px" onclick="selectOc(<?php echo $row['uid']?>, '<?php echo $row['user_name']?>')"><i class="fa fa-check"></i>Select</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">
                   Null<br>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>