@extends('layouts.master')
@section('content')
    <div class="panel" >
        <div class="panel-body" >

    <div id="page-content" class="panel">
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li class="active" role="presentation"><a href="/stationery/home" class="application_num">用品管理</a></li>
            <li class="item" role="presentation"><a href="/stationery/apply" class="application_num">物品审批</a></li>
        </ul>
    </div>
        <div class="row">
            <div class="col-lg-3 col-sm-3 col-md-3">
                <button class="btn btn-primary btn-addSta" type-id="" type-name="">添加办公用品</button>
            </div>
            <div class="col-lg-5 col-sm-5 col-md-5">
                <div class="input-group">
                    <input type="text" class="form-control col-xs-1" id="key_word" value="{{$supply_name or ' '}}" />
                    <span class="input-group-btn">
                        <select class="form-control" name="config_key" id="config_key">
                            <?php
                            if(empty($father_type)|| !is_array($father_type)){
                                echo <<<OPT
<option value=""></option>
OPT;
                            }else{
                                foreach($father_type as $k=>$v){
                                    if($k==$config_key){

                                        echo "<option value=".$k." selected>$v</option>";
                                        continue;
                                    }
                                    echo <<<OPT
<option value="{$k}">{$v}</option>
OPT;
                                }
                            }

                            ?>
                        </select>
                        <button class="btn btn-default btn-search" type="button">
                            <span class="glyphicon glyphicon-search"></span>搜索名称
                        </button>
                    </span>
                </div>
            </div>
        </div>
    <div class="table-container">
        <table class="table table-striped table-hover"  style="font-size:14px;">
            <thead>
            <tr>
                <th class="col-xs-3">品类名称</th>
                <th class="col-xs-3">用品名称</th>
                <th class="col-xs-2">用品详情</th>
                <th class="col-xs-2">备注</th>
                <th colspan="2">操作</th>
            </tr>
            </thead>
            <tbody id="show">
            @if (empty($data)|| !is_array($data))
            @else
            @foreach ($data as $t)
            <tr>
                @if (!isset($t['data'])|| empty($t['data']))
                @else
                @foreach ($t['data'] as $s)
                <td>{{ $t['s_v'] or '' }} </td>
                <td>{{ $s['supply_name'] or '' }}  </td>
                <td>{{ $s['detail'] or '' }}  </td>
                <td>{{ $s['memo'] or '' }}  </td>
                <td>
                    <button class="btn btn-sm btn-warning btn-staMod" sta-id="{{$s['id'] or '' }}">
                        <span class="glyphicon glyphicon-log-in rm-disable"></span> 修改
                    </button>
               @if (isset($s['status'])&& $s['status']==1)
                    <button class="btn btn-sm btn-danger btn-del" sta-id="{{$s['id'] or ''}}">
                        <span class="glyphicon glyphicon-remove rm-disable"></span> 停用
                    </button>
               @else
                    <button class="btn btn-sm btn-waring btn-revert" sta-id="{{$s['id'] or ''}}">
                        <span class="glyphicon glyphicon-trash"></span>恢复
                    </button>
               @endif
                </td>
            </tr>
            @endforeach
            @endif
            @endforeach

            @endif
        </table>
    </div>
</div>

</div>

<div class="modal fade" id="staModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 id="staModal-head">办公用品</h4>
            </div>
            <div class="modal-body">
                <div class="container">
                    <h3 id="form-name"></h3>
                    <p id="form-detail"></p>
                </div>
                <form class="form-horizontal" role="form" id="requestForm" onsubmit="return false">
                    <div class="form-group">
                        <label class="col-sm-3" for="id">办公用品ID</label>
                        <div class="col-sm-9">
                            <input  value="" readonly  class="form-control" name="id" id="office_id" placeholder="办公用品ID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3" for="supply_name">办公用品名称</label>
                        <div class="col-sm-9">
                            <input  value="" class="form-control" name="supply_name"  id="supply_name" placeholder="办公用品名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3" for="memo">备注</label>
                        <div class="col-sm-9">
                            <input class="form-control" name="memo" id="memo" value="" placeholder="备注">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3" for="order_type">类型</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="order_type">
                                <?php
                                if(empty($father_type) || !is_array($father_type)){
                                    echo <<<OPT
<option value=""></option>
OPT;
                                }else{
                                    foreach($father_type as $k=>$v){
                                        echo <<<OPT
<option value="{$k}">{$v}</option>
OPT;
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3" for="">种类</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="supply_type">
                                <option value="">请选择分类</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3" for="detail">办公用品详情</label>
                        <div class="col-sm-9">
                            <textarea value="detail" name="detail" class="form-control" id="detail" ></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭 </button>
                <button type="button" class="btn btn-primary rm-disable" id="submitSta"> 提 交 </button>
            </div>
        </div>
    </div>
</div>

<script src="/static/js/bootbox.min.js"></script>
<script type="text/javascript">
    $(function() {
        var subType = <?php echo json_encode($son_type) ?>;
        var orderType = $("#order_type").val();

        if (subType[orderType]) {
            var sub_type = subType[orderType];
            $.each(sub_type, function(i,val) {
                var option = $("<option>").val(i).text(val);
                $("#supply_type").append(option);
            });
        }
        $("#order_type").change(function(){

            $("#supply_type").empty();
            var parent_id = $("#order_type").val();
            var option = $("<option>").val("0").text("请选择");
            $("#supply_type").append(option);

            if (subType[parent_id]) {
                var sub_type = subType[parent_id];
                $.each(sub_type, function(i,val) {
                    var option = $("<option>").val(i).text(val);
                    $("#supply_type").append(option);
                });
            }

        });
    });
    $('.btn-search').click(function(){
        var keyword = $.trim($('#key_word').val());
        var type = $.trim($('#config_key').val());
        window.location = '/stationery/home?config_key='+type+'&supply_name='+encodeURIComponent(keyword);
    });
    $('.btn-del').click(function () {
        var _this = $(this)
        bootbox.confirm('确认删除？', function (result) {
            if (result) {
                _this.attr('disabled',true);
                var id = _this.attr('sta-id');

                $.ajax({
                    url: "/stationery/ajax_update_delete",
                    method: "POST",
                    data: {id:id,status:0},
                    dataType: "json",
                    success: function (msg) {
                        _this.removeAttr('disabled');
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            window.location.reload();
                        } else {
                            show_message(msg.code, msg);
                        }
                    }
                });
            } else {
            }
        });
    });
    $('.btn-revert').click(function () {
        var _this = $(this)
        bootbox.confirm('确认恢复？', function (result) {
            if (result) {
                _this.attr('disabled',true);
                var id = _this.attr('sta-id');

                $.ajax({
                    url: "/stationery/ajax_update_delete",
                    method: "POST",
                    data: {id:id,status:1},
                    dataType: "json",
                    success: function (msg) {
                        _this.removeAttr('disabled');
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            window.location.reload();
                        } else {
                            show_message(msg.code, msg);
                        }
                    }
                });
            } else {
            }
        });
    });
    $('#submitSta').click(function(){

        var myForm = $('#requestForm').serializeArray();
        myForm.push({name:'supply_type',value: $("#supply_type").val()});
        myForm.push({name:'order_type',value: $("#order_type").val()});
        if( !$('#id').val()){
            $.post('/stationery/ajax_add',myForm,function(ret){
                show_message(ret.code,ret);
            },'json');
        }else{
            $.post('/stationery/ajax_update_delete',myForm,function(ret){
                show_message(ret.code,ret);
            },'json');
        }

    });


    $('.btn-staMod').click(function(){
        var _this = $(this).attr('sta-id');
        $.get('/stationery/ajax_get',{id:_this},function(ret){

            if(ret.code == 200) {
                $.each(ret.data, function(key, val) {
                if( !val.memo )val.memo='';
                if( !val.id )val.id='';
                if( !val.detail )val.detail='';
                if( !val.supply_name )val.supply_name='';
                if( !val.order_type )val.order_type='';
                if( !val.supply_type )val.supply_type='';
                $('#memo').val(val.memo);
                $('#office_id').val(val.id);
                $('#detail').val(val.detail);
                $('#supply_name').val(val.supply_name);
                var oT =val.order_type,
                    sT=val.supply_type;

                if(oT){
                     $('#order_type option[value=' +oT+ ']').attr("selected",true);
                    $(function() {
                        var subType = <?php echo json_encode($son_type) ?>;
                        var orderType = $("#order_type").val();

                        if (subType[orderType]) {
                            var sub_type = subType[orderType];
                            $.each(sub_type, function(i,val) {
                                var option = $("<option>").val(i).text(val);
                                $("#supply_type").append(option);
                            });
                        }
                        $("#order_type").change(function(){

                            $("#supply_type").empty();
                            var parent_id = $("#order_type").val();
                            var option = $("<option>").val("0").text("请选择");
                            $("#supply_type").append(option);

                            if (subType[parent_id]) {
                                var sub_type = subType[parent_id];
                                $.each(sub_type, function(i,val) {
                                    var option = $("<option>").val(i).text(val);
                                    $("#supply_type").append(option);
                                });
                            }

                        });
                    });
                    if(sT){
                        $('#supply_type option[value='+sT+']').attr("selected",true);
                    }
                }

                });
                $('#staModal').modal('show');
            }else {
                console.log('接口读取错误');
            }

        },'json');

    });
    $('.btn-addSta').click(function(){
        var empty;
        $('#memo').val(empty);
        $('#office_id').val(empty);
        $('#detail').val(empty);
        $('#supply_name').val(empty);
        $('#staModal').modal('show');
    });

</script>
@endsection