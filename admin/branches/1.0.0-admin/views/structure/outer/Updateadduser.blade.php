
<!--数据的修改或添加  里面要有连动的框 要有许多字段填写框 要能把老数据置灰 -->
@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css">

<link rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/js/tokeninputspeed.js"></script>
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="/static/css/bootstrap-select.css">
<script src="/static/js/bootstrap-select.js"></script>
<style type="text/css">
    .submit_btn{
        margin-left: 5px;
        float: right;
    }
    .selectpicker{
        display: none;
    }
    .password_notice{
        margin:0 auto;text-align: center ;
    }
    .token-input-token,.token-input-input-token{line-height: 23px !important;}
    .bootstrap-select ul{max-height: 270px !important;}
</style>

<div class="panel" >
    <div class="panel-body" >
        <ul id="myTab" class="nav nav-tabs">
    <li class="active"><a href="#user" data-toggle="tab">基本信息</a></li>

</ul>

<div id="myTabContent" class="tab-content">
<div class="tab-pane fade in active" id="user">
    <p></p>
    <form  id="userForm" class="form-inline  form-horizontal" role="form" >
        <div class="row">
            <label class="control-label col-lg-3" for="">用户ID</label>
            <div class="col-lg-2">
                <input readonly type="text" class="form-control " placeholder="" name="out_user_id" id = 'out_user_id'value="{{ $data['out_user_id'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">类型</label>
            <div class="col-lg-2">
                <select    class="form-control" name="type" id="type" >
                    <option value="1" @if(isset($data['type']) && $data['type'] == '1') selected @endif>客服</option>
                    <option value="2" @if(isset($data['type']) && $data['type'] == '2') selected @endif>其他</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">员工编号</label>
            <div class="col-lg-2">
                <input   type="text"  class="form-control  " placeholder="" name="staff_id" id = 'staff_id'value="{{ $data['staff_id'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">性别</label>
            <div class="col-lg-2">
                <select    class="form-control" name="gender" id="gender" >
                    <option value="0" @if((isset($data['gender']) && $data['gender'] == '0') ||empty($data['gender'])) selected @endif>女</option>
                    <option value="1" @if(isset($data['gender']) && $data['gender'] == '1') selected @endif>男</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">入职时间</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control form_datetime" placeholder="" name="hire_time" id='hire_time' value="{{ $data['hire_time'] or '' }}">
            </div>
            <label    class="control-label col-lg-3" >转正时间</label>
            <div class="col-lg-2">
                <input     type="text" class="form-control form_datetime" placeholder="" name="positive_time" id='positive_time'  value="{{ $data['positive_time'] or '' }}" readonly>
            </div>

        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">标记</label>
            <div class="col-lg-2">
                <select     class="form-control" name="flag" id="flag" >
                    <option value="1" @if((isset($data['flag']) && $data['flag'] == '1')||empty($data['flag'])) selected @endif>实习</option>
                    <option value="2" @if(isset($data['flag']) && $data['flag'] == '2') selected @endif>试用</option>
                    <option value="3" @if(isset($data['flag']) && $data['flag'] == '3') selected @endif>正式</option>
                    <option value="4" @if(isset($data['flag']) && $data['flag'] == '4') selected @endif>申请离职</option>

                </select>
            </div>
            <label class="control-label col-lg-3" for="">在职状态</label>
            <div class="col-lg-2">
                <select    class="form-control" name="status" id="status" >
                    <option value="1" @if((isset($data['status']) && $data['status'] == '1')||empty($data['status'])) selected @endif>在职</option>
                    <option value="2" @if(isset($data['status']) && $data['status'] == '2') selected @endif>离职</option>
                    <option value="3" @if(isset($data['status']) && $data['status'] == '3') selected @endif>重新入职</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">名字拼音</label>
            <div class="col-lg-2">
                <input   type="text"   class="form-control " placeholder="" name="name_en"  id = 'name_en'value="{{ $data['name_en'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">中文名字</label>
            <div class="col-lg-2">
                <input    type="text"  class="form-control " placeholder="" name="name_cn" id = 'name_cn' value="{{ $data['name_cn'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">邮箱前缀</label>
            <div class="col-lg-2">
                <input   type="text"   class="form-control " placeholder="" name="mail" id = 'mail' value="{{ $data['mail'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">毕业时间</label>
            <div class="col-lg-2">
                <input  type="text" class="form-control form_datetime" placeholder=""id = 'graduation_time' name="graduation_time" value="{{ $data['graduation_time'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">邮箱后缀</label>
            <div class="col-lg-2">
                <select    class="form-control" name="mail_suffix" id="mail_suffix" >
                        <option value="kf.meilishuo.com" @if((isset($data['mail_suffix']) && $data['mail_suffix'] == 'kf.meilishuo.com')||empty($data['mail_suffix'])) selected @endif>kf.meilishuo.com</option>
                    <option value="meilishuo.com" @if((isset($data['mail_suffix']) && $data['mail_suffix'] == 'meilishuo.com')||empty($data['mail_suffix'])) selected @endif>meilishuo.com</option>
                </select>
            </div>
            <label class="control-label col-lg-3" for="">用户部门</label>
            <div class="col-lg-2">
            <select   class="form-control"    name="depart_id" id="depart_id" value="{{ $data['depart_id'] or '' }}">
                <option value=""></option>
            </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10">
                <button  formId="userForm" class="btn btn-primary add_new submit_btn" id="userSubmit" type="submit" data-toggle="modal" data-target="#cateModal">保存</button>
            </div>
        </div>
    </form>
</div>
</div>
</div>
    </div>
<!--</div>-->
<script>
    var userId =$('#out_user_id').val();
    var data = '{!! @json_encode($data) !!}';
    try{
        data = $.parseJSON(data);
    }catch(e){
        data = [];
    }

    $('#userSubmit').click(function(){
        var myForm = $('#userForm').serializeArray();
        var tpl = ['name_cn','mail','staff_id'];
        $.each(tpl, function(key, val) {
            if(!$('#'+val).val()){
                $('#'+val).css('border-color', '#007472');
            }
        });
//        myForm.push({name:'user_id',value:userId});
        if(!!userId){
            $.post('/structure/outer/ajax_update_user_info', myForm, function (ret) {
                show_message(ret.code,ret);
            }, 'json');

        }else{
            $.post('/structure/outer/ajax_add_user', myForm, function (ret) {
//                if(ret.data){
//                    userId =ret.data.user_id;
//                }
                show_message(ret.code,ret);

            }, 'json');
        }

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
//    var date = new Date();
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd',minView: 'month',autoclose:true}).on('changeDate', function (e) {
        var id = $(this).attr('id');
        if(id == 'hire_time'){
            var hireTime = new Date(e.date);
            var year = hireTime.getFullYear();
            var month = hireTime.getMonth() + 4; //month从0开始
            if(month > 12){
                month -= 12;
                year++;
            }
            month = month>= 10 ? month : '0' + month;
            var positive_time = [year,month,hireTime.getDate()].join('-');

            $('#positive_time').val(positive_time);
        }
    });



</script>
@endsection