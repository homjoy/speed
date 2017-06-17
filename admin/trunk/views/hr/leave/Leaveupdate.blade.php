
<!--数据的修改或添加  里面要有连动的框 要有许多字段填写框 要能把老数据置灰 -->
@extends('layouts.master')
@section('content')
<link type="text/css" rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<style type="text/css">
    .submit_btn{
        margin-left: 5px;
        float: right;
    }

</style>
<div class="panel">
    <div class="panel-body">

<div class="row">

<ul id="myTab" class="nav nav-tabs">
    <li class="active"><a href="#edit" data-toggle="tab">信息修改</a></li>
</ul>
<div id="myTabContent" class="tab-content">
<div class="tab-pane fade in active" id="edit">
    <p></p>
    <form  id="editFrom" class="form-inline  form-horizontal" role="form" >
        <div class="row">
            <label class="control-label col-lg-3 col-md-3 col-xs-3" for="">请假ID号：</label>
            <div class="col-lg-4 col-md-4 col-xs-4">
                <input   readonly type="text" class="form-control " placeholder="" name="order_id" id='order_id' value="{{ $data['order_id'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3 col-md-3 col-xs-3" for="">请假人名：</label>
            <div class="col-lg-4 col-md-4 col-xs-4">
                <input disabled  type="text" class="form-control " placeholder="" name="name_cn" id='name_cn' value="{{ $data['name_cn'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3 col-md-3 col-xs-3" for="">开始时间：</label>
            <div class="col-lg-2 col-md-2 col-xs-2">
                <input   type="text" class="form-control form_datetime change_length" placeholder="" name="start_date" id="startDate" value="{{ $data['start_date'] or '' }}">

            </div>
            <div class="col-lg-2 col-md-2 col-xs-2">
                <select class="form-control change_length" name="start_half" id="startHalf" >
                    <option value="AM" @if((isset($data['start_half']) && $data['start_half'] == 'AM')||empty($data['start_half'])) selected @endif>上午</option>
                    <option value="PM" @if(isset($data['start_half']) && $data['start_half'] == 'PM') selected @endif>下午</option>
                </select>
            </div>
        </div>
        <div class="row">
                <label    class="control-label col-lg-3 col-md-3 col-xs-3" >结束时间：</label>
                <div class="col-lg-2 col-md-2 col-xs-2">
                    <input     type="text" class="form-control form_datetime change_length" placeholder="" name="end_date" id="endDate"value="{{ $data['end_date'] or '' }}" >
                </div>
                <div class="col-lg-2 col-md-2 col-xs-2">
                    <select class="form-control change_length" name="end_half" id="endHalf" >
                        <option value="AM" @if((isset($data['end_half']) && $data['end_half'] == 'AM')||empty($data['end_half'])) selected @endif>上午</option>
                        <option value="PM" @if(isset($data['end_half']) && $data['end_half'] == 'PM') selected @endif>下午</option>
                    </select>
                </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3 col-md-3 col-xs-3" for="absenceType">请假类型：</label>
            <div class="col-lg-4 col-md-4 col-xs-4">
                <select class="form-control" name="absence_type" id="absenceType" >
                    @if (!isset($data['depart_id'])||empty($data['depart_id']))
                    <option value=""></option>
                    @else
                    <option value="{{ $data['absence_type'] }}">{{ $data['absence_name'] }}</option>
                    @endif
                    <option value="1" >事假</option>
                    <option value="2" >年假</option>
                    <option value="3" >病假</option>
                    <option value="4" >带薪病假</option>
                    <option value="5" >婚假</option>
                    <option value="6" >丧假</option>
                    <option value="7" >产假</option>
                    <option value="8" >陪产假</option>
                    <option value="9" >产检假</option>
                    <option value="10" >流产假</option>
                </select>
            </div>
        </div>
        <div class="row ">
            <label   for="length"   class="control-label col-lg-3 col-md-3 col-xs-3" >请假天数：</label>
            <div class="col-lg-4 col-md-4 col-xs-4">
                <input   readonly type="text" class="form-control " placeholder="" name="length" id='length'  value="{{ $data['length'] or '' }}" >
            </div>
        </div>
        <div class="row">
            <label for="create_reason" class="control-label col-lg-3 col-md-3 col-xs-3">修改原因：</label>
            <div class="col-lg-4 col-md-4 col-xs-4">
                <textarea class="form-control" rows="6" placeholder="请填写修改原因" id="create_reason"  name="create_reason"></textarea>
            </div>
        </div>
         <div class="row ">
            <div class="col-lg-10 col-md-10 col-xs-10">
                <button  formId="editFrom" class="btn btn-primary add_new submit_btn" id="editSubmit" type="submit" data-toggle="modal" data-target="#cateModal">保存</button>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
    </div>
<script>
    $('#editSubmit').click(function(){
        var myForm = $('#editFrom').serializeArray();
        $.post('/hr/leave/ajax_leave_update', myForm, function (ret) {
            show_message(ret.code,ret);
        }, 'json');

    });
    $(function(){
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // 获取已激活的标签页的名称
            var activeTab = $(e.target).text();
            // 获取前一个激活的标签页的名称
            var previousTab = $(e.relatedTarget).text();
            $(".active-tab span").html(activeTab);
            $(".previous-tab span").html(previousTab);
        });
    });
    //时间处理
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true}).on('changeDate', function (e) {
//请求ajax
    });
    $(".change_length").change(function(){
        var myForm = $('#editFrom').serializeArray();
        myForm.push({name:'calculate',value:1});
        $.post('/hr/leave/ajax_leave_update', myForm, function (ret) {
            if(ret.data && ret.data.length){
                $('#length').val(ret.data.length);
            }
        }, 'json');
    });

</script>
@endsection