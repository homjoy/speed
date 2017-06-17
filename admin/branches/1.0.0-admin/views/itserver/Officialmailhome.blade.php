
@extends('layouts.master')
@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<link rel="stylesheet" href="/static/css/validator.css">
<script type="text/javascript" src="/static/js/Validform_v5.3.2_min.js"></script>
<script src="/static/js/pagination.js"></script>
<style type="text/css">

    .ul_input{
        overflow: auto;
        height: 200px;
        border:1px solid #ddd;
    }
    .ul_input li{
        padding: 3px 20px;
    }
    .password_notice{
        margin:0 auto;text-align: center ;
    }
</style>
<div class="panel" >
    <div class="panel-body" >

    <form id="searchForm" class="form-inline  form-horizontal" method="POST" role="form" action="/itserver/official_mail_home">
        <div class="row">
            <div class="col-lg-4">
                <input type="text" id="search_mail" class="form-control" placeholder="按照邮箱前缀进行搜索" value="@if(!empty($search_params['mail_name'])){{$search_params['mail_name']}} @endif" name="mail_name"/>
            </div>
            <div class="col-lg-1">
                <input type="submit" value="搜索" class="btn btn-default">
            </div>
            <div class="col-lg-1">
                <button class="btn btn-primary add_new add_btn" type="button" id="mail_group_add">添加
                </button>
            </div>
        </div>
        <p></p>

        <div class="table-container">
            <table class="table-hover table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>
                        邮箱
                    </th>
                    <th>
                        状态
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(empty($data)){
                    echo '<tr><td class="text-center" colspan=5>未找到相匹配的数据</td></tr>';
                }else{
                    foreach($data as $key =>$v){

                        if(isset($v['status'])&&$v['status']==1){
                            $v['status'] = '有效';
                        }else{
                            $v['status'] = '无效';
                        }

                        echo <<<DOC
            <tr>
                <td>{$v['mail_id']}</td>
                <td>{$v['mail_name']}</td>
                <td>{$v['status']}</td>
                <td >
                    <a class="btn btn-sm  btn-warning update_mail_btn" data-yy="1"  data-mail-name="{$v['mail_name']}"
                       title="修改密码"><span class="glyphicon glyphicon-log-in rm-disable"></span>改密码
                    </a>
                    <a class="btn btn-sm  btn-danger  delete_btn" data-mail-name="{$v['mail_name']}"
                       title="删除邮件组"><span class="glyphicon glyphicon-remove rm-disable"></span>删除
                    </a>
                </td>
            </tr>
DOC;
                    }
                }

                ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-left">
        </div>
    </form>
</div>
    </div>
<!-- 添加模版 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">邮件信息</h4>
            </div>
            <div class="modal-body">
                <form  id="addForm" class="form-horizontal">
                    <div class="form-group">
                        <label for="mail_name" class="col-sm-2 control-label">邮箱前缀</label>
                        <div class="col-sm-10">
                            <input   type="text"   class="form-control form-input input-xlarge" ajaxurl="/itserver/ajax_check_mail" name="mail_name"  datatype="*" errormsg="不能为空" value="">
                            <span class="Validform_checktip"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="login_name" class="col-sm-2 control-label">手机号码</label>
                        <div class="col-sm-10">
                            <input   type="text"  class="form-control" name ='mobile'  placeholder="手机号码">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name_cn" class="col-sm-2 control-label">中文名</label>
                        <div class="col-sm-10">
                            <input   type="text"  class="form-control" name ='name_cn'  placeholder="中文名(不填写为默认信息)">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="password_notice" style="color: #d16b8e">
                    <p><span>亲，密码由IT系统自动生成，并以短信形式下发到用户手机</span></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="add_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 添加模版 -->
<!-- 修改模版 -->
<div class="modal fade" id="myUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">邮件信息</h4>
            </div>
            <div class="modal-body">
                <form  id="updateForm" class="form-horizontal">
                    <div class="form-group">
                        <label for="mail_name" class="col-sm-2 control-label">邮箱前缀</label>
                        <div class="col-sm-10">
                            <input  readonly type="text"  class="form-control" name ='mail_name' id="mail_name" placeholder="邮箱前缀">
                        </div>
                    </div>
                    <div style="display: none;form-group">
                        <label for="mail_pwd" class="col-sm-2 control-label">邮箱密码</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='mail_pwd' id="mail_pwd" placeholder="邮箱密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-2 control-label">手机号</label>
                        <div class="col-sm-10">
                            <input   type="text"  class="form-control mobileCheck" name ='mobile' id="updateMobile" placeholder="手机号">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="password_notice" style="color: #d16b8e">
                    <p><span>亲，密码由speed系统自动生成，并以短信形式下发到用户手机</span></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="update_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 修改模版 -->
<script src="/static/js/bootbox.min.js"></script>
<script>
    //分页
    var count = '{!! json_encode($count) !!}';
    try{
        count = $.parseJSON(count);
    }catch(e){
        count = [];
    }
    var page = '{!! json_encode($page) !!}';
    try{
        page = $.parseJSON(page);
    }catch(e){
        page = [];
    }
    $(".pagination-left").pagination({
        //总页数
        totalPage:count,
        //初始选中页
        currentPage:page,
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
        go: false //取消页面跳转
    }).on("switch",function(e,page){
        var email = $('#search_mail').val()
        location.href="/itserver/official_mail_home?page="+page+'&mail_name='+email;

    });
    $(function() {
        $("#addForm").Validform({
            tiptype:3,
            postonce:true,
            showAllError:true,
            ajaxPost:true
        });

    });
    //添加邮件组操作
    $('#mail_group_add').click(function(e) {
        e.stopPropagation();

        $('#myModalLabel').html('邮件列表');
        $('#myModal').modal('show');
    });
    $('.update_mail_btn').click(function(e) {

        e.stopPropagation();
        var _this=$(this);
        $('#mail_name').val(_this.attr('data-mail-name'));
        $('#myModalLabel').html('邮件列表');
        $.ajax({
            url: "/structure/user/ajax_get_personal_info",
            method: "POST",
            data: { mail:$('#mail_name').val()},
            dataType: "json",
            success:function(msg){
                if(msg.code==200){
                    $('.mobileCheck').val(msg.data.mobile);
                }
            }
        });
        $('#myUpdateModal').modal('show');
    });


     //保存数据
    $("#add_btn").click(function(){
        var myForm = $('#addForm').serializeArray();

        $('#add_btn').attr('disabled',true);
        $.ajax({
            url: "/itserver/ajax_create_official_mail",
            method: "POST",
            data: myForm,
            dataType: "json",
            success:function(msg){
                $('#add_btn').removeAttr('disabled');
                if(msg.code==200){
                    show_message(msg.code,msg);
                    $('#mail_group').val('');
                    $('#multiselect').text('');
                    setTimeout("location.reload()",1500);
                }else{
                    var ret =[];
                    ret['error_msg'] = msg['error_detail'];
                    show_message(msg.code,ret);
                }
            }
        });

    })
    var  office_mail =5;
    //保存数据
    $("#update_btn").click(function(){

        $('#update_btn').attr('disabled',true);
        $.ajax({
            url: "/itserver/ajax_update_pwd",
            method: "POST",
            data: {  login_name:$('#mail_name').val(),mobile:$('#updateMobile').val(),mail_pwd:$('#mail_pwd').val(),type:office_mail},
            dataType: "json",
            success:function(msg){
                $('#update_btn').removeAttr('disabled');
                if(msg.code==200){
                    show_message(msg.code,msg);
                }else{
                    var ret =[];
                    ret['error_msg'] = msg['error_detail'];
                    show_message(msg.code,ret);
                }
            }
        });

    })
    //删除邮件组
    $('.delete_btn').click(function(){

        var _this = $(this)
        bootbox.confirm('确认删除用户吗？', function (result) {
            if (result) {
                _this.attr('disabled',true);
                var id = _this.attr('data-mail-name');

                $.ajax({
                    url: "/itserver/ajax_delete_official_mail",
                    method: "POST",
                    data: {mail_name: _this.attr('data-mail-name')},
                    dataType: "json",
                    success: function (msg) {
                        _this.removeAttr('disabled');
                        if (msg.code == 200) {
                            $(_this).parent().parent().remove();
                            show_message(msg.code, msg);
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