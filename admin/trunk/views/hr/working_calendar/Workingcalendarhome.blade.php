@extends('layouts.master')

@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <div class="panel" >
        <div class="panel-body" >

        <div class="form-container">

            <div class="row form-group">

                <div class="col-xs-12 text-right">
                    <a class="btn btn-primary" href="/hr/working_calendar/working_calendar_upsert">添加</a>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table class="table-hover table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>
                        日期
                    </th>
                    <th>
                        类型
                    </th>
                    <th>
                        日历标题
                    </th>
                    <th>
                        状态
                    </th>
                    <th>
                        创建时间
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($data))
                    @foreach($data as $value)
                        <tr>
                            <td>{{$value['id']}}</td>
                            <td>{{$value['date']}}</td>
                            <td>
                                @if(isset($date_type[$value['type']]))
                                    {{$date_type[$value['type']]}}
                                @else
                                    {{$value['type']}}
                                @endif
                            </td>
                            <td>{{$value['title']}}</td>
                            <td>@if(isset($work_status[$value['status']]))
                                    {{$work_status[$value['status']]}}
                                @else
                                    {{$value['status']}}
                                @endif
                            </td>
                            <td>{{$value['create_time']}}</td>
                            <td><a class="btn btn-warning"
                                   href="/hr/working_calendar/working_calendar_upsert?id={{$value['id']}}">编辑</a></td>
                        </tr>
                    @endforeach
                @endif
                </tbody>

            </table>
            <div class="pagination-left">
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript">


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
            go: 'Go' //取消页面跳转
        }).on("switch", function (e, page) {

            location.href = "/hr/working_calendar/working_calendar_home?page=" + page;

        });

    </script>
@endsection
