@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>
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

<div class="panel-heading" style="overflow: hidden; line-height: 32px">
    <ol class="breadcrumb">
        <li class="active">操作账号</li>
    </ol>
    <p></p>
    <ul id="myTab" class="nav nav-tabs">
        <li class="active"><a href="#pwdEdit" data-toggle="tab">Mail密码修改</a></li>
    </ul>
    <div id="myTabContent" class="tab-content">

        <div class="tab-pane fade in active" id="pwdEdit">
            <p></p>
            <form  id="pwdForm" class="form-inline  form-horizontal" role="form" >
                <p></p>
                <div class="row">
                    <label class="control-label col-lg-3 col-md-3 col-sm-3" for="">用户名:</label>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input   id="mail_name" type="text" class="form-control " placeholder="邮箱前缀" name="mail_name"  >
                    </div>
                </div>
                <div class="row">
                    <label class="control-label col-lg-3 col-md-3 col-sm-3" for="">mail密码:</label>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input    type="text" class="form-control " placeholder="修改的密码" name="mail_pwd"  >
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10">
                        <button  formId="pwdForm" class="btn btn-primary add_new submit_btn " id="pwdSubmit" type="submit" data-toggle="modal" data-target="#cateModal">保存</button>
                    </div>
                </div>
            </form>
            <div class="password_notice" style="color: #d16b8e">
                <p>长度<span>不小于8位、不能有空格</span></p>
                <p>必须包含一个<span>大写字母、小写字母、数字、符号</span>[]~!@#$%^*=,._-之一</p>
                <p>不能<span>与用户名相同</span>、不能是<span>用户名的子集</span>、用户名也不能是<span>密码的子集(不区分大小写)</span></p>
                <p>举个例子：<span>Mls@me520, @mLis123, 88#MLS.com</span>, 仅供参考格式，勿参考内容！</p>
            </div>
        </div>
    </div>
</div>
<!--</div>-->
<script>


    $('#pwdSubmit').click(function(){
        var myForm = $('#pwdForm').serializeArray();
        $.post('/structure/user/ajax_update_mail_pwd', myForm, function (ret) {
            show_message(ret.code,ret);
        }, 'json');

    });
    $('#mail_name').tokenInput("/structure/depart/ajax_search_name",{
        queryParam:'search' ,tokenLimit: 1,onAdd:function(ret) {
            var mail_name = [ret];
            $.each(mail_name,function(index,u){
                $('#mail_name').val(u['mail']);

            });
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


</script>
@endsection