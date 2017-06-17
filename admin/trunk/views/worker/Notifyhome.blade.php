@extends('layouts.master')

@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <style type="text/css">

        .token-input-token,.token-input-input-token{line-height: 23px !important;}
    </style>
    <div class="panel" >
        <div class="panel-body" >

        <div class="container">
            <div class="from-container">
                <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/worker/notify_home" method="get">
                    <div class="row">

                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <input type="text" class="form-control" placeholder="接收人ID" id="to_id" name="to_id" value="{{$search_params['to_id'] or  ''}}"/>
                        </div>
                        {{--<div class="col-lg-3 col-md-3 col-sm-3">--}}
                            {{--<input type="text" class="form-control" placeholder="发送标题" id="title" name="title" value="{{$search_params['title'] or  ''}}"/>--}}
                        {{--</div>--}}
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <select name="channel" id="channel" class="form-control">
                                <option value="" >请选择</option>
                           @foreach ($notify_type as $notify_type => $notify_value)
                                    <option @if($search_params['channel'] == $notify_type) selected @endif value="{{$notify_type}}" >{{$notify_value}}</option>
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
                    <th class="col-xs-1">消息ID</th>
                    <th class="col-xs-1">接收人ID</th>
                    <th class="col-xs-1">类型</th>
                    <th class="col-xs-1">标题</th>
                    <th class="col-xs-2">发送数据</th>
                    <th class="col-xs-1">状态</th>
                    <th class="col-xs-1">权重</th>
                    <th class="col-xs-1">发送次数</th>
                    <th class="col-xs-1">发送时间</th>
                    <th class="col-xs-1">更新时间</th>

                </tr>
                </thead>
                <tbody id="show">
                @if (!empty($data))

                    @foreach ($data as $t)
                        <tr>
                            <td>{{ $t['notify_id'] }}</td>
                            <td>{{ $t['to_id'] }}</td>
                            <td>{{ $t['channel'] }}</td>
                            <td>{{ $t['title'] }}</td>
                            <td><a  class="btn btn-sm btn-default  toggle-detail look_btn"><span class="glyphicon glyphicon-zoom-in"></span>详情</a>
                                <code class="hide show_info">{{$t['after_content']}}</code>
                                <span class="hide resource_data"> {{$t['temp_content']}}</span>
                                <span class="hide"> {{$t['content']}}</span></td>
                            <td> {{ $t['status'] }} </td>
                            <td> {{ $t['weights'] }} </td>
                            <td> {{ $t['send_times'] }} </td>
                            <td> {{ $t['send_at'] }} </td>
                            <td> {{ $t['update_time'] }} </td>
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
        <!--model-->
        <div class="modal fade detailModal" id="docDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:750px !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 >推送信息详情 <span id="history-head"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="panel panel-default" id="showTable">

                        </div>
                        <div class="panel panel-default" id="resouceData">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭 </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            //model
            $('.toggle-detail').click(function(ev){
                var html  = $(this).siblings('.show_info').html();
                html = htmlspecialchars_decode(html)
                var resource_data  = $(this).siblings('.resource_data').html();
                resource_data = htmlspecialchars_decode(resource_data)
                $('#showTable').html(html);
                $('#resouceData').html(resource_data);
                $('#docDetail').modal('show');


            });

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

                location.href = "/worker/notify_home?page=" + page+'&to_id='+$("#to_id").val()+'&channel='+$("#channel").val();
            });
            //tokeninput
            var initUser= '{!!@json_encode($search_user_info) !!}';
            try{
                initUser = $.parseJSON(initUser);
            }catch(e){
                initUser = [];
            }
            $('#to_id').tokenInput("/structure/depart/ajax_search_name",{
                queryParam:'search',
                tokenValue:'user_id',
                prePopulate: initUser,
                tokenLimit: 1,
                onAdd:function(ret) {
                    $('.to_id').val(ret['user_id']);
                }
            });
            function htmlspecialchars_decode(str){
                str = str.replace(/&amp;/g,'&');
                str = str.replace(/&lt;/g,'<');
                str = str.replace(/&gt;/g,'>' );
                str = str.replace(/&quot;/g,'"' );
                str = str.replace(/&#039;/g,"'");
                return str;
            }

        </script>

@endsection
