@extends('layouts.master')
@section('content')
<style type="text/css">
    .submit_btn,.cancel_btn{
        margin-left: 10px;
        float: right;
    }
</style>

<div class="panel" >
    <div class="panel-body" >
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li role="presentation"><a href="/structure/depart/depart_home" class="application_num">部门首页</a></li>
            <li class="active" role="presentation"> <a href="/structure/depart/application_edit">申请修改</a></li>
            <li role="presentation"><a href="/structure/depart/add_title" class="approval_num">添加职位</a></li>
            <li role="presentation" ><a href="/structure/depart/depart_leader_home">操作部门leader</a></li>
        </ul>
    <div id="">
            <form  id="searchForm" class="form-inline  form-horizontal" role="form" action="/structure/depart/ajax_title" method="post">
                    <div class="row">
                        <label for="title" class="col-xs-2 ">申请原因：</label>
                        <div class="col-xs-8">
                            <textarea class="form-control" rows="10" placeholder="请填写申请原因" id="title"  name="title"></textarea>
                        </div>
                    </div>
             </form>
             <div class="row">
                 <button  formId="searchForm" class="btn btn-default add_new cancel_btn" id="cancel" type="submit" data-toggle="modal" data-target="#cateModal">取消</button>
                 <button  formId="searchForm" class="btn btn-primary add_new submit_btn" id="submit" type="submit" data-toggle="modal" data-target="#cateModal">保存</button>
             </div>
        </div>
    </div>
    </div>
<script>
   $('#submit').click(function () {
       var url_back = '/structure/depart/ajax_request_db';
       var myForm = $('#searchForm').serializeArray();
       $.get(url_back, myForm, function(ret) {
           if (ret.code == 200) {
               show_message(ret.code,ret);
               window.location.href = '/structure/depart/depart_home_backup';
           } else {
               show_message(ret.code,ret);
           }
       },'json');

    });
   $('#cancel').click(function () {
       window.location.href = '/structure/depart/depart_home';
   });
   function hide_message() {
       $('#message-container').slideUp();
   }
</script>
@endsection