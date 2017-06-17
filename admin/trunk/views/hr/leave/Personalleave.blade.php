@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">
    .token-input-token,.token-input-input-token{line-height: 23px !important;}
</style>

<div class="panel">
    <div class="panel-body">

    <div id="page-content" class="panel">
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li  role="presentation"><a href="/hr/leave/leave_home" class="application_num">请假首页</a></li>
            <li class="active" role="presentation" ><a href="/hr/leave/personal_leave">个人请假</a></li>
        </ul>
    </div>
     <div class="form-container">
        <form id="searchForm" class="form-horizontal" role="">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <input type="text" class="form-control user_id"  name="user_id"/>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <a   class="btn btn-default query_btn"  type="button"><span class="glyphicon glyphicon-search"></span>搜索</a>
                </div>
            </div>
        </form>
        <div class="text-center">
            <h4>可用假期查询结果</h4>
        </div>
         <div class="table-container">
            <table class="table-hovered table table-bordered" style="font-size:13px;">
                <thead>
                <tr>
                    <th>类型</th>
                    <th class="text-center">转正限制</th>
                    <th class="text-center col-xs-3">休假天数</th>
                    <th class="text-center col-xs-2">薪资计算</th>
                    <th class="text-center">可用</th>
                    <th class="text-center">已使用</th>
                    <th class="text-center">流程中</th>
                    <th class="text-center">可申请</th>
                </tr>
                </thead>
                <tbody id="show">
                    <tr><td colspan='8' class='text-center'>初始化</td></tr>
                </tbody>
            </table>
        </div>
       </div>
     </div>
    </div>
<script>
    //search
    $('.query_btn').click(function() {
        var myForm = $('#searchForm').serializeArray(),
            str;
        $.post('/hr/leave/ajax_personal_leave', myForm, function (ret) {
            $("#show").empty();
            if (ret.code == 200) {
                $("#show").empty();
                $.each(ret.data, function (i, val) {
                    if( !val.value )val.value=0;
                    if( !val.limit )val.limit=0;
                    if( !val.leave_instruction )val.leave_instruction=0;
                    if( !val.salary_instruction )val.salary_instruction=0;
                    if( !val.all )val.all=0;
                    if( !val.used )val.used=0;
                    if( !val.going )val.going=0;
                    if( !val.usable )val.usable=0;
                    str += '<tr>'+
                        '<td >' + val.value + '</td>' +
                        '<td>' +  val.limit + '</td>' +
                        '<td>' +  val.leave_instruction + '</td>' +
                        '<td>' +  val.salary_instruction + '</td>' +
                        '<td>' +  val.all + '</td>' +
                        '<td>' +  val.used  + '</td>' +
                        '<td>' +  val.going +'</td>' +
                        '<td>' +  val.usable + '</td>' +
                        '</tr>';
                });
            }
            $('#show').html(str);
        }, 'json');
    });
    //tokeninput
    $('.user_id').tokenInput("/structure/depart/ajax_search_name",{
        queryParam:'search',tokenValue:'user_id',tokenLimit: 1,onAdd:function(ret) {
            $('.user_id').val(ret['user_id']);
        }
    });
    //格式化
    var str = window.location.pathname;
    $.each($('.list-group-item'),function(){
        console.log(str);
        if($(this).attr('href')==str){
            $(this).addClass('active');
        }
    })
</script>
@endsection
