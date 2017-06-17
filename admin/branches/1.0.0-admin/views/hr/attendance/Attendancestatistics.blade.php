@extends('layouts.master')

@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
    .date_time{
        cursor:pointer !important;
    }
</style>
<div class="col-xs-6 col-sm-2">
    @include('hr/attendance/Navbar')
</div>
<div class="col-xs-12 col-sm-10">
    <div class="panel panel-default">
        <div class="panel-heading" style="overflow: hidden; line-height: 32px;">
<!--        这个地方放查询条件 -->
            <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/hr/attendance/attendance_home" method="get">
                <div class="row">
                    <div class="col-lg-2">
                        <input type="search" readonly="" class="form-control date_time" placeholder="考勤开始日期（含）" id="attendance_start_date" name="attendance_start_date">
                    </div>

                    <div class="col-lg-2">
                        <input type="search"  readonly="" class="form-control date_time" placeholder="考勤结束日期（含）" id="attendance_end_date"  name="attendance_end_date">
                    </div>
                    <div class="col-lg-2">
                        <input type="text" class="form-control" placeholder="可按中文姓名" id="search" name="search"/>
                    </div>
                        <div class="col-lg-2">
                        <input type="text" class="form-control" placeholder="员工编号" id="staff_id" name="staff_id"/>
                    </div>
                    <div class="col-lg-1">
                        <button  type="submit" class="btn btn-default search_btn">搜索</button>
                    </div>
                    <div class="col-lg-1">
                        <button class="btn btn-primary add_new statistics_btn" type="button" data-toggle="modal"
                                data-target="#cateModal">统计
                        </button>
                    </div>
                    <div class="col-lg-1">
                        <button class="btn btn-primary add_new export_btn" type="button" data-toggle="modal"
                                data-target="#cateModal">导出
                        </button>
                    </div>
             
                </div>
            </form>
     

        </div>
    </div>

</div>
<div class="panel">
    <table class="table table-striped table-hover"  style="font-size:14px;">
        <thead>
        <tr>
            <th class="col-xs-1">ID</th>
            <th class="col-xs-1">名字</th>
            <th class="col-xs-1">工号</th>
            <th class="col-xs-1">考勤规则</th>

        </tr>
        </thead>
        <tbody id="show">
        @if (isset($data)||empty($data))

            @foreach ($data as $t)
                <tr>
                    <td >{{ $t['user_id'] }}</td>
                    <td >{{ $t['name_cn'] }}</td>
                    <td >{{ $t['staff_id'] }}</td>
                    <td >{{ 11 }}</td>
                </tr>
                @if (isset($data['list'])||empty($data['list']))
                <tr>
                    <table class="table table-striped table-hover"  style="font-size:14px;">
                        <thead>
                        <tr>
                            <th class="col-xs-1">日期</th>
                            <th class="col-xs-1">上班打卡时间</th>
                            <th class="col-xs-1">下班打卡时间</th>
                            <th class="col-xs-1">标示</th>
                            <th class="col-xs-1">迟到</th>
                            <th class="col-xs-1">早退</th>
                            <th class="col-xs-1">上请类型/状态</th>
                            <th class="col-xs-1">下请类型/状态</th>
                            <th class="col-xs-1">备注</th>
                        </tr>
                        </thead>
                        <tbody id="show">
                        @foreach ($t['list'] as $list)
                            <tr>
                                <td class="col-xs-1">{{ $list['attendance_date'] }}</td>
                                <td class="col-xs-1">{{ $list['start_time'] }}</td>
                                <td class="col-xs-1">{{ $list['end_time'] }}</td>
                                <td class="col-xs-1">{{ $list['approval_half'] }}</td>
                                <td class="col-xs-1">{{ $list['late_time'] }}</td>
                                <td class="col-xs-1">{{ $list['early_time'] }}</td>
                                <td class="col-xs-1">{{ $list['approval_am_type'] }}/{{ $list['approval_am_status'] }}</td>
                                <td class="col-xs-1">{{ $list['approval_pm_type'] }}/{{ $list['approval_pm_status'] }}</td>
                                <td class="col-xs-1">{{ $list['abnormal_state'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        </table>
                </tr>
                @endif
            @endforeach
        @endif
        </tbody>
    </table>
</div>
<div class="pagination-left">
</div>

    <link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script>
    var str = window.location.pathname;
    $.each($('.list-group-item'),function(){
        if($(this).attr('href')==str){
            $(this).addClass('active');
        }
    });
    //分页
    $('.date_time').datepicker({'pickerPosition': 'bottom-left',dateFormat: 'yy-mm-dd'});
    
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
        location.href="/hr/attendance/attendance_home?page="+page+'&search='+search;

    });

    //导出功能
    $('.export_btn').click(function(){
        var attendance_start_date = $("#attendance_start_date").val();
        var attendance_end_date = $("#attendance_end_date").val()
        if(attendance_start_date =='' || attendance_end_date ==''){
            var error_data = {'error_msg': '请选择开始结束日期'};
            show_message(0,error_data);
            return ;
        }
        window.location.href='/hr/attendance/export_attendance?attendance_start_date=' +attendance_start_date+ '&attendance_end_date='+attendance_end_date;
    })
    $('.statistics_btn').click(function(){
        var attendance_start_date = $("#attendance_start_date").val();
        var attendance_end_date = $("#attendance_end_date").val()
        if(attendance_start_date =='' || attendance_end_date ==''){
            var error_data = {'error_msg': '请选择开始结束日期'};
            show_message(0,error_data);
            return ;
        }
        window.location.href='/hr/attendance/export_attendance?attendance_start_date=' +attendance_start_date+ '&attendance_end_date='+attendance_end_date;
    })
</script>
@endsection
