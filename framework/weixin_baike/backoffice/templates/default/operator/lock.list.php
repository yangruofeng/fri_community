<?php
$loanApplyStateLang = enum_langClass::getMemberStateLang();
?>

<style>
    .content{
        margin-top:50px;
    }
</style>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Lock </h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'requestLock', array(0), false, BACK_OFFICE_SITE_URL)?>"><span>request</span></a></li>
                <li><a  class="current"><span>request list</span></a></li>
            </ul>
        </div>
    </div>
    <div class="business-condition content">
        <form class="form-inline" id="frm_search_condition">
            <table class="search-table">
                <tbody>
                <tr>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search_text" style="height: 34px" name="search_text" placeholder="Search for name">
                            <span class="input-group-btn">
                                             <button type="button" class="btn btn-default square" id="btn_search_list" onclick="btn_search_onclick();">
                                              <i class="fa fa-search"></i>
                                              Search
                                            </button>
                                            </span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div style="padding: 0;border-bottom: 1px solid #D5D5D5;">
        <table class="table table-bordered">
            <thead>
            <tr class="table-header">
                <td>No.</td>
                <td>ID</td>
                <td>Account</td>
                <td>Client Name</td>
                <td>Contact Phone</td>
                <td>Apply Time</td>
                <td>State</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $i = 0; foreach ($output['data'] as $row) { $i++ ?>
                <tr>
                    <td>
                        <?php echo $i ?>
                    </td>
                    <td>
                        <?php echo $row['obj_guid'] ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['display_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                    <td>
                        <?php echo $loanApplyStateLang[$row['member_state']] ?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>

</div>

<script>
    function btn_search_onclick() {

        var _search_text = $('#search_text').val();

        yo.loadData({

        });
    }


</script>






