@extends('layouts.master')
@section('content')
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <div class="panel" >
        <div class="panel-body" >

        <div class="container">
        <div class="row row-offcanvas row-offcanvas-right">
<!--            <div class="col-xs-4 col-sm-2 sidebar-offcanvas" id="sidebar">-->
<!--                <div class="list-group">-->
<!--                    <a href="/meeting/meeting_upsert" class="list-group-item">添加/修改会议室</a>-->
<!--                    <a href="/meeting/home" class="list-group-item ">会议室列表</a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-xs-12 col-sm-10">-->
                <form class="form-horizontal" id="meetingForm" role="form" action="#" method="post">
                    <input type="hidden" name="room_id" id="room_id" value="{{ $id or '' }} ">

                    <div class="container">
                        <div class="col-lg-12">
                            @foreach( $data as $value)
                                <div class="form-group">
                                    <div class="col-xs-8">
                                        <input type="checkbox" @if(!empty($service_rule[$value['service_id']])) checked @endif name="service_name[]" value="{{$value['service_id']}}">{{$value['name']}}
                                        <input type="hidden" name="rule_id_{{$value['service_id']}}" value="{{$service_rule[$value['service_id']]['rule_id'] or ''}}"/>
                                    </div>
                                    <div class="col-xs-8">
                                        <input type="input" class="form-control" id="config_user_{{$value['service_id']}}" name="config_user_{{$value['service_id']}}">
                                    </div>

                                </div>
                            @endforeach
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-success" id="saveServiceBtn">
                                    <i class="glyphicon glyphicon-plus"></i>保存
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
<!--</div>-->
    <!--end container-->

    <script type="text/javascript">
        $(function () {
            @foreach( $data as $value)

            var initUser= '{!!@json_encode($service_rule[$value["service_id"]]["config"]) !!}';
            try{
                initUser = $.parseJSON(initUser);
            }catch(e){
                initUser = [];
            }
            $('#config_user_{{$value["service_id"]}}').tokenInput("/structure/depart/ajax_search_name",
                        {
                            queryParam: 'search',
                            tokenValue: 'user_id',
                            prePopulate: initUser,
                            hintText: "请输入名字",
                            onAdd: function (item) {

                            },
                            onDelete: function (item) {

                            }


                        }
            );
            @endforeach
            $("#saveServiceBtn").on('click', function () {
                $.ajax({
                    url: "/meeting/ajax_service_upsert",
                    method: "POST",
                    data: $("#meetingForm").serialize(),
                    dataType: "json",
                    success: function (msg) {
                        if (msg.code == 200) {
                            show_message(msg.code, msg);
                            window.location.reload();
                        } else{
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