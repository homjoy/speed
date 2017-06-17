@extends('layouts.master')

@section('content')

    <div class="panel" >
        <div class="panel-body" >
            <div class="panel-heading">
                <h3 class="panel-title">
                    添加/修改考勤人员
                </h3>
            </div>
        <p></p>
        <div class="table-container">
            <form action="post" name="workForm" id="workForm">
                <input type="hidden" value="@if(isset($data['id'])) {{$data['id']}} @endif" name="id"/>
                <table class="table-hover table table-striped table-bordered">
                    <tbody>
                    <tr>
                        <td>日期</td>
                        <td><input type="input" name="date" datatype="date" errormsg="日期类型错误,必须为2016-01-01"
                                   value="@if(isset($data['date'])) {{$data['date']}} @endif" class=" form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>类型</td>
                        <td>
                            @foreach($date_type as $key=>$value)
                                <input type="radio" name="type" datatype="*" errormsg="不能为空" value="{{$key}}"
                                       @if(isset($data['type']) && $data['type'] == $key) checked @elseif(!isset($data['type']) && $key ==1 ) checked @endif>{{$value}}
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>日历标题</td>
                        <td><input type="input" datatype="*" errormsg="不能为空" name="title"
                                   value="@if(isset($data['title'])) {{$data['title']}} @endif" class=" form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>状态</td>
                        <td>
                            @foreach($work_status as $status_key=>$status_value)
                                <input type="radio" name="status" datatype="*" errormsg="不能为空" value="{{$status_key}}"
                                       @if(isset($data['status']) && $data['status'] == $status_key) checked @elseif (!isset($data['status']) && $status_key == 1 ) checked   @endif>{{$status_value}}
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center"><input type="button" value="提交"
                                                                   class="btn btn-primary submit_form" name="button"/>
                            <a href="/hr/working_calendar/working_calendar_home" class="btn btn-default">返回</a></td>
                    </tr>
                    </tbody>

                </table>
            </form>

        </div>
    </div>
    </div>
    <link rel="stylesheet" href="/static/css/validator.css">
    <script type="text/javascript" src="/static/js/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript">
        var validform_obj = $("#workForm").Validform({
            tiptype: 3,
            postonce: true,
            showAllError: true,
            ajaxPost: true,
            datatype: {
                "date": function (gets, obj, curform, regxp) {
                    //参数gets是获取到的表单元素值，obj为当前表单元素，curform为当前验证的表单，regxp为内置的一些正则表达式的引用;
                    var reg1 = /^\d{4}-\d{2}-\d{2}$/;
                    if (reg1.test(gets)) {
                        return true;
                    }
                    return false;

                    //注意return可以返回true 或 false 或 字符串文字，true表示验证通过，返回字符串表示验证失败，字符串作为错误提示显示，返回false则用errmsg或默认的错误提示;
                },

            },
        });

        $(".submit_form").on('click', function () {
            var valid_result = validform_obj.check(false)
            if (!valid_result) {
                return false;
            }
            $.ajax({
                url: "/hr/working_calendar/ajax_working_calendar_upsert",
                method: "POST",
                data: $("#workForm").serialize(),
                dataType: "json",
                success: function (msg) {
                    if (msg.code == 200) {
                        show_message(msg.code, msg);
                        window.location.reload();
                    } else {
                        var ret = [];
                        ret['error_msg'] = msg['error_detail'];
                        show_message(msg.code, ret);
                    }
                }
            });

        });


    </script>
@endsection
