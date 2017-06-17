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
            <form id="searchForm" class="form-inline  form-horizontal" role="form" action="" method="get">
                <div class="row">

                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input type="text" class="form-control" placeholder="消息ID" id="op_user_id" name="op_user_id" value="{{$search_params['op_user_id'] or  ''}}"/>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <select name="channel" id="channel" class="form-control">
                            <option value="" >请选择</option>
                            @foreach ($notify_type as $notify_type => $notify_value)
                            <option @if($search_params['channel'] == $notify_type) selected @endif value="{{$notify_type}}" >{{$notify_value}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <input type="submit" class="btn btn-default" id="retBtn" value="搜索"/>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <input type="button" class="btn btn-primary" id="addBtn" value="添加"/>
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
                <th class="col-xs-1">发送类型</th>
                <th class="col-xs-1">发送标题</th>
                <th class="col-xs-1">发送对象</th>
                <th class="col-xs-2">发送数据</th>
                <th class="col-xs-1">优先级</th>
                <th class="col-xs-1">发送状态</th>
                <th class="col-xs-1">发送人ID</th>
                <th class="col-xs-1">发送时间</th>
                <th class="col-xs-1">更新时间</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody id="show">
            @if (!empty($data))

            @foreach ($data as $t)
            <tr>

                <td>{{ $t['msg_id'] }}</td>
                <td>{{ $t['channel'] }}</td>
                <td>{{ $t['title'] }}</td>
                <td>{{ $t['send_object'] }}</td>
                <td>{{$t['content']}}
<!--                    <span class="hide"> {{$t['content']}}</span></td>-->
                <td> {{ $t['weights'] }} </td>
                <td> {{ $t['send_status'] }} </td>
                <td> {{ $t['op_user_id'] }} </td>
                <td> {{ $t['send_at'] }} </td>
                <td> {{ $t['update_time'] }} </td>
                <td>
                    <a  data-msg_id="{{$t['msg_id'] or  ''}}" class="btn btn-sm btn-default toggle-detail look_btn"><span class="glyphicon glyphicon-zoom-in"></span>详情</a>

                </td>
                <td>
                    <a  data-msg_id="{{$t['msg_id'] or  ''}}" class="btn btn-sm btn-warning  update_btn"><span class="glyphicon glyphicon-log-in rm-disable"></span>修改</a>

                </td>

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

            location.href = "/message_send/message_list?page=" + page+'&op_user_id='+$("#op_user_id").val()+'&channel='+$("#channel").val();
        });

        //tokeninput
        var initUser= '{!!@json_encode($search_user_info) !!}';
        try{
            initUser = $.parseJSON(initUser);
        }catch(e){
            initUser = [];
        }
        $('#op_user_id').tokenInput("/structure/depart/ajax_search_name",{
            queryParam:'search',
            tokenValue:'user_id',
            prePopulate: initUser,
            tokenLimit: 1,
            onAdd:function(ret) {
                $('#op_user_id').val(ret['user_id']);
            }
        });
        $('.look_btn').click(function () {

            var data = $(this).data();
            var msg_id = data.msg_id;
            if(msg_id){
                window.location.href = '/message_send/message_user_list?msg_id='+msg_id;
            }

        });
        $('#addBtn').click(function () {
           window.location.href = '/message_send/message_upsert';

        });
        $('.update_btn').click(function () {
            var data = $(this).data();
            var msg_id = data.msg_id;
            if(msg_id){
                window.location.href = '/message_send/message_upsert?msg_id='+msg_id;
            }

        });
    </script>

    @endsection
