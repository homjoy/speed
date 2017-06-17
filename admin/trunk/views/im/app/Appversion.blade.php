@extends('layouts.master')
@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">
    .search_btn, .export_btn, .add_btn, .delete_btn, .edit_btn {
        margin-left: 5px;
        float: right;
    }
    .ul_input{
        overflow: auto;
        height: 200px;
        border:1px solid #ddd;
    }
    .ul_input li{
        padding: 3px 20px;
    }
</style>
<div class="panel-heading" >
    <ol class="breadcrumb">
        <li class="active">版本列表管理</li>
    </ol>
    <form id="searchForm" class="form-inline  form-horizontal" method="POST" role="form" action="/mail/mail_home">
        <div class="row">
            <div class="col-lg-4">
                <input type="text" class="form-control" placeholder="按照邮箱进行搜索，可用邮箱前缀进行搜搜" value="@if(!empty($search_params['email'])){{$search_params['email']}} @endif" id="email" name="email"/>
            </div>
            <div class="col-lg-1">
                <input type="submit" value="搜索" class="btn btn-default">
            </div>
            <div class="col-lg-1">
                <button class="btn btn-primary add_new add_btn" type="button" id="mail_group_add">添加
                </button>
            </div>
        </div>
        <p></p>
        <!-- Table -->
        <div class="table-container">
            <table class="table table-striped table-hover"  style="font-size:14px;">
                <thead>
                <tr>
                    <th class="col-xs-1">ID</th>
                    <th class="col-xs-1">类型</th>
                    <th class="col-xs-1">版本号</th>
                    <th class="col-xs-1">版本名称</th>
                    <th class="col-xs-2">md5</th>
                    <th class="col-xs-3">下载地址</th>
                    <th class="col-xs-3">强制更新</th>
                    <th class="col-xs-3">备注</th>
                    <th class="col-xs-3">操作</th>
                </tr>
                </thead>
                <tbody id="show">
                @if (!empty($list))

                @foreach ($list as $t)
                <tr>
                    <td >{{ $t['id'] }}</td>
                    <td >{{ $t['v_type'] }}</td>
                    <td >{{ $t['v_code'] }}</td>
                    <td >{{ $t['v_name'] }}</td>
                    <td >{{ $t['v_md5'] }}</td>
                    <td >{{ $t['v_url'] }}</td>
                    <td >{{ $t['force_update'] }}</td>
                    <td >{{ $t['notes'] }}</td>
                    <td >
                        <!--<a class="btn btn-warning  delete_btn" data-group-id="{{$t['id']}}"
                           title="删除邮件组">删除 -->
                        </a>
                        <a href="javascript:void(0);" class="btn btn-primary add_new edit_btn" type="button">修改
                        </a>

                    </td>
                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
         <div class="pagination-left">
         </div>
    </form>
</div>
<!-- 添加模版 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">版本列表</h4>
            </div>
            <div class="modal-body">
                <form  id="addForm" class="form-horizontal">
                    <input type="hidden"  class="form-control" name ='v_id' id="v_id" placeholder="">
                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">版本类型</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='v_type' id="v_type" placeholder="版本类型">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">版本号</label>
                        <div class="col-sm-10">
                        <input type="text" name ='v_code'  id="v_code" class="form-control" placeholder="版本号" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">版本名称</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='v_name' id="v_name" placeholder="版本名称">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">MD5</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='v_md5' id="v_md5" placeholder="md5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">下载url</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='v_url' id="v_url" placeholder="下载地址">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">强制更新</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='force_update' id="force_update" placeholder="强制更新:0不强制更新1强制更新">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='notes' id="notes" placeholder="备注">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="add_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 添加模版 -->
<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>

<script>
    //分页
    var count = '{!! json_encode($count) !!}';
    try{
        count = $.parseJSON(count);
    }catch(e){
        count = [];
    }
    var page = '{!! json_encode($page) !!}';
    try{
        page = $.parseJSON(page);
    }catch(e){
        page = [];
    }
    $(".pagination-left").pagination({
        //总页数
        totalPage:count,
        //初始选中页
        currentPage:page,
        //最前面的展现页数
        firstPagesCount: 0, //最前面的展现页数，默认值为2
        preposePagesCount: 2,  //当前页的紧邻前置页数，默认值为2
        postposePagesCount: 0, //当前页的紧邻后置页数，默认值为1
        lastPagesCount: 2,//最后面的展现页数，默认值为0
        href: false,    //不生成链接
        first: '', //取消首页
        prev: '<',
        next: '>',
        last: '', //取消尾页
        go: false //取消页面跳转
    }).on("switch",function(e,page){
        var email = $('#email').val()
        location.href="/im/app/appVersion?page="+page;

    });

    //添加邮件组操作
    $('#mail_group_add').click(function(e) {
        e.stopPropagation();
        $('#myModalLabel').html('邮件列表');
        $('#myModal').modal('show');
    });
    
    //移除添加的数据
    $(document).delegate('.del_email', "click", function () {
        $(this).parent('li').remove();
    });
    //保存数据
    $("#add_btn").click(function(){
        var v_id = $("#v_id").val();
        var v_url = $("#v_url").val();
        var v_code = $("#v_code").val();
        var v_name = $("#v_name").val();
        var v_md5 = $("#v_md5").val();
        var v_type = $("#v_type").val();
        var force_update = $("#force_update").val();
        var notes = encodeURIComponent($("#notes").val());

        if(!v_type){
            var ret = [];
            ret['error_msg'] = '类型不能为空';
            show_message(0,ret)
            return;
        }
       
        if(v_id>0){
            var url = "/im/app/ajax_app_add";
            var data = { v_id:v_id,  v_url : v_url, v_code:v_code, v_name:v_name, v_md5:v_md5, v_type:v_type, force_update:force_update, notes:notes};
        }else{
            var url = "/im/app/ajax_app_add";
            var data = { v_url : v_url, v_code:v_code, v_name:v_name, v_md5:v_md5, v_type:v_type, force_update:force_update, notes:notes};
        }

        $('#add_btn').attr('disabled',true);
        $.ajax({
            url: url,
            method: "POST",
            data: data,
            dataType: "json",
            success:function(msg){
                $('#add_btn').removeAttr('disabled');
                if(msg.code==200){
                    show_message(msg.code,msg);
                    $('#v_url').val('');
                    $('#v_code').val('');
                    $('#v_name').val('');
                    $('#v_md5').val('');
                    $('#v_type').val('');
                    setTimeout("location.reload()",1500);
                }else{
                    var ret =[];
                    ret['error_msg'] = msg['error_detail'];
                    show_message(msg.code,ret);
                }
            }
        });

    })

    //删除邮件组
    $('.delete_btn').click(function(){
        if(!confirm('确认删除？')){
            return false;
        }
        var mail_group_id = $(this).attr('data-group-id');
        if(!mail_group_id){
            show_message(0,'邮件列表不能为空')
            return;
        }
        var _this = $(this)
        $(this).attr('disabled',true);
        $.ajax({
            url: "/mail/ajax_mail_group_del",
            method: "POST",
            data: {  group_id:mail_group_id},
            dataType: "json",
            success:function(data){
                _this.removeAttr('disabled');
                if(data.code==200){
                    show_message(data.code,data);
                    setTimeout("location.reload()",1500);
                }else{
                    var ret =[];
                    ret['error_msg'] = data['error_detail'];
                    show_message(data.code,ret);
                }
            }
        });

    })

    //修改
    $('.edit_btn').click(function(e){

        e.stopPropagation();
        $('#myModalLabel').html('邮件列表');
        $('#myModal').modal('show');
        
        var tds = $(this).parents('tr').children('td');
        var v_id = parseInt(tds.eq(0).text());
        var v_type = $.trim(tds.eq(1).text());
        var v_code = $.trim(tds.eq(2).text());
        var v_name = $.trim(tds.eq(3).text());
        var v_md5 = $.trim(tds.eq(4).text());
        var v_url = $.trim(tds.eq(5).text());
        var force_update = $.trim(tds.eq(6).text());
        var notes = $.trim(tds.eq(7).text());

        // var status = $.trim(tds.eq(2).text());
        // status = status == '有效' ? 1 : 0;
        $('input[name=v_id]').val(v_id);
        $('input[name=v_type]').val(v_type);
        $('input[name=v_code]').val(v_code);
        $('input[name=v_name]').val(v_name);
        $('input[name=v_md5]').val(v_md5);
        $('input[name=v_url]').val(v_url);
        $('input[name=force_update]').val(force_update);
        $('input[name=notes]').val(notes);

    });
</script>
@endsection
