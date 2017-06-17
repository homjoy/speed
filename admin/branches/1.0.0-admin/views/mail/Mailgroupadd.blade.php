@extends('layouts.master')
@section('content')
    <style type="text/css">

        .ul_input {
            overflow: auto;
            height: 200px;
            border: 1px solid #ddd;
        }

        .ul_input li {
            padding: 3px 20px;
        }

        .selectpicker {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="/static/css/bootstrap-select.css">

    <div class="panel">
        <div class="panel-body">

            <div class="table-container">

                <form action="" name="editMailMemeber">
                    <div class="row ">
                        <div class="col-xs-3 text-right"><h5>邮件组</h5></div>
                        <div class="col-xs-5">
                            <input type="text" class="form-control" name='mail_group' id="mail_group"
                                   placeholder="邮件列表">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>部门</h5></div>
                        <div class="col-xs-5">
                            <select class="col-lg-12 selectpicker" data-live-search="true" name="depart_id"
                                    id="depart_id" value="">
                                    <option value="">请选择部门</option>
                                @if (!empty($department_list))
                                    @foreach ($department_list as $p)
                                        <option value="{{ $p['depart_id'] }}">{{ $p['depart_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>

                        </div>
                    </div>
                    </br>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>邮箱成员</h5></div>
                        <div class="col-xs-5">
                            <textarea name="mail" id="multiselect_to" cols="30" rows="15" class="form-control text-left"
                                      size="15">@if (!empty($data))@foreach ($data as $t){{ $t }}@endforeach @endif</textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>备注</h5></div>
                        <div class="col-xs-5">
                            <textarea name="memo" id="memo" cols="30" rows="3" class="form-control text-left"
                                      size="15"></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-5 text-center">

                            <input type="button" value="提交" class="btn btn-success submit_email_group"/>
                        </div>
                    </div>

                </form>
            </div>


            </form>
            <div class="row hidden">
                <div class="errror_msg"></div>
            </div>
        </div>
    </div>
    <script src="/static/js/bootstrap-select.js"></script>

    <script type="text/javascript">
        $(function () {

            //保存数据
            $(".submit_email_group").click(function () {
                var mail_group = $("#mail_group").val();
                var depart_id = $('#depart_id').val();
                var memo = $('#memo').val();
                if (!mail_group) {
                    var ret = [];
                    ret['error_msg'] = '邮件列表不能为空';
                    show_message(0, ret)
                    return;
                }

                var arr_string = $("#multiselect_to").val();
                $('#add_btn').attr('disabled', true);
                $.ajax({
                    url: "/mail/ajax_mail_group_list_add",
                    method: "POST",
                    data: {group_name: mail_group, mail: arr_string,depart_id:depart_id,memo:memo},
                    dataType: "json",
                    success: function (msg) {
                        $('#add_btn').removeAttr('disabled');
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            //setTimeout("location.reload()", 1500);
                        } else {
                            var ret = [];
                            ret['error_msg'] = msg['error_detail'];
                            show_message(msg.code, ret);
                        }
                    }
                });

            })

        })

    </script>
@endsection