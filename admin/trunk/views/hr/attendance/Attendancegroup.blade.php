@extends('layouts.master')

@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
</style>
<!--<div class="col-xs-6 col-sm-2">-->
<!--    @include('hr/attendance/Navbar')-->
<!--</div>-->
<!--<div class="col-xs-12 col-sm-10">-->
<div class="panel">
<div class="panel-body" >

    <div id="page-content" class="panel">
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li role="presentation"><a href="/hr/attendance/attendance_home" class="application_num">考勤数据统计</a></li>
            <li  class="active" role="presentation" ><a href="/hr/attendance/attendance_group">考勤组管理</a></li>
            <li role="presentation" ><a href="/hr/attendance/attendance_user">考勤人员管理</a></li>
        </ul>
    </div>
<!--    <div class="col-xs-12 col-sm-10">-->
<!--    <div class="panel panel-default">-->
<!--        <div class="panel-heading" style="overflow: hidden; line-height: 32px;">-->
<!--        这个地方放查询条件 -->
       <div class="from-container">
        <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/hr/attendance/attendance_group" method="get">
            <div class="row ">

                <div class="col-lg-2 col-md-2 col-sm-2">
                    <input type="text" class="form-control" placeholder="名字搜索" id="search" name="search" value="{{ $search_params['search'] or '' }}"/>
                </div>

                <div class="col-lg-1 col-md-1 col-sm-1">
                    <button  type="submit" class="btn btn-default search_btn"><span class="glyphicon glyphicon-search"></span>搜索</button>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <a class="btn btn-primary add_new add_btn" href="/hr/attendance/update_add_group" target="_self" >添加
                    </a>
                </div>

            </div>
        </form>
       </div>
<!--       </div>-->
<!--    </div>-->

<!--</div>-->
<!--<div class="table-container col-xs-12  col-sm-10">-->
       <div class="table-container">
        <table class="table table-striped table-hover"  style="font-size:14px;">
            <thead>
            <tr>
                <th class="col-xs-1">ID</th>
                <th class="col-xs-1">名称</th>
                <th class="col-xs-1">早上上班时间</th>
                <th class="col-xs-1">早上下班时间</th>
                <th class="col-xs-1">下午上班时间</th>
                <th class="col-xs-1">下午下班时间</th>
                <th class="col-xs-1">加班开始时间</th>
                <th class="col-xs-1">状态</th>
                <th class="col-xs-1">操作</th>

            </tr>
            </thead>
            <tbody id="show">
            @if (isset($data)||!empty($data))
                @foreach ($data as $t)
                    <tr>
                        <td >{{ $t['id'] }}</td>
                        <td >{{ $t['name'] }}</td>
                        <td >{{ $t['morning_start'] }}</td>
                        <td >{{ $t['morning_end'] }}</td>
                        <td >{{ $t['afternoon_start'] }}</td>
                        <td >{{ $t['afternoon_end'] }}</td>
                        <td >{{ $t['overtime_start'] }}</td>
                        <td >{{ $t['status']  }}</td>
                         <th class="col-xs-1"><a href="/hr/attendance/update_add_group?id={{ $t['id'] }}" class="btn btn-sm btn-primary">修改</a></th>
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
<script>
//    var str = window.location.pathname;
//    $.each($('.list-group-item'),function(){
//        if($(this).attr('href')==str){
//            $(this).addClass('active');
//        }
//    });
    //分页
    $('.search').datepicker({'pickerPosition': 'bottom-left',dateFormat: 'yy-mm-dd'});
    
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
        var search = $('#search').val()
        location.href="/hr/attendance/attendance_group?page="+page+'&search='+search;

    });



</script>
@endsection
