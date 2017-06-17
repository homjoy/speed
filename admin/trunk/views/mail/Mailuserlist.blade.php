@extends('layouts.master')
@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>
<style type="text/css">
    .search_btn, .export_btn, .add_btn, .delete_btn, .edit_btn {
        margin-left: 5px;
        float: right;
    }
</style>
<div class="panel" >
    <div class="panel-body" >

    <!-- Table -->
    <div class="row">

        <div class="col-xs-5">
            <textarea name="mail" id="multiselect_to" cols="30" rows="20"  class="form-control" size="15">@if (!empty($data))  @foreach ($data as $t) {{ $t['user_mail'] }} @endforeach @endif</textarea>
        </div>
    </div>
    <div class="row"><div class="col-xs-12 text-center"><input type="hidden" value="{{ $search_params['group_id'] }}" id="group_id" name="group_id"/><input type="button" value="提交" class="btn btn-success submit_email_group"/></div></div>


</div>
</div>

<!--</div>-->
<script type="text/javascript" src="/static/js/multiselect.js"></script>    
<script>
$(function () {
    $('.submit_email_group').click(function () {
        var group_id = "{{ $search_params['group_id'] }}";
        var arr = $("#multiselect_to").val();
        $('.submit_email_group').attr('disabled',true);
        $.ajax({
            url: "/mail/ajax_mail_user_edit",
            method: "POST",
            data: {group_id: group_id, mail: arr},
            dataType: "json",
            success: function (msg) {
                $('.submit_email_group').removeAttr('disabled');
                if (msg.code == 200) {
                    show_message(msg.code, msg);
                    setTimeout("location.reload()", 1500);
                } else {
                    var ret = [];
                    ret['error_msg'] = msg['error_msg'];
                    ret['error_detail'] = '';//msg['error_msg'];

                    show_message(msg.code, ret);
                }
            }
        });
    })

//        $('#multiselect').multiselect({
//            search: {
//                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
//            }
//        });
//        $('#sub_leader_id').tokenInput("/mail/ajax_search_all_name",{
//            queryParam:'search' ,tokenLimit: 1,onAdd:function(ret) {
//                var html ="<option  value='"+ret.mail+"'>"+ret.mail+"</option>"
//                $('#multiselect').append(html);
//            }
//        });
})
</script>
@endsection