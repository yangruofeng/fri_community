<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>Credit</h3>
      <ul class="tab-base">
        <li><a class="current"><span>List</span></a></li>
      </ul>
    </div>
  </div>
  <div class="container">
    <div class="table-form">
      <div class="business-condition">
        <form class="form-inline" id="frm_search_condition">
          <table class="search-table">
            <tr>
              <td>
                <div class="form-group">
                  <label for="exampleInputName2">GUID</label>
                  <input type="text" class="form-control" name="item" id="item">
                </div>
              </td>
              <td>
                <div class="form-group">
                  <label for="exampleInputName2">Name</label>
                  <input type="text" class="form-control" name="name" id="name">
                </div>
              </td>
              <td>
                <div class="form-group">
                  <label for="exampleInputName2">Credit Status</label>
                  <select class="form-control">
                    <option>Credit</option>
                    <option>Never give credit</option>
                  </select>
                </div>
              </td>
              <td>
                <div class="input-group">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                        <i class="fa fa-search"></i>
                        <?php echo 'Search';?>
                    </button>
                  </span>
                </div><!-- /input-group -->
              </td>
            </tr>
          </table>
        </form>
      </div>
      <hr>
      <div class="business-content">
        <div class="business-list">

        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 10;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var item = $('#item').val(), name =  $('#name').val();

        yo.dynamicTpl({
            tpl: "loan/credit.list",
            dynamic: {
                api: "loan",
                method: "getCreditList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, item: item, name: name}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
