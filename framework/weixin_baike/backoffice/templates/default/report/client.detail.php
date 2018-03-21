<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    var uid = '<?php echo intval($output['uid'])?>';
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.uid = uid;

        yo.dynamicTpl({
            tpl: "report/client.detail.list",
            dynamic: {
                api: "report",
                method: "getClientDetail",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
