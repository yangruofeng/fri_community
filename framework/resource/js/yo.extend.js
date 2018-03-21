/**
 * Created by tim on 6/4/2015.
 */
var ColDataType={
    varchar:"varchar",
    bool:'tinyint',
    datetime:'datetime',
    int:'int',
    decimal:'decimal',
    time:'time',
    float:'float',
    date:'date',
    timestamp:'timestamp',
    longblob:'longblob',
    text:'text',
    enums:'enum'
};
var ColEditorType={
    text:"text",
    checkbox:'checkbox',
    radio:'radio',
    file:'file',
    int:"int",
    number:'number',
    time:'time',
    textarea:'textarea',
    datetime:'datetime',
    combo:'combo',
    treeCombo:'treecombo',
    picker:'picker',
    img:'img',
    linker:'linker',
    hidden:'hidden'
};
var ColViewType={
    text:"text",
    int:'int',
    number:'number',
    percent:'percent',
    datetime:'datetime',
    checkbox:'checkbox',
    file:'file',
    select:'select',
    img:'img',
    linker:'linker',
    label:"label",
    password:"password",
    radio:"radio",
    hidden:'hidden'
};
/******************************************处理dialog*******/
yo.dialog={};
yo.dialog.close=function(){
    var _dialog=$("#yo_dialog");
    _dialog.dialog('close');
    _dialog.html("");
};
yo.dialog.template=function(_args){
    var _dialog=$("#yo_dialog");
    if(_dialog.length==0) throw Error("Dialog Container Not Found");
    if(!_args.content) throw Error("Need to Set Content for Dialog");
    _args.content=_args.content.html();
    _args.content=$(_args.content);
    var _config= $.extend({},_args);
    _config.title=_args.title?_args.title:"Dialog";
    _config.top=_args.top?_args.top:0;
    // _config.height=_args.height?_args.height:"500";
    _config.modal=_args.modal?_args.modal:true;
    _config.cache=false;
    _dialog.html(_args.content);
    _dialog.dialog(_config);
    $('.form_date').datepicker({
        format:"yyyy-mm-dd"
    });


};
yo.dialog.show=function(_args){
    var _dialog=$("#yo_dialog");
    if(_dialog.length==0) throw Error("Dialog Container Not Found");
    if(!_args.content) throw Error("Need to Set Content for Dialog");
    var _config= $.extend({},_args);
    _config.title=_args.title?_args.title:"Dialog";
    _config.width=_args.width?_args.width:"600";
    _config.top=$(document).scrollTop()+50;
   // _config.height=_args.height?_args.height:"500";
    _config.modal=_args.modal?_args.modal:true;
    if(!_config.buttons){
        _config.buttons=[{text:"Close",handler:function(){
           yo.dialog.close();
        }}];
    }


    _dialog.html(_args.content);
    _dialog.dialog(_config);
    $('.form_date').datepicker({
       format:"yyyy-mm-dd"
    });
    if(_args.content){
        _args.content.show();


    }
};
yo.dialog.form=function(){
    var _dialog=$("#yo_dialog");
    var _frm=_dialog.find("form").first();
    return _frm;
};
yo.dialog.waiting=function(){
    var _dialog=$("#yo_dialog");
    var _wd=_dialog.parent();
    if(!_wd) return;

    _wd.waiting();
};
yo.dialog.unmask=function(){
    var _dialog=$("#yo_dialog");
    var _wd=_dialog.parent();
    _wd.unmask();

};
yo.dialog.prompt=function(_title,_desc,_fn,_default_value){
    if(!_default_value) _default_value="";
    var _frm='<form onsubmit="return false;"><strong>'+_desc+'</strong><div><input name="txt_prompt" value="'+_default_value+'" class="form-control" style="width: 500px"/></div></form>';
    _frm=$(_frm);
    yo.dialog.show({
        content:_frm,
        title:_title,
        buttons:[{text:"Submit",handler:function(){
            var _frm=yo.dialog.form();
            var _values=_frm.getValues();
            yo.dialog.close();
            if(_fn){
                _fn(_values.txt_prompt);
            }
        }},{text:"Cancel",handler:function(){
            yo.dialog.close();
            if(_fn){
                _fn(null);
            }
        }}]
    });
};

/*********************************处理form***********************************/
yo.generateForm=function(_args){
    var _cols=_args.cols;
    if(_cols.length==0) return "";
    var parts=[];
    if(_args.horiz){
        parts.push('<form onsubmit="return false;" class="form-horizontal yo-form">');
    }else{
        parts.push('<form onsubmit="return false;" class="yo-form">');
    }
    for(var _i in _cols){
        var _col=_cols[_i];
        var _str_readonly="";

        if(_col.is_prikey && _args.is_edit){
            _col.is_readonly=true;
        }
        if(_col.is_readonly){
            _str_readonly=' readonly="true" ';
        }
        if(_col.is_autoid){
            _col.hidden=true;
        }

        if(!_col['default_value'] || 'undefined'==_col['default_value']){
            _col['default_value']='';
        }else{
            _col.default_value='value="'+_col.default_value+'"';
        }
        if(_col.dataType==ColDataType.date){
            _col.default_value='value="'+app.today()+'"';
        }
        if(_col.dataType==ColDataType.datetime){
            _col.default_value='value="'+app.now()+'"';
        }



        var _str_required="";
        var _str_placeholder="";
        if(_args.require_cols){
            if(_args.require_cols[_col.name]){
                _col.is_null=true;
            }
        }
        if(_col.is_notnull){
            if(!_args.hidePlace){
                _str_placeholder=' placeholder="Must Input "'+_col.caption+' "';
            }
            _str_required='required="true"';
        }
        if(_col.placeholder){
            if(!_args.hidePlace){
                _str_placeholder=' placeholder="'+_col.placeholder+' "';
            }
        }
        if(_col.hidden){
            parts.push('<input type="hidden" name="'+_col.name+'"'+_col.default_value+' >');
            continue;
        }
        if(_col.viewType==ColViewType.hidden){
            parts.push('<input type="hidden" name="'+_col.name+'"'+_col.default_value+' >');
            continue;
        }
        parts.push('<div class="form-group">');
        var _lbl_ext_class="";
        var _div_ext_before="";
        var _div_ext_after="";
        var _offset_ext_before="";
        if(_args.horiz){
            _lbl_ext_class="col-sm-4";
            _div_ext_before='<div class="col-sm-8">';
            _offset_ext_before='<div class="col-sm-offset-4 col-sm-8">';
            _div_ext_after='</div>';
        }

        switch (_col.dataType){
            case ColDataType.date:case ColDataType.datetime:
                parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                parts.push(_div_ext_before);
                //parts.push('<div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">');
                parts.push('<input class="form-control form_date" name="'+_col.name+'" '+_col.default_value+'  size="16" type="text" value="" readonly>');
                //parts.push('<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>');
                //parts.push('</div>');


//                parts.push('<input type="text" class="form-control easyui-datebox" name="'+_col.name+'" dataType="date" '+_col.default_value+' '+_str_readonly+' '+_str_required+' '+_str_placeholder+'>');
                break;
            case ColDataType.int:
                switch(_col.viewType){
                    case ColViewType.select:
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<select name="'+_col.name+'" class="form-control">');
                        if(_col.list){
                            for(var _k in _col.list){
                                var _opt_value=_col.key_fld?_col.list[_k][_col.key_fld]:_k;
                                var _opt_text=_col.value_fld?_col.list[_k][_col.value_fld]:_col.list[_k];
                                parts.push('<option value="'+_opt_value+'">'+_opt_text+'</option>');
                            }
                        }
                        parts.push('</select>');
                        break;
                    case ColViewType.radio:
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<div class="form-inline">');
                        if(_col.list){
                            for(var _k in _col.list){
                                var _opt_value=_col.key_fld?_col.list[_k][_col.key_fld]:_k;
                                var _opt_text=_col.value_fld?_col.list[_k][_col.value_fld]:_col.list[_k];
                                parts.push('<input type="radio" name="'+_col.name+'" value="'+_opt_value+'">'+_opt_text);
                            }
                        }
                        parts.push('</div>');
                        break;
                    default :
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        var _max=_col.max?'max="'+_col.max+'"':'';
                        var _min=_col.max?'min="'+_col.min+'"':'';
                        parts.push('<input type="number" class="form-control" '+_max+' '+_min+' digits="true" name="'+_col.name+'" '+_col.default_value+'  '+_str_readonly+' '+_str_required+' '+_str_placeholder+'>');
                        break;
                }
                break;
            case ColDataType.float:case ColDataType.decimal:
                parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                parts.push(_div_ext_before);
                var _max=_col.max?'max="'+_col.max+'"':'';
                var _min=_col.max?'min="'+_col.min+'"':'';
                parts.push('<input type="number" class="form-control"  '+_max+' '+_min+'  number="true" name="'+_col.name+'" '+_col.default_value+'  '+_str_readonly+' '+_str_required+' '+_str_placeholder+'>');
                break;
            case ColDataType.text:case ColDataType.longblob:
                if(_args.horiz){
                    parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                    parts.push(_div_ext_before);
                }

                parts.push('<textarea rows=3 class="form-control" name="'+_col.name+'" '+_col.default_value+'  '+_str_readonly+' '+_str_required+' '+_str_placeholder+'></textarea>');
                break;
            case ColDataType.bool:
                parts.push(_div_ext_before);
                parts.push('<div class="checkbox">');
                parts.push('<label><input type="checkbox" name="'+_col.name+'">'+_col.caption+'</label>');
                parts.push('</div>');
                break;
            default :
                switch (_col.viewType){
                    case ColViewType.img://todo:优化
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<input type="file" name="'+_col.name+'" >');
                        break;
                    case ColViewType.file://todo:优化
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<input type="file" name="'+_col.name+'" id="'+_col.id+'" >');
                        break;
                    case ColViewType.select:
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<select name="'+_col.name+'" class="form-control">');
                        if(_col.list){
                            for(var _k in _col.list){
                                var _opt_value=_col.key_fld?_col.list[_k][_col.key_fld]:_k;
                                var _opt_text=_col.value_fld?_col.list[_k][_col.value_fld]:_col.list[_k];
                                parts.push('<option value="'+_opt_value+'">'+_opt_text+'</option>');
                            }
                        }
                        parts.push('</select>');
                        break;
                    case ColViewType.label:
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<input type="text" class="form-control form-control-read" '+_col.default_value+' readonly="true" name="'+_col.name+'">');
                        break;
                    case ColViewType.password:
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<input type="password" class="form-control" name="'+_col.name+'"   '+_str_readonly+' '+_str_required+' '+_str_placeholder+'>');
                        break;
                    default :
                        parts.push('<label class="'+_lbl_ext_class+' control-label">'+_col.caption+'</label>');
                        parts.push(_div_ext_before);
                        parts.push('<input type="text" class="form-control" name="'+_col.name+'" '+_col.default_value+'  '+_str_readonly+' '+_str_required+' '+_str_placeholder+'>');
                        break;
                }
                break;
        }
        parts.push(_div_ext_after);
        parts.push('</div>');
    }
    var _frm=$(parts.join("\n"));
    /*_frm.find("input[name][dataType=date]").each(function(){
        $(this).datepicker({format:'yyyy-mm-dd'});
    });
    */
    //todo:批量处理事件
    return _frm;

};


/*******************************************************************************yo.grid***/
yo.grid={};
yo.grid.gridList={};
yo.grid.getGrid=function(_id){
    var _o=yo.grid.gridList[_id];
    if(_o) return(_o.grid);
    return null;
};
yo.grid.getContainer=function(_id){
    var _o=yo.grid.gridList[_id];
    if(_o) return(_o.container);
    return null;

};
yo.grid.getConfig=function(_id){
    var _o=yo.grid.gridList[_id];
    if(_o) return(_o.config);
    return null;
};
yo.grid.create=function(_conf){
    if(!_conf.id) throw Error("Need to Set Div");
    yo.grid.remove(_conf.id);

    var _container=$("#"+_conf.id);
    if(_container.length==0) throw Error("Invalid Div Name for Grid");
    var _dg=$("<div style='height: auto'></div>");
    _container.html(_dg);
    _container.data("grid",_dg);
    var _config= $.extend({
        singleSelect: true,
        width:"100%",
        height:"100%",
        pagination:true,
        fitColumns:true,
        pageSize:(_conf.pageSize?_conf.pageSize:20)
    },_conf);
    return yo.grid.loadGrid(_container,_config);
};

yo.grid.loadGrid=function(_container,_config){
    _container.waiting();
    var _dg=_container.data("grid");
    var _load=_config.loadData;//这是定义的外部赋值方法
    if(!_load){//如果没定义，则认为数据来自于_config
        _config.pagination=false;

        if(_config.actions){
            var _col={field:"",title:"",formatter:""};
            _col.field="row_action";
            _col.title="Action";

            _col.formatter=function(_value,_r,_index){
                var _f_str="";
                for(var _x in _config.actions){
                    var _act_item=_config.actions[_x];
                    if(!_act_item.key) continue;
                    if(_act_item.onCheck){
                        if(!_act_item.onCheck(_value,_r,_index)) continue;
                    }
                    if(_act_item.icon){
                        _f_str+= '&nbsp;&nbsp;<a href="#" class="yo-grid_row_action" title="'+_act_item.title+'" action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span class="glyphicon '+_act_item.icon+'"></span></a>';
                    }else{
                        var _act_btn_txt=_act_item.title?_act_item.title:_act_item.key;
                        var _btn_class=_act_item.btn?_act_item.btn:"btn-danger";
                        _f_str+='&nbsp;&nbsp;<a href="#" class="btn '+_btn_class+' btn-xs yo-grid_row_action" action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span>'+_act_btn_txt+'</span></a>';
                    }
                }
                return _f_str;
            };
            if(_config.action_last){ //放在最后
                _config.columns.push(_col);
            }else{//默认放在最前
                _config.columns.unshift(_col);
            }
        }
        _config.columns=[_config.columns];

        _dg.datagrid(_config);
        if(!yo.grid.getGrid(_config.id)){
            yo.grid.gridList[_config.id]={container:_container,grid:_dg,config:_config};
        }
        yo.grid.initAction(_config.id);
        _dg.datagrid("resize",{});
        _container.unmask();
    }else{
        var _pageNumber=_config.pageNumber?_config.pageNumber:1;
        var _pageSize=_config.pageSize?_config.pageSize:20;

        _load(function(_o){
            _config.data=_o.data;
            if(!_config.is_load){
                _config.columns=_config.columns?_config.columns:_o.columns;
                //处理行操作
                if(_config.actions){
                    var _col=null;
                    if(_config.action_col){
                        for(var _zz in _config.columns){
                            if(_config.columns[_zz].field==_config.action_col){
                                _col=_config.columns[_zz];
                                break;
                            }
                        }
                        if(!_col){
                            throw Error("Invalid Action-Col!");
                        }
                    }else{
                        _col={field:"",title:"",formatter:""};
                        _col.field="row_action";
                        _col.title="Action";
                    }

                    _col.formatter=function(_value,_r,_index){
                        var _f_str="";
                        if(_config.action_type=="menu"){
                            var _strMenu=['<div class="btn-group">'];
                            if(!_config.action_col){
                                _strMenu.push('<button type="button" class="btn btn-default">Action</button>');
                            }
                            _strMenu.push('<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">');
                            _strMenu.push('<span class="caret"></span>');
                            _strMenu.push('</button>');
                            _strMenu.push('<ul class="dropdown-menu">');
                            for(var _x in _config.actions){
                                var _act_item=_config.actions[_x];
                                if(!_act_item.key) continue;
                                if(_act_item.onCheck){
                                    if(!_act_item.onCheck(_value,_r,_index)) continue;
                                }
                                if(_act_item.icon){
                                    _strMenu.push('<li><a href="#" class="yo-grid_row_action" title="'+_act_item.title+'" action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span class="glyphicon '+_act_item.icon+'"></span></a></li>');
                                }else{
                                    var _act_btn_txt=_act_item.title?_act_item.title:_act_item.key;
                                    var _btn_class=_act_item.btn?_act_item.btn:"";
                                    _strMenu.push('<li><a href="#" class="'+_btn_class+' btn-xs yo-grid_row_action" action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span>'+_act_btn_txt+'</span></a></li>');
                                }
                            }
                            _strMenu.push('</ul>');
                            _strMenu.push('</div>');
                            if(typeof (_value)=="undefined") _value="";
                            _f_str=_strMenu.join("\n")+_value;
                        }else{
                            for(var _x in _config.actions){
                                var _act_item=_config.actions[_x];
                                if(!_act_item.key) continue;
                                if(_act_item.onCheck){
                                    if(!_act_item.onCheck(_value,_r,_index)) continue;
                                }
                                if(_act_item.icon){
                                    _f_str+= '&nbsp;&nbsp;<a href="#" class="yo-grid_row_action" title="'+_act_item.title+'"  action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span class="glyphicon '+_act_item.icon+'"></span></a>';
                                }else{
                                    var _act_btn_txt=_act_item.title?_act_item.title:_act_item.key;
                                    var _btn_class=_act_item.btn?_act_item.btn:"btn-danger";
                                    _f_str+='&nbsp;&nbsp;<a href="#" class="btn '+_btn_class+' btn-xs yo-grid_row_action" action_key="'+_act_item.key+'" onclick="return false;" action_index="'+_index+'"><span>'+_act_btn_txt+'</span></a>';
                                }
                            }
                        }

                        return _f_str;


                    };
                    if(!_config.action_col){
                        if(_config.action_last){ //放在最后
                            _config.columns.push(_col);
                        }else{//默认放在最前
                            _config.columns.unshift(_col);
                        }

                    }
                }
                _config.columns=[_config.columns];
                _dg.datagrid(_config);
            }else{
                _dg.datagrid("loadData",_config.data);
            }


            var _total=_o.total?_o.total:_o.data.length;
            var _real_p=Math.ceil(_total/_pageNumber);//重新计算页数
            if(_real_p<_pageNumber) _pageNumber=_real_p;


            if(_config.pagination){
                _dg.datagrid("getPager").pagination({
                    total:_total,
                    pageNumber:_pageNumber,
                    onSelectPage: function(pageNumber, pageSize){
                        //alert(pageNumber);
                        if(!_config.param) _config.param={};
                        _config.pageSize=pageSize;
                        yo.grid.query({
                            id:_config.id,
                            pageSize:pageSize,
                            pageNumber:pageNumber
                        })
                    }
                });
            }



            _config.is_load=true;
            if(!yo.grid.getGrid(_config.id)){
                yo.grid.gridList[_config.id]={container:_container,grid:_dg,config:_config};
            }
            yo.grid.initAction(_config.id);
            _dg.datagrid("resize",{width:_config.width,height:_config.height});
            _container.unmask();

        },_config.param,_pageNumber,_pageSize)
    }
    return _dg;
};
yo.grid.initAction=function(_id){
    var _container=yo.grid.getContainer(_id);
    if(!_container) throw Error("init Grid Not Yet");
    _container.find(".yo-grid_row_action").off("click").on("click", function (evt) {
        var _btn=$(this);
        var _act_key=_btn.attr("action_key");
        var _act_index=_btn.attr("action_index");
        var _grid=yo.grid.getGrid(_id);
        var _rows=_grid.datagrid("getRows");
        var _row=_rows[_act_index];

        var _config=yo.grid.getConfig(_id);
        var _actions=_config.actions;
        if(_actions){
            for(var _k in _actions){
                var _act_item=_actions[_k];
                if(_act_item.key==_act_key){
                    var _call=_act_item.act;
                    _call(_row,_act_index,_id,_btn);
                    break;
                }
            }
        }
    });
};
yo.grid.query=function(_conf){
    if(!_conf.id) throw Error("Need to Set Div");
    var _old_conf=yo.grid.getConfig(_conf.id);
    if(!_old_conf) Error("Init "+_conf.id+" Not Yet");
    var _config= $.extend(_old_conf,_conf);

    yo.grid.gridList[_conf.id].config=_config;
    var _container=yo.grid.getContainer(_conf.id);
    yo.grid.loadGrid(_container,_config);
};
yo.grid.reload=function(_id){
    yo.grid.query({
        id:_id,
        pageNumber:1
    })
};
yo.grid.remove=function(_id){
    yo.grid.gridList[_id]=null;
};
yo.grid.subRow=function(e){//处理子表
    var _nextTr=$(e).closest('tr').next('tr');
    if(_nextTr.hasClass('tr-subgrid')){
        if(_nextTr.data("show_sts")==1){
            _nextTr.hide();
            _nextTr.data("show_sts",0);
        }else{
            _nextTr.show();
            _nextTr.data("show_sts",1);
        }
        return false;
    }else{
        var _new_tr=$("<tr class='tr-subgrid datagrid-row'></tr>");
        var _new_td=$("<td colspan='12' style='padding-left: 50px'></td>");
        var _new_div=$("<div style='padding:8px;'></div>");
        _new_tr.html(_new_td);
        _new_td.html(_new_div);
        //_new_div.html($.toJSON(_row));
        $(e).closest('tr').after(_new_tr);
        _nextTr=$(e).closest('tr').next('tr');
        _nextTr.data("show_sts",1);
        return _new_div;
    }
};
/*
yo.grid.formatColumns=function(columns){
    var _rt=[];
    for(var _c in columns){
        var _ci=columns[_c];
        var _new_ci={field:_ci.name,title:_ci.caption};
        switch(_ci.name){
            case "order_sn":
                _new_ci.formatter=function(_cell,_row,_index){
                    return '<a href="#" onclick="app.showOrderDetail(\''+_row['order_id']+'\')">'+_cell+'</a>';
                };
                break;
            case "store_name":
                _new_ci.formatter=function(_cell,_row,_index){
                    return '<a href="#" onclick="app.showStoreDetail(\''+_row['store_id']+'\')">'+_cell+'</a>';
                };
                break;
            case "buyer_name":
                _new_ci.formatter=function(_cell,_row,_index){
                    return '<a href="#" onclick="app.showBuyerDetail(\''+_row['buyer_id']+'\')">'+_cell+'</a>';
                };
                break;
            case "goods_items":
                _new_ci.title="Products";
                _new_ci.formatter=function(_cell,_row,_index){
                    var _goods_image=_row['goods_image_url'].split("@");
                    var _goods_link=_row['goods_image_link'].split("@");
                    var _str_ft="";
                    for(var _x in _goods_image){
                        _str_ft=_str_ft+'<div style="float:left;border: double"><span><i></i><a target="_blank" href="'+_goods_link[_x]+'"><img src="'+_goods_image[_x]+'" /></a></span></div>'
                    }
                    return _str_ft;
                };
                break;
            default :
                break;
        }
        _rt.push(_new_ci);
    }
    return  _rt;
};
*/
yo.grid.formatColumns=function(columns){
    var _rt=[];
    for(var _c in columns){
        var _ci=columns[_c];
        var _new_ci={field:_ci.name,title:_ci.caption};
        switch(_ci.name){
            case "order_sn":
                _new_ci.formatter=function(_cell,_row,_index){
                    return '<a href="#" onclick="app.showOrderDetail(\''+_row['order_id']+'\')">'+_cell+'</a>';
                };
                break;
            case "store_name":
                _new_ci.formatter=function(_cell,_row,_index){
                    if(_row['store_id']){
                        return '<a href="#" onclick="app.showStoreDetail(\''+_row['store_id']+'\')">'+_cell+'</a>';
                    }else{
                        return _cell;
                    }
                };
                break;
            case "buyer_name":
                _new_ci.formatter=function(_cell,_row,_index){
                    return '<a href="#" onclick="app.showBuyerDetail(\''+_row['buyer_id']+'\')">'+_cell+'</a>';
                };
                break;
            case "member_name":
                _new_ci.formatter=function(_cell,_row,_index){
                    if(_row['member_id']){
                        return '<a href="#" onclick="app.showBuyerDetail(\''+_row['member_id']+'\')">'+_cell+'</a>';
                    }else{
                        return _cell;
                    }

                };
                break;
            case "goods_items":
                _new_ci.title="Products";
                _new_ci.formatter=function(_cell,_row,_index){
                    var _goods_image=_row['goods_image_url'].split("@");
                    var _goods_link=_row['goods_image_link'].split("@");
                    var _str_ft="";
                    for(var _x in _goods_image){
                        _str_ft=_str_ft+'<div style="float:left;border: double"><span><i></i><a target="_blank" href="'+_goods_link[_x]+'"><img src="'+_goods_image[_x]+'" /></a></span></div>'
                    }
                    return _str_ft;
                };
                break;
            default :
                break;
        }
        _rt.push(_new_ci);
    }
    return  _rt;
};

/****************************** Selector ************************************/
yo.grid.selector={};
yo.grid.selector.create=function(_conf){
    var _id=_conf.id;
    var _is_enum=_conf.is_enum;
    var _cols=[];
    var _data=_conf.data;
    if(_is_enum){
        if(_conf.singleSelect==true){
            _cols.push({field:"grid_check_col",title:"",formatter:function(value, rowData, rowIndex){
                return '<input type="radio" name="grid_selector_rbn" id="grid_selector_rbn"' + rowIndex + ' index="'+rowIndex+'"    value="' + rowData.grid_value_col + '" />';
            }});
        }else{
            _cols.push({field:"grid_check_col",title:"",checkbox:true});
        }
        _cols.push({field:"grid_value_col",title:""});
        _conf.key_fld="grid_value_col";
        _conf.value_fld="grid_value_col";
        var _new_data=[];
        for(var _x in _data){
            _new_data.push({grid_value_col:_data[_x]});
        }
        _data=_new_data;
    }else{
        _cols=_conf.columns;
        var _col={};
        if(_conf.singleSelect==true){
            _col={field:"grid_check_col",title:"",formatter:function(value, rowData, rowIndex){
                return '<input type="radio" name="grid_selector_rbn" id="grid_selector_rbn' + rowIndex + '"  index="'+rowIndex+'"    value="' + rowData[_conf.key_fld] + '" />';
            }}
            _conf.onClickRow=function(rowIndex, rowData){
                $("#grid_selector_rbn"+rowIndex).attr("checked","checked");
            }
        }else{
            _col={field:"grid_check_col",title:"",checkbox:true};
        }
        _cols.unshift(_col);
    }
    _conf.height=_conf.height?_conf.height:400;
    var _new_conf= $.extend({singleSelect:false},_conf,{columns:_cols,data:_data});
    var _gd=yo.grid.create(_new_conf);
    if(_conf.selected){
        yo.grid.selector.setSelected(_conf.id,_conf.selected);
    }
};
yo.grid.selector.setSelected=function(id,items){
    var _conf=yo.grid.getConfig(id);
    var _gd=yo.grid.getGrid(id);
    var _data=_conf.data;
    if(_conf.singleSelect==true){
        var _sel_item=items;
        for(var _i in _data){
            if(_data[_i][_conf.key_fld]==_sel_item){
                _gd.datagrid("selectRow",_i);
                $("#grid_selector_rbn"+_i).attr("checked","checked");
            }
        }
    }else{
        for(var _i in _data){
            for(_j in items){
                if(_data[_i][_conf.key_fld]==items[_j]){
                    _gd.datagrid("selectRow",_i);
                }
            }
        }
    }

};
yo.grid.selector.getSelected=function(id){
    var _gd=yo.grid.getGrid(id);
    var _conf=yo.grid.getConfig(id);
    var _rows=_gd.datagrid("getSelections");
    var _rt=[];
    if(_conf.singleSelect==true){
        var _rbn=$('input[type="radio"][name="grid_selector_rbn"]:checked');
        if(!_rbn || _rbn.length==0) return null;

        if(_conf.return_row==true){
            var _sel_idx=_rbn.attr("index");
            var _rows=_gd.datagrid("getRows");
            return _rows[_sel_idx];
        }else{
            return _rbn.attr("value");
        }
    }else{
        for(var _x in _rows){
            _rt.push(_rows[_x][_conf.key_fld]);
        }
    }
    return _rt;
};
yo.grid.selector.dialog=function(_conf){
    var _div=$('<div id="div_selector_dialog_grid" style="height: 400px"></div>');
    var _diaConf= $.extend({title:"Choose"},_conf,{
        content:_div,
        buttons:[{text:"Ok",width:100,iconCls:"glyphicon glyphicon-ok",handler:function(){
            var _items=yo.grid.selector.getSelected(_conf.id);
            if(_conf.callback){
                _conf.callback(_items);
            }
            yo.dialog.close();
        }},{text:"cancel",iconCls:"glyphicon glyphicon-remove",handler:function(){
            yo.dialog.close();
        }}]
    });
    yo.dialog.show(_diaConf);
    yo.dialog.waiting();
    _conf.id="div_selector_dialog_grid";
    yo.grid.selector.create(_conf);
    yo.dialog.unmask();
};
/*******************************Pager*************************************/
yo.pager=function(_config){
    this.id=_config.id;
    this.config=_config;

    var _parts=["<nav>"];
    _parts.push('<ul class="pager">');

    _parts.push('<li><a href="#" class="pager-pre" ">Previous</a></li>');
    _parts.push('PageSize:<select class="pager-size"><option value="10">10</option><option selected value="20">20</option><option value="50">50</option><option value="100">100</option></select>');
    _parts.push('<li>Total:<label class="label-total">0</label>,Current:<label class="label-current">0</label></li>');
    _parts.push('<li><a href="#" class="pager-next">Next</a></li>');
    _parts.push('</ul>');
    _parts.push('</nav>');
    _html=_parts.join("\n");
    $("#"+this.id).html(_html);
    this.content=$("#"+this.id);
    var me=this;
    me.total=0;
    me.current=0;
    me.pageSize=20;
    this.setPagerTotal=function(_total){
        me.content.find(".label-total").first().text(_total);
        me.total=_total;
    };
    this.setPagerCurrent=function(_current){
        me.content.find(".label-current").first().text(_current);
        me.current=_current;
        if(me.current==me.total){
            me.content.find(".pager-next").first().closest("li").addClass("disabled");
        }else{
            me.content.find(".pager-next").first().closest("li").removeClass("disabled");
        }
        if(me.current==1){
            me.content.find(".pager-pre").first().closest("li").addClass("disabled");
        }else{
            me.content.find(".pager-pre").first().closest("li").removeClass("disabled");
        }
    };
    this.setPageSize=function(_size){
        this.content.find(".pager-size").first().val(_size);
    };
    if(_config.total) this.setPagerTotal(_config.total);
    if(_config.current) this.setPagerCurrent(_config.current);
    if(_config.size) this.setPageSize(_config.size);

    this.content.find(".pager-pre").first().on("click",function(){
        if(me.current==1) return;
        me.current-=1;
        me.setPagerCurrent(me.current);
        if(me.config && me.config.changePage){
            me.config.changePage(me.current,me.pageSize,me);
        }
    });
    this.content.find(".pager-next").first().on("click",function(){
        if(me.current==me.total) return;
        me.current+=1;
        me.setPagerCurrent(me.current);
        if(me.config && me.config.changePage){
            me.config.changePage(me.current,me.pageSize,me);
        }
    });
    this.content.find(".pager-size").first().on("change",function(e){
        var _size=$(this).val();
        if(_size==me.pageSize) return;
        me.pageSize=_size;
        me.total=0;
        me.current=1;
        if(me.config && me.config.changePage){
            me.config.changePage(me.current,me.pageSize,me);
        }
    });
    this.setPager=function(_number,_size,_total){
        me.setPagerCurrent(_number);
        me.setPagerTotal(_total);
        me.setPageSize(_size);

    };
    return this;
};
/**************以后有时间了再设计Store的模式来处理数据集*******************/
/**************还要处理图片剪辑、文件上传的功能*******************/
/*************************日期处理函数***********************/
yo.dateAdd=function(_dateString,_days){
    var _d=new Date(Date.parse(_dateString));
    _d=_d.DateAdd("d",_days);
    _d=_d.DateFormat("yyyy-MM-dd");
    var _rt_str= _d.toString();
    return _rt_str;

};
function formatAmount(num){
    return (num.toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
}
