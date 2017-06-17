@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="/static/css/validator.css">
<script type="text/javascript" src="/static/js/Validform_v5.3.2_min.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">

</style>
<div class="panel" >
    <div class="panel-body" >
    <div class="container">
        <div class="from-container">
            <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/prompt/prompt_home" method="get">
                <div class="row">

<!--                    开始时间 结束时间   状态-->
<!--                    <div class="col-lg-3 col-md-3 col-sm-3">-->
<!--                        <input type="search" class="form-control search" placeholder="开始日期（含）" id="start_time" name="admin_start_time"value="{{$search_params['admin_start_time'] or  ''}}">-->
<!--                    </div>-->
<!---->
<!--                    <div class="col-lg-3 col-md-3 col-sm-3">-->
<!--                        <input type="search" class="form-control search" placeholder="结束日期（含）" id="end_time"  name="admin_end_time"value="{{$search_params['admin_end_time'] or  ''}}">-->
<!--                    </div>-->
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <select    class="form-control" name="status" id="status" value="{{$search_params['status'] or  ''}}">
                            <option value="" @if(empty($search_params['status'])) selected @endif> 状态</option>
                            <option value="0" @if(isset($search_params['status']) && $search_params['status'] == '0') selected @endif>无效</option>
                            <option value="1" @if(isset($search_params['status']) && $search_params['status'] == '1') selected @endif>有效</option>
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2">
                        <input type="submit" class="btn btn-default" id="retBtn" value="搜索"/>
                    </div>
                     <div class="col-lg-1  col-md-1 col-sm-2">
                        <button class="btn btn-primary add_new add_btn" type="button" data-toggle="modal"
                                data-target="#cateModal">添加
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped table-hover"  style="font-size:14px;">
            <thead>
            <tr>
                <th class="col-xs-1">消息ID</th>
                <th class="col-xs-3">发送标题</th>
                <th class="col-xs-3">消息链接</th>
                <th class="col-xs-2">开始时间</th>
                <th class="col-xs-2">结束时间</th>
                <th >操作消息</th>
<!--                <th></th>-->
            </tr>
            </thead>
            <tbody id="show">
            @if (!empty($data))

            @foreach ($data as $t)
            <tr>
                <td>{{ $t['notice_id'] }}</td>
                <td><pre>{{ $t['title'] }}</pre></td>
                <td><pre>{{ $t['link'] }}</pre></td>
                <td>{{ $t['start_time'] }}</td>
                <td>{{ $t['end_time'] }}</td>

                <td >
                    <a class="btn btn-sm btn-warning  edit_btn" type="button" data-toggle="modal"
                       data-notice_id="{{$t['notice_id']}}" ><span class="glyphicon glyphicon-log-in rm-disable"></span>修改
                    </a>
                </td>
<!--                <td>-->
<!--                    <a  class="btn btn-sm  btn-danger look_btn" data-notice_id="{{$t['notice_id']}}"-->
<!--                        title="查看"><span class="glyphicon glyphicon-search"></span>标记-->
<!--                    </a>-->
<!--                </td>-->
            </tr>
            @endforeach
            @endif
            </tbody>
        </table>

    </div>
    <div class="pagination-left">
    </div>
</div>
    </div>
    <link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
    <script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $('.search').datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true});
        $('.add_btn').click(function () {
            window.location.href = '/prompt/update_add_prompt';
        });
        $('.look_btn').click(function () {
            var data = $(this).data();
            var notice_id = data.notice_id;
            if(notice_id){
                window.location.href = '/prompt/mark_prompt?notice_id=' + notice_id;
            }
        });
        $('.edit_btn').click(function () {
            var data = $(this).data();
            var notice_id = data.notice_id;
            if(notice_id){
                window.location.href = '/prompt/update_add_prompt?notice_id=' + notice_id;
            }
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

            location.href="/prompt/prompt_home?page="+page+'&start_time='+$('#start_time').val()
                +'&end_time='+$('#end_time').val();

        });
    </script>

    @endsection
