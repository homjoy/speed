@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">
    .token-input-token,.token-input-input-token{line-height: 23px !important;}
    .query_btn, .export_btn {
        margin-left: 5px;
        float: left;
    }
</style>
<div class="panel" >
    <div class="panel-body" >
    <div id="page-content" class="panel">
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li   class="item" role="presentation"><a href="/stationery/home" class="application_num">用品管理</a></li>
            <li  class="active" role="presentation"><a href="/stationery/apply" class="application_num">物品审批</a></li>
        </ul>
    </div>
    <div class="form-container">
        <form id="searchForm" class="form-horizontal" role="">
            <div class="row">
                <div class=" col-lg-3 col-xs-3 col-sm-3">
                    <input type="text" class="search form-control  form_datetime"  value="{{$search_params['create_time'] or '' }}" placeholder="开始日期（含）" id="createTime" name="create_time">
                </div>
                <div class="  col-lg-3 col-xs-3 col-sm-3">
                    <input type="text" class="search form-control form_datetime" value="{{$search_params['end_time'] or '' }}"  placeholder="结束日期（含）" id="endTime"  name="end_time">
                </div>
<!--                <div class="col-lg-4 col-md-4 col-sm-4">-->
<!--                    <input type="text" class="form-control user_id" id="user_id"  value="{{$search_params['user_id'] or '' }}"  name="user_id"/>-->
<!--                </div>-->
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <a   class="btn btn-default query_btn"  type="button">搜索</a>
                </div>
<!--                <div class="col-lg-1 col-md-1 col-sm-1">-->
<!--                    <a  class="btn btn-primary  export_btn" type="button">导出</a>-->
<!--                </div>-->
            </div>
        </form>
    </div>

<!--        <div class="table-container">-->
            <div class="text-center">
                <h4>审批查询</h4>
            </div>
            <div class="table-container">

                <table class="table table-striped table-hover"  style="font-size:14px;">

                    <thead>
                    <tr>
                        <th class="col-xs-2">流程id</th>
                        <th class="col-xs-2">申请人</th>
                        <th class="col-xs-1">物品名称</th>
                        <th class="col-xs-1">物品数量</th>
                        <th class="col-xs-2">发放状态</th>
                        <th class="col-xs-2">审批状态</th>
                        <th class="col-xs-2">创建时间</th>

                    </tr>
                    </thead>
                    <tbody id="show">
                    @if (!empty($data))

                    @foreach ($data as $t)
                    <tr>
                        <td>{{ $t['order_id'] }}</td>
                        <td>{{ $t['name_cn'] }}</td>
                        <td>{{ $t['supply_name'] }}</td>
                        <td>{{ $t['apply_number'] }}</td>
                        <td> {{ $t['output'] }} </td>
                        <td> {{ $t['status'] }} </td>
                        <td> {{ $t['create_time'] }} </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="pagination-left">
            </div>

        </div>
<!--</div>-->
    </div>
<link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
//    function init() {
//        $('#user_id').tokenInput("add", {user_id:$('#user_id').val() , name: ''});
//    }
    //搜索
    $('.search').datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true});
    //分页
    var count = '{!! @json_encode($count) !!}';
    try {
        count = $.parseJSON(count);
    } catch (e) {
        count = [];
    }
    var page = '{!! @json_encode($page) !!}';
    try {
        page = $.parseJSON(page);
    } catch (e) {
        page = [];
    }
    $(".pagination-left").pagination({
        //总页数
        totalPage: count,
        //初始选中页
        currentPage: page,
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
    }).on("switch", function (e, page) {
        location.href = "/stationery/apply?page=" + page+'&create_time='
            +$('#createTime').val()+'&end_time='+$('#endTime').val();
    });
    //tokeninput
//    $('#user_id').tokenInput("/structure/depart/ajax_search_name",{
//        queryParam:'search',tokenValue:'user_id',tokenLimit: 1,onAdd:function(ret) {
//
//            if(!!ret['user_id']){
//                $('#user_id').val(ret['user_id']);
//            }
//        }
//    });

    $('.query_btn').click(function() {
        location.href = "/stationery/apply?"+'create_time='
            +$('#createTime').val()+'&end_time='+$('#endTime').val();
    });

//    $('.export_btn').click(function() {
//        show_message(200,'');
//        window.location.href="/stationery/export_apply?"+'create_time='
//            +$('#createTime').val()+'&end_time='+$('#endTime').val();
//    });
</script>
@endsection