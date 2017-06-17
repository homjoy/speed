@extends('layouts.master')

@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
</style>
<!--<div class="col-xs-6 col-sm-2">-->
<!--    @include('hr/attendance/Navbar')-->
<!--</div>-->
<!--<div class="col-xs-12 col-sm-10">-->
<div class="panel">
    <div class="panel-body">
        <div class="panel-heading">
            <h3 class="panel-title">
                添加/修改考勤人员
            </h3>
        </div>
        <div class="panel-body">
            <form  id="attendance" class="form-horizontal" role="form" >
                <div class="form-group">
                    <label class="control-label col-lg-3" for="">姓名</label>
                    <div class="col-lg-5">
                        @if (!empty($data['staff_info']['id']))
                        <input  type="text" class="form-control " placeholder="" name="search"  disabled="" value="{{$data['staff_info']['name_cn']}}">
                        @else
                        <input  type="text" class="form-control " placeholder="" name="search" id = 'user_id' value="">
                        @endif
                        
                        <input  type="hidden"  name="user_id" id ="hidden_user_id" value="{{ $data['staff_info']['user_id'] or ''}}">
                        <input  type="hidden"  name="id"  value="{{ $data['staff_info']['id'] or ''}}">
                    </div>

                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="">考勤组</label>
                    <div class="col-lg-5">
                        <select class="form-control" name="work_id" id="work_id">
                            <option value="">请选择考勤规则</option>
                            @if (!empty($data['attendance_group']))
                                @foreach ($data['attendance_group'] as $t)
                                <option @if(!empty($data['staff_info']['work_id']) && ($data['staff_info']['work_id'] == $t['id'])) selected @endif value="{{ $t['id']}}">{{ $t['name']}}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="">状态</label>
                    <div class="col-lg-1">
                       <input type="radio" name="status"  @if (  !empty($data['staff_info']) && $data['staff_info']['status'] ==1) checked @endif value="1" > 考勤

                    </div>
                    <div class="col-lg-2"> <input type="radio" name="status" @if (  !empty($data['staff_info']) && $data['staff_info']['status'] ==3) checked @endif value="3" > 不考勤</div>

                </div>
                <div class="form-group">
                    <div class="col-lg-10 text-center">
                        <button  formId="userForm" class="btn btn-primary add_new submit_btn" id="userSubmit" type="submit" data-toggle="modal" data-target="#cateModal">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>
<script>
//时间
// $('.date-time').datepicker({'pickerPosition': 'bottom-left',dateFormat: 'hh:ii:ss','minView':'hour','maxView':'hour'});
$('.add_new').click(function () {
    var hidden_user_id = $("#hidden_user_id").val();
    var work_id = $("#work_id").val()
    if(!hidden_user_id){
        var ret = []
        ret['error_msg'] = '用户不能为空';
        show_message(400, ret);
        return;
    }
    if(!work_id){
        var ret = []
        ret['error_msg'] = '考勤组不能为空';
        show_message(400, ret);
        return;
    }
    $.ajax({
            url: "/hr/attendance/ajax_attendance_user_update_add",
            method: "GET",
            data: $("#attendance").serialize(),
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

 })

    $('#user_id').tokenInput("/structure/depart/ajax_search_name",{
        queryParam:'search',tokenValue:'user_id',tokenLimit: 1,onAdd:function(ret) {
            $('#hidden_user_id').val(ret['user_id']);
        }
    });
    
</script>
@endsection
