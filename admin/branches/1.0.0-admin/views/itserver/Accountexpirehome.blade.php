@extends('layouts.master')

@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>

<div class="panel">
    <div class="panel-body" >

    <div class="container">
        <div class="from-container">
            <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/itserver/account_expire_home" method="get">
                <div class="row">

                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <input type="text" class="form-control" placeholder="用户ID" id="user_id" name="user_id" value="{{$search_params['user_id'] or  ''}}"/>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <select name="account_type" id="account_type" class="form-control">
                            <option value="" >请选择</option>
                            @foreach ($expire_type as $notify_type => $notify_value)
                             <option @if($search_params['account_type'] == $notify_type) selected @endif value="{{$notify_type}}" >{{$notify_value}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <input type="submit" class="btn btn-primary" id="retBtn" value="搜索"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped table-hover"  style="font-size:14px;">
            <thead>
            <tr>
                <th class="col-xs-1">过期ID</th>
                <th class="col-xs-1">用户ID</th>
                <th class="col-xs-3">登陆名</th>
                <th class="col-xs-3">修改时间</th>
                <th class="col-xs-2">状态</th>
                <th class="col-xs-2">帐号类型</th>
            </tr>
            </thead>
            <tbody id="show">
            @if (!empty($data))

            @foreach ($data as $t)
            <tr>
                <td>{{ $t['id'] }}</td>
                <td>{{ $t['user_id'] }}</td>
                <td>{{ $t['login_name'] }}</td>
                <td> {{ $t['update_time'] }} </td>
                <td> {{ $t['status'] }} </td>
                <td>{{ $t['account_type'] }}</td>

            </tr>
            @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <div class="pagination-left">
    </div>
    <!--</div>-->

</div>
    <script type="text/javascript">
        //分页
        var count = '{!! json_encode($count) !!}';
        try {
            count = $.parseJSON(count);
        } catch (e) {
            count = [];
        }
        var page = '{!! json_encode($page) !!}';
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

            location.href = "/itserver/account_expire_home?page=" + page+'&user_id='+$("#user_id").val()+'&account_type='+$("#account_type").val();
        });


    </script>

    @endsection