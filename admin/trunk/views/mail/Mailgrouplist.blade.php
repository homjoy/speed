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
        .selectpicker{
            display: none;
        }
    </style>
    <link rel="stylesheet" href="/static/css/bootstrap-select.css">

    <div class="panel">
        <div class="panel-body">

            <div class="table-container">

                <form action="" name="editMailMemeber">
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>部门</h5></div>
                        <div class="col-xs-5">
                            <select class="col-lg-12 selectpicker" data-live-search="true" name="depart_id"
                                    id="depart_id" value="{{ $current_depart_info['current_depart_id'] or '' }}">
                                @if (empty($current_depart_info['current_depart_id']))
                                    <option value=""></option>
                                @else
                                    <option value="{{$current_depart_info['current_depart_id']}}">{{ $current_depart_info['current_depart_name'] }}</option>
                                @endif

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
                                      size="15">@if (!empty($data))@foreach ($data as $t){{ $t }}
@endforeach
@endif</textarea>
                        </div>
                    </div>
                    </br>
                    </br>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>成员数:</h5></div>
                        <div class="col-xs-5">
                           {{$group_member_number}}
                        </div>
                    </div>
                    </br>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>备注</h5></div>
                        <div class="col-xs-5">
                            <textarea name="memo" id="memo" cols="30" rows="2" class="form-control text-left"
                                      size="15">{{  $group_info['memo'] or ''}}
                                </textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-5 text-center">
                            <input type="hidden" value="{{ $group_name }}" id="group_name" name="group_name"/>
                            <input type="hidden" value="{{ $group_id }}" id="group_id" name="group_id"/>
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

    <script src="/static/js/bootbox.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.submit_email_group').click(function () {
                var group_name = "{{ $group_name }}";
                var arr = $("#multiselect_to").val();
                var depart_id = $('#depart_id').val();
                var group_id = $('#group_id').val();
                var memo = $('#memo').val();
                $('.submit_email_group').attr('disabled', true);
                $.ajax({
                    url: "/mail/ajax_mail_group_edit",
                    method: "POST",
                    data: {group_name: group_name, mail: arr,group_id:group_id,depart_id:depart_id,memo:memo},
                    dataType: "json",
                    success: function (msg) {
                        $('.submit_email_group').removeAttr('disabled');
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            setTimeout("location.reload()", 4000);
                        } else {
                            var ret = [];
                            ret['error_msg'] = msg['error_msg'];
                            ret['error_detail'] = '';//msg['error_msg'];
                            $('#errror_msg').text(msg['error_msg'])
                            show_message(msg.code, ret);
                        }
                    }
                });
            })
        })

    </script>
@endsection