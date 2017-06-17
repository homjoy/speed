@extends('layouts.master')
@section('content')
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
    <script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <div class="panel" >
        <div class="panel-body" >

     <div class="from-container">
        <form id="searchForm" class="form-horizontal" role="">
            <div class="row">
                <div class=" col-lg-3 col-xs-3 col-sm-3">
                    <input type="search" class="form-control form_datetime" id="start_time" placeholder="开始日期（含）" value="{{ $data['start_time'] or '' }}"  name="start_time">
                </div>

                <div class=" col-lg-3 col-xs-3 col-sm-3">
                    <input type="search" class="form-control form_datetime " id="end_time" placeholder="结束日期（含）" value="{{ $data['end_time'] or '' }}" name="end_time">
                </div>
                <div class=" col-lg-3 col-xs-3 col-sm-3">
                    <a href="javascript:void(0)" formId="searchForm" class="btn btn-default search_btn"><i class="glyphicon glyphicon-search"></i>搜索</a>
                </div>
            </div>
        </form>
        <div class="panel-info">
            <div class="panel-heading">
                预定列表 [会议室名称:{{ $data['room_name'] or '' }}会议室位置:{{ $data['room_position'] or '' }} 容纳人数:{{ $data['room_capacity'] or '' }}]
            </div>
            <table class="table table-striped" id="orderHistory" style="font-size: 13px">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>预定日期</th>
                    <th>预定星期</th>
                    <th>预定人</th>
                    <th>预定原因</th>
                    <th>重复性</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $html ='';
                if(!empty($room_list)){


                    foreach ($room_list as $key => $value) {
                        $html .= "<tr>
                                    <td width=''>{$value['book_id']}</td>
                                    <td width=''>{$value['start_time']}－{$value['end_time']}</td>
                                    <td width=''>{$value['start_w']}</td>
                                    <td width=''>{$value['name_cn']}</td>
                                    <td width=''>{$value['topic']}</td>
                                    <td width=''>{$value['is_repeat']}</td>
                                    <td width=''>
                                    <a href='javascript:;' data-id='{$value['book_id']}' class='btn btn-sm btn-del btn-danger'>取消</a>
                                    </td>
                                    </tr>";

                    }
                    echo "$html";
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>
     <div class="pagination-left">
     </div>
 </div>
        </div>
<!--end container-->
<script src="/static/js/bootbox.min.js"></script>
<script type="text/javascript">
    $(function () {
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
            location.href = "/meeting/meeting_book?page=" + page+"&start_time="+$('#start_time').val()+'&end_time='+$('#end_time').val()+'&room_id='+room_id;
        });

    })

    //属性添加 时间处理
    var str =  location.href;
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true}).on('changeDate', function (e) {
    });

    $(".search_btn").click(function(){
        location.href = "/meeting/meeting_book?start_time="+$('#start_time').val()+'&end_time='+$('#end_time').val()+'&room_id='+room_id;
    });
    String.prototype.GetValue= function(para) {
        var reg = new RegExp("(^|&)"+ para +"=([^&]*)(&|$)");
        var r = this.substr(this.indexOf("\?")+1).match(reg);
        if (r!=null) return unescape(r[2]); return null;
    }
    if(str.GetValue("start_time")){
        $('#start_time').val(str.GetValue("start_time"));
    }
    if(str.GetValue("end_time")){
        $('#end_time').val(str.GetValue("end_time"));
    }
    room_id =str.GetValue("room_id");

    $('.btn-del').click(function () {
        var _this = $(this)
        bootbox.confirm('确认取消会议？', function (result) {
            if (result) {
                _this.attr('disabled',true);
                var id = _this.attr('data-id');

                $.ajax({
                    url: "/meeting/ajax_meeting_cancel",
                    method: "POST",
                    data: {book_id:id},
                    dataType: "json",
                    success: function (msg) {
                        _this.removeAttr('disabled');
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            window.location.reload();
                        } else {
                            show_message(msg.code, msg);
                        }
                    }
                });
            } else {

            }
        });
    })
</script>
@endsection