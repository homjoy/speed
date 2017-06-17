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
    <div class="panel-heading" ><h3 class="panel-title">发送人员列表</h3> </div>

    <div class="panel-body" >
    <p></p>

    <div class="table-container">
        <input type="text" style="display: none;"  id="msg_id" name="msg_id">
        <table class="table table-striped table-hover"  style="font-size:14px;">
            <thead>
            <tr>
                <th class="col-xs-1">ID</th>
                <th class="col-xs-1">内容ID</th>
                <th class="col-xs-2">接受者姓名</th>
                <th class="col-xs-2">接收人ID</th>
                <th class="col-xs-2">邮箱</th>
                <th class="col-xs-2">手机</th>
                <th class="col-xs-1">发送状态</th>
                <th class="col-xs-1">更新时间</th>
            </tr>
            </thead>
            <tbody id="show">
            @if (!empty($data))

            @foreach ($data as $t)
            <tr>
                <td>{{ $t['user_list_id'] }}</td>
                <td>{{ $t['msg_id'] }}</td>
                <td>{{ $t['name_cn'] }}</td>
                <td>{{ $t['to_id'] }}</td>
                <td>{{$t['mail']}}
                <td> {{ $t['phone'] }} </td>
                <td> {{ $t['status'] }} </td>
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
</div>
    <script type="text/javascript">
        var str = location.href,
            strname = window.location.pathname;

        $.each($('.list-group-item'),function(){
            if($(this).attr('href')==strname){
                $(this).addClass('active');
            }
        });
        String.prototype.GetValue= function(para) {
            var reg = new RegExp("(^|&)"+ para +"=([^&]*)(&|$)");
            var r = this.substr(this.indexOf("\?")+1).match(reg);
            if (r!=null) return unescape(r[2]); return null;
        }
        $('#msg_id').val(str.GetValue("msg_id"));
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

            location.href = "/message_send/message_user_list?page=" + page+'&msg_id='+$("#msg_id").val();
        });


    </script>

    @endsection
