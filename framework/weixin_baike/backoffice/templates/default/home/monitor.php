<div>
    <audio src="resources/voice/alert.mp3" id="ad_default"></audio>
</div>
<style>
    .monitor-flash{
        background-color: #801010;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Monitor</h3>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-10">
            <?php $groups=$output['monitor_items'];?>
            <div id="div_monitor">
                <ul class="list-inline">
                    <?php foreach($groups as $group){?>
                        <li>
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-info" style="font-size: 18px;font-weight: bold">
                                    <?php echo $group['title']?>
                                </li>
                                <?php foreach($group['items'] as $item){?>
                                    <li class="list-group-item" style="height: 150px">
                                        <div id="div_monitor_item_<?php echo $item['key']?>" class="popover top" style="position: relative;display: block;float: left;width: 200px;margin: 20px;z-index: 500">
                                            <div class="popover-title">
                                                <a href="<?php echo $item['url'] ?>" my_title="<?php echo $item['title'];?>">
                                                    <?php echo $item['title']?>
                                                    <span class="badge <?php echo $item['key']?>_badge_monitor" style="float: right">0</span>
                                                </a>
                                            </div>
                                            <div id="div_monitor_content_<?php echo $item['key']?>" class="popover-content"><?php echo $item['desc']?:'Null<br/>&nbsp;'?></div>
                                        </div>
                                    </li>
                                <?php }?>
                            </ul>
                        </li>
                    <?php }?>
                </ul>

            </div>
        </div>
        <div class="col-sm-2">
            <div style="top: 0;border: 0;right: 0;">
                <ul class="list-group" id="ul_msg">
                    <li class="list-group-item list-group-item-info">
                        Message
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    var _ad_playing=false;//是否正在播放
    $(document).ready(function(){
        $("#ad_default").bind("ended",function(){
            _ad_playing=false;
        });
        $("#ad_default").bind("play",function(){
            _ad_playing=true;
        });
        monitor.start();
    });
</script>
<!------******************Monitor**************************-->
<script>
    var monitor={};
    monitor.clear=function(){
        monitor.div.html();
    };
    monitor.start=function(){
        monitor.getData();
    };
    monitor.stop=function(){
        if(monitor.timer_id){
            clearTimeout(monitor.timer_id);
        }
    };
    monitor.play_voice=true;
    monitor.show_alert=true;
    monitor.timer_id=null;
    monitor.last_time=null;
    monitor.getData=function(){
        var _url = '<?php echo ENTRY_API_SITE_URL . DS . 'monitor.get.php'?>';
        $.post(_url, {last_time:monitor.last_time}, function (_o) {
            _o = $.parseJSON(_o);
            if(!_o.STS) return false;
            monitor.last_time=_o.last_time;
            var _monitor_items=_o.data;
            var _new_task=0;
            for(var _monitor_key in _monitor_items){
                $("."+_monitor_key+"_badge_monitor").text(_monitor_items[_monitor_key].count);
                var _div_monitor_item=$("#div_monitor_item_"+_monitor_key);
                if(_monitor_items[_monitor_key].count>0){
                    _div_monitor_item.addClass('monitor-alert');
                }else{
                    _div_monitor_item.removeClass('monitor-alert');
                }

                $("#div_monitor_content_"+_monitor_key).html(_monitor_items[_monitor_key].content);

                if(_monitor_items[_monitor_key].new>0){
                    _new_task+=_monitor_items[_monitor_key].new;
                    var _li='<li class="list-group-item" style="font-size: 10px">'+'<p>'+_monitor_items[_monitor_key]['title']+' <code>'+_o.last_time+'</code></p>'+'</li>';
                    if($("#ul_msg").find("li").length>=10){
                        $("#ul_msg li:last-child").remove();
                    }
                    $("#ul_msg li:first-child").after(_li);
                    _div_monitor_item.data("flash_time",0);
                    //闪动效果
                    _flashItem(_div_monitor_item);
                }
            }

            //计算新的task
            if(_new_task>0){
                if(monitor.play_voice==true){
                    if(_ad_playing==false){
                        $("#ad_default").trigger("play");
                    }
                }
            }
            monitor.timer_id=setTimeout(function(){
                monitor.getData();
            },1000*30);//30秒请求一次
        });
    }
    function _flashItem(_div_fi){
        if(parseInt(_div_fi.data("flash_time"))%2==0){
            _div_fi.addClass("monitor-flash");
        }else{
            _div_fi.removeClass("monitor-flash");
        }
        _div_fi.data("flash_time",parseInt(_div_fi.data('flash_time'))+1);
        if(parseInt(_div_fi.data("flash_time"))<10){
            setTimeout(function(){
                _flashItem(_div_fi);
            },500);
        }
    }

</script>
