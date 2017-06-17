
@extends('layouts.master')
@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">
    .search_btn, .export_btn, .add_btn, .delete_btn, .edit_btn {
        margin-left: 5px;
        float: right;
    }
</style>
<div class="panel" >
    <div class="panel-body" >
    <div  class="form-container">
    <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/structure/user/ajax_search_user"
          method="get">
        <div class="row">
            <div class="col-lg-2">
                <select class="form-control" name="status" id="status" >
                    <option value="1,3">在职</option>
                    <option value="2">离职</option>
                </select>
            </div>
            <div class="col-lg-2">
                <input type="search" class="form-control search" placeholder="入职开始日期（含）" id="hire_start_time" name="hire_start_time">
            </div>

            <div class="col-lg-2">
                <input type="search" class="form-control search" placeholder="入职结束日期（含）" id="hire_end_time"  name="hire_end_time">
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" placeholder="姓名、邮箱、手机、QQ" id="search" name="search"/>
            </div>
            <div class="col-lg-1">
                <a  href="javascript:void(0)" formId="searchForm" class="btn btn-default search_btn" type="button">
                    <span class="glyphicon glyphicon-search"></span>搜索
                </a>
            </div>
            <div class="col-lg-1">
                <button class="btn btn-primary add_new export_btn" type="button" data-toggle="modal"
                        data-target="#cateModal">导出
                </button>
            </div>
            <div class="col-lg-1">
                <button class="btn btn-primary add_new add_btn" type="button" data-toggle="modal"
                        data-target="#cateModal">添加
                </button>
            </div>
        </div>
        </form>
    </div>
        <p></p>
        <!-- Table -->
        <div class="table-container">
            <table class="table table-striped table-hover"  style="font-size:14px;">
                <thead>
                <tr>
                    <th class="col-xs-1">ID</th>
                    <th class="col-xs-1">名字</th>
                    <th class="col-xs-1">工号</th>
                    <th class="col-xs-1">邮箱</th>
                    <th  class="col-xs-2">部门</th>
                    <th class="col-xs-2">手机</th>
                    <th class="col-xs-1">QQ</th>
                    <th class="col-xs-2">MLS昵称</th>
                    <th ></th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="show">
                @if (isset($data))

                @foreach ($data as $t)
                <tr>
                    <td >{{ $t['user_id'] }}</td>
                    <td >{{ $t['user_name'] }}</td>
                    <td >{{ $t['staff_id'] }}</td>
                    <td >{{ $t['mail'] }}</td>
                    <td >{{ $t['depart_name'] }}</td>
                    <td>{{ $t['mobile'] }}</td>
                    <td >{{ $t['qq'] }}</td>
                    <td>{{ $t['nickname'] }}</td>
                    <td >
                        <a class="btn btn-sm btn-warning add_new edit_btn" type="button" data-toggle="modal"
                                data-user_id="{{$t['user_id']}}"><span class="glyphicon glyphicon-log-in rm-disable"></span>修改
                        </a>
                    </td>
                    <td>
                        <a  class="btn btn-sm  btn-danger add_new btn-del" data-user_id="{{$t['user_id']}}"
                           title="删除用户信息"><span class="glyphicon glyphicon-remove rm-disable"></span>删除
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            <div class="pagination-left">
            </div>


</div>
        </div>
<link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script src="/static/js/bootbox.min.js"></script>
<script>
    //搜索
    $('.search').datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true});
    function init() {
        $("input[name='search']").val("");
        $("input[name='hire_end_time']").val("");
        $("input[name='hire_start_time']").val("");
    }

    $('.search_btn').click(function() {
        $(".pagination-left").hide();
        $('.page_btn').parent().hide();//父亲隐藏toggle()
        var myForm = $('#searchForm').serializeArray();
        $.get('/structure/user/ajax_search_user', myForm, function (ret) {
            init();
            if (ret.code == 200) {
                $("#show").empty();
                var str;
                $.each(ret.data, function (i, val) {
                    str += '<tr>' +
                        '<td>' + val.user_id + '</td>' +
                        '<td>' + val.user_name + '</td>' +
                        '<td>' + val.staff_id + '</td>' +
                        '<td>' + val.mail + '</td>' +
                        '<td>' + val.depart_name + '</td>' +
                        '<td>' + val.mobile + '</td>' +
                        '<td>' + val.qq + '</td>' +
                        '<td>' + val.nickname + '</td>' +
                        '<td>' +
                        '<a class="btn btn-primary btn-sm btn-warning add_new edit_btn" type="button" data-toggle="modal"  data-user_id="' + val.user_id + '"><span class="glyphicon glyphicon-log-in rm-disable"></span>修改</a>' +
                        '</td>' +
                         '<td>' +
                        '<a class="btn  btn-sm  btn-danger add_new btn-del"  title="删除用户信息" data-user_id="' + val.user_id + '"><span class="glyphicon glyphicon-remove rm-disable"></span>删除</a>'+
                        '</td>' +
                        '</tr>';
                });
                $('#show').html(str);
            } else if (ret.code == 400 || ret.code == 500) {

            }
        }, 'json');
    });

    //添加
    $('.add_btn').click(function () {
        window.location.href = '/structure/user/add_user';
    });
    //编辑
    $('#show').delegate('.edit_btn', 'click', function () {
        var data = $(this).data();
        var user_id = data.user_id;
        if(user_id){
            window.location.href = '/structure/user/update_user?user_id=' + user_id;
        }

    });
    //导出
    $('.export_btn').click(function () {

        show_message(200,'');
        window.location.href='/structure/user/export_info?status='+$('#status').val()+'&search='+$("#search").val()+
           '&hire_end_time=' +$("#hire_end_time").val()+ '&hire_start_time='+$("#hire_start_time").val();
    });

    //分页
    var count = '{!! @json_encode($count) !!}';
    try{
        count = $.parseJSON(count);
    }catch(e){
        count = [];
    }
    var page = '{!! @json_encode($page) !!}';
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
        go: 'Go' //取消页面跳转
    }).on("switch",function(e,page){

        location.href="/structure/user/user_home?page="+page;

    });
    $('.btn-del').click(function () {
        var _this = $(this)
        bootbox.confirm('确认删除用户吗？', function (result) {
            if (result) {
                _this.attr('disabled',true);
                var id = _this.attr('data-user_id');

                $.ajax({
                    url: "/structure/user/ajax_delete_user",
                    method: "POST",
                    data: {user_id:id},
                    dataType: "json",
                    success: function (msg) {
                        _this.removeAttr('disabled');
                        if (msg.code == 200) {
                            $(_this).parent().parent().remove();
                            show_message(msg.code, msg);
                        } else {
                            show_message(msg.code, msg);
                        }
                    }
                });
            } else {

            }
        });
    })
</script>
@endsection