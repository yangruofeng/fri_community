<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Client</h3>
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
                            <label for="exampleInputName2">CID</label>
                            <input type="text" class="form-control" name="member_item" id="member_item">
                          </div>
                        </td>
                        <td>
                          <div class="form-group">
                            <label for="exampleInputName2">Name</label>
                            <input type="text" class="form-control" name="username" id="username">
                          </div>
                        </td>
                        <td>
                          <div class="form-group">
                            <label for="exampleInputName2">Phone</label>
                            <div style="display: inline-block;">
                              <select class="form-control" name="country_code" id="country_code" style="float:left;">
                                <option value="855">+855</option>
                                <option value="84">+84</option>
                                <option value="86">+86</option>
                              </select>
                              <input type="text" class="form-control" name="phone" id="phone" style="margin-left: -1px;">
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="form-group">
                            <label>
                              <input type="checkbox" name="ck" id="ck"> Register On Today
                            </label>
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
            <div class="business-list"></div>
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

        var member_item = $('#member_item').val(), member_name =  $('#username').val(), ck = $('#ck').is(':checked'), country_code = $('#country_code').val(), phone = $('#phone').val();
        mobile = phone ? '+' + country_code + phone : '';
        yo.dynamicTpl({
            tpl: "client/client.list",
            dynamic: {
                api: "client",
                method: "getClientList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, member_item: member_item, member_name: member_name, ck: ck, phone: mobile}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
