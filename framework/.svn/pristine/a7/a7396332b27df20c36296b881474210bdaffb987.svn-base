<style>
.easyui-panel {
  overflow-y: hidden;
  text-align: right;
  background-color: #fff;
}
.pagination {
  margin: 0;
}
.pagination-info{
    margin: 5px 16px 0 20px;
}
.easyui-pagination table tr td{
    height: 39px!important;
}
.pagination-page-list {
  font-size: 13px;
}
.l-btn-text {
  line-height: 18px;
}
</style>
<hr/>
<div class="easyui-panel" style="">
    <div class="easyui-pagination" data-options="
                    total: <?php echo $data['total']?>,
                       pageNumber: <?php echo $data['pageNumber']?>,
                       pageSize: <?php echo $data['pageSize']?>,
                       layout:['list','sep','first','prev','links','next','last','sep'],
                       onSelectPage: function(pageNumber, pageSize){
                           <?php echo $content_pager_function?:'btn_search_onclick'?>(pageNumber,pageSize);
                       },

                "></div>
</div>

<script>
    $(document).ready(function(){
        $.parser.parse($(".easyui-panel"));
    });
</script>
