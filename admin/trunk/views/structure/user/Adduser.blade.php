
<!--数据的修改或添加  里面要有连动的框 要有许多字段填写框 要能把老数据置灰 -->
@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css">
<link rel="stylesheet" href="/static/bootstrap/css/bootstrap-datetimepicker.min.css">
<script src="/static/js/tokeninputspeed.js"></script>
<script src="/static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="/static/css/bootstrap-select.css">
<script src="/static/js/bootstrap-select.js"></script>
<link rel="stylesheet" href="/static/css/validator.css">
<script type="text/javascript" src="/static/js/Validform_v5.3.2_min.js"></script>
<style type="text/css">
    .btn_pre,.btn_next,.btn_success{
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
<div id= class="container">
<div class="page_1" id="user">
    <p></p>
    <form  id="userForm" class="form-inline  form-horizontal" role="form" >
        <div class="row">
            <label class="control-label col-lg-3" for="">用户ID</label>
            <div class="col-lg-2">
                <input readonly type="text" class="form-control " placeholder="" name="user_id" id = 'user_id'value="{{ $data['user_id'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">用户部门</label>

                <select    class="col-lg-2 selectpicker" data-live-search="true" name="depart_id" id="depart_id" value="{{ $data['depart_id'] or '' }}">
                    @if (!isset($data['depart_id'])||empty($data['depart_id']))
                    <option value=""></option>
                    @else
                    <option value="{{ $data['depart_id'] }}">{{ $data['depart_name'] }}</option>
                    @endif
                    @if (!empty($parentType))
                    @foreach ($parentType as $p)
                    <option value="{{ $p['depart_id'] }}">{{ $p['depart_name'] }}</option>
                    @endforeach
                    @endif
                </select>

        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">员工编号</label>
            <div class="col-lg-2">
                <input   type="text"  class="form-control" placeholder="" name="staff_id" id = 'staff_id' value="{{ $data['staff_id'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
            <label class="control-label col-lg-3" for="">性别</label>
            <div class="col-lg-2">
                <select    class="form-control" name="gender" id="gender" >
                    <option value="0" >女</option>
                    <option value="1" >男</option>
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
                    <option value="1" >实习</option>
                    <option value="2" >试用</option>
                    <option value="3"  selected >正式</option>
                    <option value="4" >申请离职</option>

                </select>
            </div>
            <label class="control-label col-lg-3" for="">在职状态</label>
            <div class="col-lg-2">
                <select    class="form-control" name="status" id="status" >
                    <option value="1"  selected>在职</option>
                    <option value="2" >离职</option>
                    <option value="3" >重新入职</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">名字拼音</label>
            <div class="col-lg-2">
                <input   type="text"   class="form-control "  placeholder="" name="name_en"  id = 'name_en'value="{{ $data['name_en'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
            <label class="control-label col-lg-3" for="">中文名字</label>
            <div class="col-lg-2">
                <input    type="text"  class="form-control "  placeholder="" name="name_cn" id = 'name_cn' value="{{ $data['name_cn'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
        </div>

        <div class="row">
            <label class="control-label col-lg-3" for="">用户角色</label>
            <div class="col-lg-2">
                <select class="form-control" name="job_role_id" id="job_role_id" value="{{ $data['job_role_id'] or '' }}">
                    @if (!isset($data['job_role_id'])||empty($data['job_role_id']))
                    <option value=""></option>
                    @else
                    <option value="{{ $data['job_role_id'] }}">{{ $data['job_role_name'] }}</option>
                    @endif
                    @if (!empty($roleInfo))
                    @foreach ($roleInfo as $p)
                    <option value="{{ $p['role_id'] }}">{{ $p['role_name'] }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <label class="control-label col-lg-3" for="">直属领导(旧)</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control " placeholder="" name="direct_leader" id ="direct_leader">
            </div>
        </div>

        <div class="row">
            <label class="control-label col-lg-3" for="">邮箱前缀</label>
            <div class="col-lg-2">
                <input   type="text"   class="form-control form-input input-xlarge" ajaxurl="/itserver/ajax_check_speed_mail" name="mail" id = 'mail' datatype="*" errormsg="不能为空" value="{{ $data['mail'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
            <label class="control-label col-lg-3" for="">毕业时间</label>
            <div class="col-lg-2">
                <input  type="text" class="form-control form_datetime " placeholder=""id = 'graduation_time' name="graduation_time" value="{{ $data['graduation_time'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
        </div>
        @if (!isset($data['user_id'])|| empty($data['user_id']))
        <div class="row">
            <label class="control-label col-lg-3" for="">手机</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control " placeholder="" datatype="m" id ="mobile" name="mobile" datatype="*" errormsg="手机号填写错误" value="{{ $data['mobile'] or '' }}">
                <span class="Validform_checktip"></span>
            </div>
            <label class="control-label col-lg-3" for="">生日</label>
            <div class="col-lg-2">
                <input type="text" class="form-control form_datetime form-input input-xlarge" placeholder=""  id ="birthday" name="birthday" value="{{ $data['birthday'] or '' }}"  errormsg="生日不允许为空" >
                <span class="Validform_checktip"></span>
            </div>
        </div>
        @endif
    </form>
</div>
<div class="page_2" style="display:none" id="work">
    <p></p>
    <form  id="workForm" class="form-inline  form-horizontal" role="form" >
        <div class="row">
            <label class="control-label col-lg-3" for="">MLSID</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="mls_id" value="{{ $data['mls_id'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">MLS昵称</label>
            <div class="col-lg-2">
                <input    type="text" class="form-control " placeholder="" name="mls_nickname"  value="{{ $data['mls_nickname'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">工位</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control " placeholder="" name="position" value="{{ $data['position'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">RedmineID</label>
            <div class="col-lg-2">
                <input    type="text" class="form-control " placeholder="" name="redmineid"  value="{{ $data['redmineid'] or '' }}">
            </div>

        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">其他工作信息</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="others_work" value="{{ $data['others_work'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">用户职位</label>
            <div class="col-lg-2">
                <select class="form-control" name="job_title_id" id="job_title_id">
                    <option value="0" @if(!isset($data['job_title_id'])||empty($data['job_title_id'])) selected @endif></option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">用户级别</label>
            <div class="col-lg-2">
                <select class="form-control" name="job_level_id" id="job_level_id">
                    <option value="0" @if(!isset($data['job_level_id']) ||empty($data['job_level_id'])) selected @endif></option>
                </select>
            </div>
            <label class="control-label col-lg-3" for="">业务归属公司</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="business_company_id" value="{{ $data['business_company_id'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">合同签定公司</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="contract_company_id" value="{{ $data['contract_company_id'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">银行卡号</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control " placeholder="" name="bank_card_number" value="{{ $data['bank_card_number'] or '' }}">
            </div>
        </div>
        <div class="row">
        <label class="control-label col-lg-3" for="">工作城市</label>
        <div class="col-lg-2">
            <input type="text" class="form-control " placeholder="" name="work_city" value="{{ $data['work_city'] or '' }}">
        </div>
        </div>
    </form>
</div>
<div class="page_3" style="display:none" id="personal">
    <p></p>
    <form  id="personalForm" class="form-inline  form-horizontal" role="form">
        <div class="row">

            <label class="control-label col-lg-3" for="">上衣颜色</label>
            <div class="col-lg-2">
                <select class="form-control" name="coat_color" id="coat_color">
                    <option value="" @if(empty($data['coat_color'])) selected @endif></option>
                    <option value="粉色" @if(isset($data['coat_color']) && $data['coat_color'] == '粉色') selected @endif>粉色</option>
                    <option value="黑色" @if(isset($data['coat_color']) && $data['coat_color'] == '黑色') selected @endif>黑色</option>
                    <option value="灰色" @if(isset($data['coat_color']) && $data['coat_color'] == '灰色') selected @endif>灰色</option>
                    <option value="电光蓝" @if(isset($data['coat_color']) && $data['coat_color'] == '电光蓝') selected @endif>电光蓝</option>
                </select>
            </div>
            <label class="control-label col-lg-3" for="">地址</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="address" value="{{ $data['address'] or '' }}">
            </div>

        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">座机</label>
            <div class="col-lg-2">
                <input    type="text" class="form-control " placeholder="" name="telephone" value="{{ $data['telephone'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">民族</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="nation" value="{{ $data['nation'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">其他私人信息</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="others_pinfo" value="{{ $data['others_pinfo'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">衣服尺寸</label>
            <div class="col-lg-2">
                <select    class="form-control" name="coat_size" id="coat_size" value="{{$data['coat_size'] or '' }}">
                    <option value="" @if(!isset($data['pants_size'])|| empty($data['coat_size'])) selected @endif></option>
                    <option value="女SM" @if(isset($data['coat_size']) && $data['coat_size'] == '女SM')) selected @endif>女SM</option>
                    <option value="女M" @if(isset($data['coat_size']) && $data['coat_size'] == '女M') selected @endif>女M</option>
                    <option value="女L" @if(isset($data['coat_size']) && $data['coat_size'] == '女L') selected @endif>女L</option>
                    <option value="女XL" @if(isset($data['coat_size']) && $data['coat_size'] == '女XL') selected @endif>女XL</option>
                    <option value="男SM" @if(isset($data['coat_size']) && $data['coat_size'] == '男SM') selected @endif>男SM</option>
                    <option value="男M" @if(isset($data['coat_size']) && $data['coat_size'] == '男M') selected @endif>男M</option>
                    <option value="男L" @if(isset($data['coat_size']) && $data['coat_size'] == '男L') selected @endif>男L</option>
                    <option value="男XL" @if(isset($data['coat_size']) && $data['coat_size'] == '男XL') selected @endif>男XL</option>
                    <option value="男XXL" @if(isset($data['coat_size']) && $data['coat_size'] == '男XXL') selected @endif>男XXL</option>
                    <option value="男XXXL" @if(isset($data['coat_size']) && $data['coat_size'] == '男XXXL') selected @endif>男XXXL</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">QQ</label>
            <div class="col-lg-2">
                <input    type="text" class="form-control " placeholder="" name="qq" value="{{ $data['qq'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">备用手机</label>
            <div class="col-lg-2">
                <input type="text" class="form-control " placeholder="" name="mobile_another" value="{{ $data['mobile_another'] or '' }}">
            </div>
        </div>
        <div class="row">
            <label class="control-label col-lg-3" for="">下装尺寸</label>
            <div class="col-lg-2">
                <select class="form-control" name="pants_size" id="pants_size" >
                    @if (!isset($data['pants_size'])||empty($data['pants_size']))
                    <option value=""></option>
                    @else
                    <option value="{{ $data['pants_size'] }}">{{ $data['pants_size'] }}</option>
                    @endif
                    @for ($i = 27; $i < 36; $i++)
                    <option value="{{ $i }}">{{$i}}</option>
                    @endfor
                </select>
            </div>
            <label class="control-label col-lg-3" for="">鞋子号码</label>
            <div class="col-lg-2">
                <select class="form-control" name="shoes_size" id="shoes_size">
                    @if (!isset($data['shoes_size'])||empty($data['shoes_size']))
                    <option value=""></option>
                    @else
                    <option value="{{ $data['shoes_size'] }}">{{ $data['shoes_size'] }}</option>
                    @endif
                    @for ($i = 35; $i < 46; $i++)
                    <option value="{{ $i }}">{{$i}}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="row">
            @if (isset($data['user_id'])|| !empty($data['user_id']))
            <label class="control-label col-lg-3" for="">生日</label>
            <div class="col-lg-2">
                <input    type="text" class="form-control form_datetime" placeholder="" name="birthday" value="{{ $data['birthday'] or '' }}">
            </div>
            <label class="control-label col-lg-3" for="">手机</label>
            <div class="col-lg-2">
                <input   type="text" class="form-control " placeholder="" name="mobile" value="{{ $data['mobile'] or '' }}">
            </div>
            @endif
        </div>
    </form>
</div>
    <div class="page_4" style="display:none" id="privacy">
        <p></p>
        <form  id="privacyForm" class="form-inline  form-horizontal" role="form" >
            <div class="row">
                <label class="control-label col-lg-3" for="">户口</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="hukou" value="{{ $data['hukou'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">学历</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="education" value="{{ $data['education'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">毕业院校</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="school" value="{{ $data['school'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">专业</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="speciality"  value="{{ $data['speciality'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">前单位</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="last_work" value="{{ $data['last_work'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">紧急联系人</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="emergency_person" value="{{ $data['emergency_person'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">合同开始时间</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control form_datetime" placeholder="" name="contract_start_time" value="{{ $data['contract_start_time'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">合同结束时间</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control form_datetime" placeholder="" name="contract_end_time" value="{{ $data['contract_end_time'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">私人邮箱</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="personal_mail" value="{{ $data['personal_mail'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">结婚时间</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control form_datetime" placeholder="" name="marry_time" value="{{ $data['marry_time'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">身份证号</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="id_number" value="{{ $data['id_number'] or '' }}">
                </div>
                <label class="control-label col-lg-3" for="">紧急联系人手机</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control " placeholder="" name="emergency_phone" value="{{ $data['emergency_phone'] or '' }}">
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">子女生日</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control form_datetime" placeholder="" name="children_birthday" value="{{ $data['children_birthday'] or '' }}">
                </div>


            </div>
        </form>
    </div>
    <div class="page_5"style="display:none" id="allAuth">
        <p></p>

        <form  id="allAuthForm" class="form-inline  form-horizontal" role="form" >
            <p></p>
            <div class="row">
                <label class="control-label col-lg-3" for=""></label>
                <div class="col-lg-2">
                    <input type="radio" name="authRadio" value="1" checked="checked"/>注册&nbsp;
    <!--                <input type="radio" name="authRadio" value="2" />注销&nbsp;-->
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">WIFI(仅北京)</label>
                <div class="col-lg-2">
                    <select class="form-control" name="wifi_status" id="wifi_status">
                        <option value=0>不申请</option>
                        <option value=1 selected>申请</option>
                    </select>
                </div>
                <label class="control-label col-lg-3" for="">VPN</label>
                <div class="col-lg-2">
                    <select class="form-control" name="vpn_status" id="vpn_status">
                        <option value=0 >不申请</option>
                        <option value=1 selected>申请</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">Mail</label>
                <div class="col-lg-2">
                    <select class="form-control" name="mail_status" id="mail_status">
                        <option value=0 >不申请</option>
                        <option value=1 selected>申请</option>
                    </select>
                </div>
                <label class="control-label col-lg-3" for="">Redmine&Svn</label>
                <div class="col-lg-2">
                    <select class="form-control" name="redmine_status" id="redmine_status">
                        <option value=0>不申请</option>
                        <option value=1 selected>申请</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3" for="">电脑密码</label>
                <div class="col-lg-2">
                    <select class="form-control" name="computer_status" id="computer_status">
                        <option value=0>不申请</option>
                        <option value=1 selected>申请</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
        <div class="btns form-group text-center">
            <button type="button"  class="btn btn-primary  btn_pre" style="display: none;">上一步</button>
            <button type="button"  class="btn btn-primary btn_next" >下一步</button>
            <button type="button"  class="btn btn_success" style="display: none;">完成注册</button>
        </div>
    </div>
</div>
    </div>
<script>

    var userId =$('#user_id').val();
    var data = '{!! @json_encode($data) !!}';
    try{
        data = $.parseJSON(data);
    }catch(e){
        data = [];
    }
    var  btn_next = $(".btn_next")
        ,btn_pre = $(".btn_pre")
        ,btn_success = $(".btn_success")
        ,i = 1;

    $(function() {
        var time = new Date();
        var year = time.getFullYear();
        var month = time.getMonth()+1; //month从0开始
        if(month > 12){
            month -= 12;
            year++;
        }
        month = month>= 10 ? month : '0' + month;
        var  day = time.getDate()>=10?  time.getDate() : '0' +  time.getDate();
        document.getElementById("graduation_time").value = [year,month,day].join('-');
        document.getElementById("birthday").value = [year,month,day].join('-');



        $("#userForm").Validform({
                tiptype:3,
                postonce:true,
                showAllError:true,
                ajaxPost:true
           });

        var subType = <?php echo json_encode($subType) ?>;
        var departId = $("#depart_id").val();
        if (subType[departId]) {
            var sub_type = subType[departId];
            $.each(sub_type, function(i,val) {
                var option = $("<option>").val(val['job_title_id']).text(val['job_title_name']);
                $("#job_title_id").append(option);
            });
        }
        $("#depart_id").change(function(){
            $("#job_title_id").empty();
            var parent_id = $("#depart_id").val();
            var option = $("<option>").val("0").text("请选择");
            $("#job_title_id").append(option);

            if (subType[parent_id]) {
                var sub_type = subType[parent_id];
                $.each(sub_type, function(i,val) {
                    var option = $("<option>").val(val['job_title_id']).text(val['job_title_name']);
                    $("#job_title_id").append(option);
                });
            }

        });
        if(!!data.direct_leader && !!data.direct_name ){
                $('#direct_leader').tokenInput("/structure/depart/ajax_search_name",{queryParam:'search',tokenValue:'user_id',tokenLimit: 1}).tokenInput("add", {user_id:data.direct_leader , name: data.direct_name});
        }else{
            $('#direct_leader').tokenInput("/structure/depart/ajax_search_name",{
                queryParam:'search',tokenValue:'user_id',tokenLimit: 1,onAdd:function(ret) {
                    $('#direct_leader').val(ret['user_id']);
                }
            });
        }


    });

    btn_next.on("click",function(){
//        debugger;
        var  f = 400;
        if(i==1){
//            debugger;
            var userForm = $('#userForm').serializeArray();


            userForm.push({name:'user_id',value:userId});
            if(!!userId){
                $.ajax({
                    method: "POST",
                    url: "/structure/user/ajax_update_user_info",
                    data: userForm,
                    dataType: "json",
                    async: false,
                    success: function (msg) {
                        f=msg.code;
                        show_message(msg.code,msg);
                    }
                });
            }else{
                $.ajax({
                    method: "POST",
                    url: "/structure/user/ajax_add_user",
                    data: userForm,
                    dataType: "json",
                    async: false,
                    success: function (msg) {
                        if(msg.data){
                            userId =msg.data.user_id;
                        }

                        f=msg.code;
                        show_message(msg.code,msg);
                    }
                });
            }

        }
        if(i==2){
            var workForm = $('#workForm').serializeArray();
            workForm.push({name:'user_id',value:userId});
            $.ajax({
                method: "POST",
                url: "/structure/user/ajax_update_work_info",
                data: workForm,
                dataType: "json",
                async: false,
                success: function (ret) {
                    f = ret.code;
                    show_message(ret.code,ret);
                }
            });

        }
        if(i==3){
            var personalForm = $('#personalForm').serializeArray();
            personalForm.push({name:'user_id',value:userId});
            $.ajax({
                method: "POST",
                url: "/structure/user/ajax_update_personal_info",
                data: personalForm,
                dataType: "json",
                async: false,
                success: function (ret) {
                    f = ret.code;
                    show_message(ret.code,ret);
                }
                });

        }
        if(i==4){
            var privacyForm = $('#privacyForm').serializeArray();
            privacyForm.push({name:'user_id',value:userId});
            $.ajax({
                method: "POST",
                url: "/structure/user/ajax_update_privacy_info",
                data: privacyForm,
                dataType: "json",
                async: false,
                success: function (ret) {
                    f = ret.code;
                    show_message(ret.code,ret);
                }
                });
        }
//        debugger;
        if(f!=200 && f!='200' ){
            return;
        }
        //上一页下一页处理
        i = i + 1;
        var tpl = [1,2,3,4,5];
//        debugger;
        $.each(tpl, function(key, val) {
            if(val ==i){
                $(".page_"+i).show();
            }else{
                $(".page_"+val).hide();
            }
        });
        if(i==5){
            btn_next.hide();
            btn_success.show();
        }else{
            btn_pre.show();
        }

    });
    btn_pre.on("click",function(){
        i = i - 1;
        var tpl = [1,2,3,4,5];
        $.each(tpl, function(key, val) {
            if(val ==i){
                $(".page_"+i).show();
            }else{
                $(".page_"+val).hide();
            }
        });
        if(i==1){
            btn_pre.hide();
        }else{
            btn_success.hide();
            btn_next.show();

        }

    });
    btn_success.on("click",function(){
       var myForm = $('#allAuthForm').serializeArray(),
        rad =$('input[name=authRadio]:checked').val();

        myForm.push({name:'user_id',value:userId});
        //找到单选按钮值1，2
        if( rad==1){
            $.post('/structure/user/ajax_get_all_auth', myForm, function (ret) {
                if(ret.code==200){

                    var data =ret.data;
                    if(data.msg){
                        $('#message-container').html(data.msg);
                        $('#message-container').removeClass('alert-danger');
                        $('#message-container').addClass('alert-success');
                        $('#message-container').slideDown();
                        setTimeout(hide_message, 3500);
                     }else{
                            show_message(ret.code,ret);
                     }

                }else{
                    show_message(ret.code,ret);
                }

            }, 'json');
        }

    });

    //时间处理

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


    $("#name_cn").change(function(){
            $.ajax({
                method: "POST",
                url: "/structure/user/ajax_auto_name",

                data: {name_cn: $("#name_cn").val()},
                dataType: "json",
                async: false,
                success: function (msg) {
                    if (msg.code == 200) {
                        $("#name_en").val(msg.data.name_en);
                        $("#mail").val(msg.data.mail);
                    }
                }
            });

    });

    $('#staff_id').keydown(function(event){
        var this_value = event.which
        //只允许字母A-Z的应用
        if(this_value >=65 && this_value <=90){
            $.ajax({
                method: "POST",
                url: '/structure/user/ajax_get_staff_id',
                async:true	,
                dataType:'json',
                data: { staff_id_prefix: this_value},
                success:function(msg){
                    if(msg.code==200){
                        var staff_id_new = msg.data
                        $('#staff_id').val(staff_id_new)
                    }
                }
            })
        }
    })

</script>
@endsection