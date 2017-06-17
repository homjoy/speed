@extends('layouts.master')
@section('content')
    <div class="panel" >
        <div class="panel-body" >

        <div id="page-content" class="panel">
            <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
                <li class="item" role="presentation"> <a href="/meeting/home">会议室列表</a></li>
                <li class="active" role="presentation"><a href="/meeting/meeting_upsert" class="application_num">添加/修改会议室</a></li>
            </ul>
        </div>
      <div class="table-container">
        <div class="row row-offcanvas row-offcanvas-right">
<!--            <div class="col-xs-4 col-sm-2 sidebar-offcanvas" id="sidebar">-->
<!--                <div class="list-group">-->
<!--                    <a href="/meeting/meeting_upsert" class="list-group-item active">添加/修改会议室</a>-->
<!--                    <a href="/meeting/home" class="list-group-item ">会议室列表</a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-xs-12 col-sm-10">-->
                <form class="form-horizontal" id="meetingForm" role="form" action="#" method="post">
                    <input type="hidden" name="room_id" id="room_id" value="{{ $data['room_id'] or '' }} ">
                    <div class="container">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室编号：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室编号，例如1001" id="room_sn" name="room_sn" value="{{ $data['room_sn'] or ''}}" datatype="*" errormsg="不能为空"  />
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室名称：</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室名称" id="room_name" name="room_name" value="{{ $data['room_name'] or ''}}" datatype="*" errormsg="不能为空" />
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室位置：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室位置" id="room_position" name="room_position" value="{{ $data['room_position'] or ''}}" datatype="*" errormsg="不能为空"  />
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室容量：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室容纳人数" id="room_capacity" name="room_capacity" value="{{ $data['room_capacity'] or ''}}" datatype="*" errormsg="不能为空"  />
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="extension" class="col-xs-2 control-label">会议室分机：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室分机" id="extension" name="extension" value="{{ $data['extension'] or '' }}"   />

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type" class="col-xs-2 control-label">会议室类型：</label>

                                <div class="col-xs-8">
                                    <select class="form-control form-input input-xlarge" id="type" name="type">
                                        <option value='1' @if(isset($data['office_id']  ) && $data['type'] ==1)  SELECTED @endif >会议室</option>
                                        <option value='2' @if(isset($data['office_id']  ) && $data['type'] ==2)  SELECTED @endif>活动场地 - 用于活动</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="room_area" class="col-xs-2 control-label">会议室地区：</label>

                                <div class="col-xs-8">
                                    <select class="form-control form-input input-xlarge" id="office_id" name="office_id">
                                        @foreach($office_list as $val)
                                            <option value='{{$val['office_id']}}' @if(isset($data['office_id']  ) && ($data['office_id'] ==$val['office_id']))  SELECTED @endif >{{$val['office_position']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室说明：</label>
                                <div class="col-xs-8">
                                    <textarea class="form-control" rows="3" placeholder="请填写会议室设备描述" id="room_detail" name="room_detail"  datatype="*" errormsg="不能为空"  >{{$data['room_detail'] or ''}}</textarea>
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">状态：</label>
                                <div class="col-xs-8">
                                    <input type="radio" value="1" @if(isset($data['status']  ) && $data['status'] !=0)  checked @endif name="status"/>可预定
                                    <input type="radio" value="0" @if(isset($data['status']  ) && $data['status'] ==0)  checked @endif name="status"/>不可预定
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="button" class="btn btn-success" id="saveMeetingBtn">
                                    <i class="glyphicon glyphicon-plus"></i>保存
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
<!--    </div>-->
</div>
    <!--end container-->
</div>
    <link rel="stylesheet" href="/static/css/validator.css">
    <script type="text/javascript" src="/static/js/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript">
        $(function () {
            var validform_obj =  $("#meetingForm").Validform({
                tiptype:3,
                postonce:true,
                showAllError:true,
                ajaxPost:true
            });

            $("#saveMeetingBtn").on('click',function(){
                var valid_result = validform_obj.check(false)
                if(!valid_result){
                    return false;
                }
                $.ajax({
                    url: "/meeting/ajax_meeting_upsert",
                    method: "POST",
                    data: $("#meetingForm").serialize(),
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


        })
    </script>
@endsection