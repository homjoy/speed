@extends('layouts.master')
@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <div class="panel" >
        <div class="panel-body" >

    <div id="page-content" class="panel">
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li class="active" role="presentation"> <a href="/meeting/home">会议室列表</a></li>
            <li class="item" role="presentation"><a href="/meeting/meeting_upsert" class="application_num">添加/修改会议室</a></li>
        </ul>
    </div>
    <div class="table-container">
        <div class="row row-offcanvas row-offcanvas-right">

                <table class="table table-striped table-hover" style="font-size:14px;">
                    <thead>
                    <th class="col-xs-1">ID</th>
                    <th class="col-xs-1">编号</th>
                    <th class="col-xs-1">名称</th>
                    <th class="col-xs-2">位置</th>
                    <th class="col-xs-1">容纳人数</th>
                    <th class="col-xs-1">状态</th>
                    <th class="col-xs-2">城市</th>
                    <th class="col-xs-3">操作</th>
                    </thead>
                    <tbody>
                    @if(isset($data))
                        @foreach ($data as $t)
                            <tr>
                                <td>{{ $t['room_id'] }}</td>
                                <td>{{ $t['room_sn'] }}</td>
                                <td>{{ $t['room_name'] }}</td>
                                <td>{{ $t['room_position'] }}</td>
                                <td>{{ $t['room_capacity'] }}</td>
                                <td>{{ $t['status'] }}</td>
                                <td>{{ $t['office_position'] }}</td>
                                <td>
                                    <a data-id="{{ $t['room_id'] }}" href="javascript:;" class="btn btn-sm btn-default show_detail">查看</a>&nbsp;<a href="/meeting/meeting_book?room_id={{ $t['room_id'] }}" class="btn  btn-sm btn-info">人员</a>&nbsp;<a href="/meeting/meeting_upsert?room_id={{ $t['room_id'] }}" class="btn  btn-sm btn-warning">修改</a>&nbsp;<a href="/meeting/service_upsert?id={{$t['room_id']}}" class="btn btn-sm btn-default">服务</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                <div class="pagination-left panel-body">
                </div>
            </div>
        </div>
            <!--查看详情模版-->
            <div class="modal fade detailModal" id="docDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <table class="table table-striped" id="showTable">
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
    <script>
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

            location.href = "/meeting/home?page=" + page;

        });
        $('.show_detail').click(function () {
            var _this = $(this),
                    str='<tr class="text-center"><th colspan=4>详情</th></tr>';
            $.post('/meeting/ajax_room_info', {room_id:_this.attr('data-id')}, function (ret) {
                        $("#showTable").empty();
                        if (ret.code==200) {
                            var ser = ret.data;

                            $.each(ser, function(i,val) {
                                if( !val.name )val.name='';
                                if( !val.config )val.config='';
                                str+="<tr><th>服务名称:</th><td >"+val.name+"</td>"+
                                    "<tr><th >负责人:</th><td >"+val.config+"</td>";
                            });
                        } else {
                        }
                        $('#showTable').html(str);
                        $('#docDetail').modal('show');
              }, 'json');
        });
    </script>
@endsection