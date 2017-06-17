
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
    .btn-disabled {
        background-color: #c5c5c5;
    }
</style>
<div class="panel" >
    <div class="panel-body" >
    <form id="searchForm" class="form-inline  form-horizontal" method="POST" role="form" action="/itserver/wifi">
        <div class="row">
            <div class="col-lg-4">
                <input type="text" id="login_name" class="form-control" placeholder="按照用户名进行搜索" value="@if(!empty($search_params['login_name'])){{$search_params['login_name']}} @endif" name="login_name"/>
            </div>
            <div class="col-lg-1">
                <input type="submit" value="搜索" class="btn btn-default">
            </div>
        </div>
        <p></p>

        <div class="table-container">
            <table class="table-hover table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>
                        wifi账号
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
                    echo '<tr><td class="text-center" colspan=4>未找到相匹配的数据</td></tr>';
                }else{
                    foreach($data as $key =>$v){

                        if(isset($v['status'])&&$v['status']==1){
                            $v['status'] = '有效';
                            echo <<<DOC
            <tr>
                <td>{$v['id']}</td>
                <td>{$v['login_name']}</td>
                <td>{$v['status']}</td>
                <td >
                    <a class="btn btn-sm  btn-warning update_wifi_btn" data-yy="1" data-type={$v['type']} data-login_name="{$v['login_name']}"
                       title="修改密码"><span class="glyphicon glyphicon-log-in rm-disable"></span>改密码
                    </a
                </td>
            </tr>
DOC;
                        }else{
                            $v['status'] = '无效';
                            echo <<<DOC
            <tr>
                <td>{$v['id']}</td>
                <td>{$v['login_name']}</td>
                <td>{$v['status']}</td>
                <td >
                    <a class="btn btn-sm btn-disabled"
                       title="禁用"><span class="glyphicon glyphicon-log-in rm-disable"></span>已禁用
                    </a
                </td>
            </tr>
DOC;

                        }


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
                        <label for="login_name" class="col-sm-2 control-label">WIFI</label>
                        <div class="col-sm-10">
                            <input  readonly type="text"  class="form-control" name ='login_name' id="update_login"  placeholder="(默认)邮箱前缀">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-2 control-label">手机号</label>
                        <div class="col-sm-10">
                            <input   type="text"  class="form-control mobileCheck" name ='mobile'  placeholder="手机号">
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
        var email = $('#login_name').val()
        location.href="/itserver/wifi?page="+page+'&login_name='+email;

    });
var t;
    $('.update_wifi_btn').click(function(e) {

        e.stopPropagation();
        var _this=$(this);
        $('#update_login').val(_this.attr('data-login_name'));
        t = _this.attr('data-type');
        $('#myModalLabel').html('wifi列表');
        $.ajax({
            url: "/structure/user/ajax_get_personal_info",
            method: "POST",
            data: { mail:$('#update_login').val()},
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
    $("#update_btn").click(function(){

        $('#update_btn').attr('disabled',true);
        $.ajax({
            url: "/itserver/ajax_update_pwd",
            method: "POST",
            data: { login_name:$('#update_login').val(),type:t},
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
</script>
@endsection