@extends('layouts.master')
@section('content')
<style type="text/css">
    .online_btn{
        margin-left: 10px;
        float: right;
    }
    .token-input-token,.token-input-input-token{line-height: 23px !important;}
</style>
<link rel="stylesheet" href="/static/css/easyTree.css">
<script src="/static/js/easyTree.js"></script>

<link rel="stylesheet" href="/static/css/tokeninput.css">
<script src="/static/js/tokeninputspeed.js"></script>

<div class="panel" >
    <div class="panel-body" >
        <div id="page-content" class="panel">
            <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
                <li class="active" role="presentation"><a href="/structure/depart/depart_home_backup" class="application_num">部门首页</a></li>
                <li role="presentation"> <a href="/structure/depart/application_edit">申请修改</a></li>
                <li role="presentation"><a href="/structure/depart/add_title" class="approval_num">添加职位</a></li>
                <li role="presentation" ><a href="/structure/depart/depart_leader_home">操作部门leader</a></li>
            </ul>
        </div>
           <div class="easy-tree">
               <?php
               if(empty($data)) {
                   echo  '<ul><li></li></ul>';
               }else{
                   $dept = $data;
                   function dept_ul_li($dept) {
                       $dept_str = '<ul >';
                       foreach ($dept as $key => $val) {
                           @$dept_str .= "<li data-depart_id='". $val['depart_id'] ."'data-depart_name='". $val['depart_name'] ."'>" . $val['depart_name'];
                           if ($val['child']) {
                               $dept_str .= dept_ul_li($val['child']);
                           }
                       }
                       $dept_str .= '</li></ul>';
                       return $dept_str;
                   }
                   $ul_li = dept_ul_li($dept);
                   echo $ul_li;
               }
               ?>

           </div>
            <div >
                <button type="button" class="btn btn-primary online_btn" id="online_btn">提交线上</button>
            </div>
       </div>

</div>
<!-- 添加模版 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">添加部门</h4>
            </div>
            <div class="modal-body">
                <form  id="addForm" class="form-horizontal">
                    <div class="form-group">
                        <label for="depart_id" class="col-sm-2 control-label">部门名称</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control" name ='depart_name' id="depart_name" placeholder="部门名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="depart_info" class="col-sm-2 control-label">部门信息</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name ='depart_info' id="depart_info" placeholder="部门信息">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="memo" class="col-sm-2 control-label">部门备注</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name ='memo' id="memo" placeholder="部门备注">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="leader_id" class="col-sm-2 control-label">部门领导</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name ='leader_id'   id="leader_id" placeholder="部门负责人">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="role_id" class="col-sm-2 control-label">部门角色</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="role_id" id="role_id" ">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sub_leader_id" class="col-sm-2 control-label">代理领导</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control " name ='sub_leader_id'  id="sub_leader_id" placeholder="代理领导">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="virtual_leader_id" class="col-sm-2 control-label">汇报关系</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control "  name ='virtual_leader_id'   id="virtual_leader_id" placeholder="虚拟关系">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="virtual_role_id" class="col-sm-2 control-label">汇报角色</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="virtual_role_id" id="virtual_role_id" >
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_id" class="col-sm-2 control-label">部门成员</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"  id="user_id" placeholder="部门成员">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="add_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 添加模版 -->
<!-- 编辑模版 -->
<div  class="modal fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myEditModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myEditModalLabel">修改部门</h4>
            </div>
            <div class="modal-body">
                <form  id="editForm" class="form-horizontal">
                    <div class="form-group">
                        <label for="e_depart_id" class="col-sm-2 control-label">部门ID</label>
                        <div class="col-sm-10">
                            <input readonly type="text" class="form-control" name ='depart_id' id="e_depart_id" placeholder="部门名称" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_depart_name" class="col-sm-2 control-label">部门名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name ='depart_name' id="e_depart_name" placeholder="部门名称" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_depart_info" class="col-sm-2 control-label">部门信息</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"  name ='depart_info'   id="e_depart_info" placeholder="部门信息">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_leader_id" class="col-sm-2 control-label">部门领导</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control " name ='leader_id' id="e_leader_id" placeholder="部门负责人">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_role_id" class="col-sm-2 control-label">部门角色</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="role_id" id="e_role_id" ">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_parent_id" class="col-sm-2 control-label">上级部门</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control " name ='parent_id'  id="e_parent_id" placeholder="上级部门">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_sub_leader_id" class="col-sm-2 control-label">代理领导</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control " name ='sub_leader_id' id="e_sub_leader_id" placeholder="代理领导">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_virtual_leader_id" class="col-sm-2 control-label">汇报关系</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name ='virtual_leader_id' id="e_virtual_leader_id" placeholder="虚拟关系">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_virtual_role_id" class="col-sm-2 control-label">汇报角色</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="virtual_role_id" id="e_virtual_role_id" >
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="e_user_id" class="col-sm-2 control-label">部门成员</label>
                        <div class="col-sm-10">
                            <input   type="text" class="form-control"  name ='user_id' id="e_user_id" placeholder="部门成员">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="edit_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 编辑模版 -->
<!-- 删除 -->
<div class="modal fade" id="myDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myDeleteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myDeleteModalLabel">删除部门</h4>
            </div>
            <form  id="deleteForm" class="form-horizontal">
                <div class="modal-body">
                    <h6 class="modal-title" >亲，真的要删除部门吗？</h6>
                </div>
             </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="delete_btn">保存</button>
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        function init() {
            $('.easy-tree').EasyTree({
                addable: true,
                editable: true,
                deletable: true,
                selectable: false,
                speed_show_btn: false,
                speed_edit_btn: true,
                speed_delete_btn: true
            });
        }
        window.onload = init();
    })(jQuery);

    var useridArry = [];
    $('#user_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,onAdd:function(ret) {
            var user_id = [ret];
            $.each(user_id,function(index,u){
                useridArry.push(u['user_id']);
            });
        }
    });
    $('#leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,onAdd:function(ret) {
            $('#leader_id').val(ret['user_id']);
        }
    });
    $('#sub_leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,onAdd:function(ret) {
            $('#sub_leader_id').val(ret['user_id']);
        }
    });
    $('#virtual_leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,onAdd:function(ret) {
            $('#virtual_leader_id').val(ret['user_id']);
        }
    });
    // 修改
    $('#e_user_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search',tokenValue:'user_id',onAdd:function(ret) {
        }
    });
    $('#e_leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,tokenValue:'user_id',onAdd:function(ret) {
        }
    });
    $('#e_sub_leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,tokenValue:'user_id',onAdd:function(ret) {
        }
    });
    $('#e_virtual_leader_id').tokenInput("/structure/depart/ajax_search_name?type=1",{
        queryParam:'search' ,tokenLimit: 1,tokenValue:'user_id',onAdd:function(ret) {
        }
    });
    $('#e_parent_id').tokenInput("/structure/depart/ajax_search_depart?type=1",{
        queryParam:'depart_name',tokenLimit: 1, tokenValue:'depart_id',onAdd:function(ret) {
        }
    });

    var form_tpl = ['leader_id','memo','parent_id','virtual_role_id','role_id','depart_id','user_id','depart_info','virtual_leader_id','sub_leader_id'];
    //添加
    var departId;
    $('.speed_add_model').click(function(e) {
        e.stopPropagation();
        $.post('structure/depart/ajax_get_role',{}, function(ret) {
            if(ret.code==200){
                var html;
                $.each(ret.data,function(k,v){
                    html +='<option value="'+v.role_id+'">'+ v.role_name+'</option>';
                });
                $('#role_id').html(html);
                $('#virtual_role_id').html(html);
            }
        }, 'json');
        $.each(form_tpl, function(key, val) {
            $('#' + val).val('');
        });
        departId  = $(this).parent().parent().attr('data-depart_id');
        var  departName  = $(this).parent().parent().attr('data-depart_name');
        $('#myModalLabel').html('添加部门:上级部门'+departName);
        $('#myModal').modal('show');
    });
    //保存
    $('#add_btn').click(function() {
        var roleId = $("#role_id").val();
        var departName = $("#depart_name").val();
        if(!roleId ||!departName){
            $('#role_id').css('border-color', '#007472');
            $('#depart_name').css('border-color', '#007472');
            return ;
        }
        $('#myModal').modal('hide');
        var myForm = $('#addForm').serializeArray();
        myForm.push({name:'user_id',value:useridArry});
        myForm.push({name:'parent_id',value:departId});
        $.post('structure/depart/ajax_add_depart',myForm, function(ret) {
            if (ret.code == 200) {
                show_message(ret.code,ret);
                setTimeout("window.location.href = '/structure/depart/depart_home_backup'",1500);
            } else {
                show_message(ret.code,ret);
            }
        }, 'json');
    });

    //编辑
    $('.speed_edit_model').click(function(e) {
        e.stopPropagation();
        $.post('structure/depart/ajax_get_role',{}, function(ret) {
            if(ret.code==200){
                var html;
                $.each(ret.data,function(k,v){
                    html +='<option value="'+v.role_id+'">'+ v.role_name+'</option>';
                });
                $('#e_role_id').html(html);
                $('#e_virtual_role_id').html(html);
            }
        }, 'json');

        departId= $(this).parent().parent().attr('data-depart_id');
            $.get('structure/depart/ajax_get_depart_backup' ,{depart_id:departId},function(ret) {
                if(ret.code==200) {
                    $.each(form_tpl, function(key, val) {

                        if(val=='user_id'){
                            var users = ret.data[val];
                               $('#e_user_id').tokenInput("clear");
                            $.each(users,function(index,u){
                                $('#e_user_id').tokenInput("add", {user_id:u['user_id'] , name: u['name_cn']});

                            });
                        }else if(val == 'leader_id'){
                            var leader = [ret.data[val]];
                            $('#e_leader_id').tokenInput("clear");
                            $.each(leader,function(index,u){
                                $('#e_leader_id').tokenInput("add", {leader_id:u['leader_id'] , name: u['leader_name']});
                                $('#e_leader_id').val(ret.data[val].leader_id);
                            });
                        }else if(val == 'sub_leader_id'){
                            var sub = [ret.data[val]];
                            $('#e_sub_leader_id').tokenInput("clear");
                            $.each(sub,function(index,u){
                                $('#e_sub_leader_id').tokenInput("add", {sub_leader_id:u['sub_leader_id'] , name: u['sub_name']});
                                $('#e_sub_leader_id').val(ret.data[val].sub_leader_id);
                            });
                        }else if(val == 'parent_id'){
                            var parent = [ret.data[val]];
                            $('#e_parent_id').tokenInput("clear");
                            $.each(parent,function(index,u){
                                $('#e_parent_id').tokenInput("add", {parent_id:u['parent_id'] , name: u['parent_name']});
                                $('#e_parent_id').val(ret.data[val].parent_id);
                            });
                        }else if(val == 'virtual_leader_id'){
                            var virtual = [ret.data[val]];
                            $('#e_virtual_leader_id').tokenInput("clear");
                            $.each(virtual,function(index,u){
                                $('#e_virtual_leader_id').tokenInput("add", {virtual_leader_id:u['virtual_leader_id'] , name: u['virtual_name']});
                                $('#e_virtual_leader_id').val(ret.data[val].virtual_leader_id);
                            });
                        }else if(val == 'depart_id'){
                            $('#e_depart_name').val(ret.data[val].depart_name);
                            $('#e_depart_id').val(ret.data[val].depart_id);
                        }else if(val == 'role_id'){
                            $('#e_role_id').val(ret.data[val]);
                        }else if(val == 'virtual_role_id'){
                            $('#e_virtual_role_id').val(ret.data[val]);
                        }else{
                             $('#'+'e_'+ val).val(ret.data[val]);
                        }
                    });
                    $('#myEditModal').modal('show');
                }
            },'json');
    });

    $('#edit_btn').click(function() {
        var roleId = $("#e_role_id").val();
        if(!roleId){
            $('#e_role_id').css('border-color', '#007472');
            return ;
        }
        $('#myEditModal').modal('hide');
        var myForm = $('#editForm').serializeArray();
        myForm.push({name:'depart_id',value:departId});

        $.post('structure/depart/ajax_update_depart',myForm, function(ret) {
            if (ret.code == 200) {
                show_message(ret.code,ret);
                setTimeout("window.location.href = '/structure/depart/depart_home_backup'",1500);
            } else {
                show_message(ret.code,ret);
            }
        }, 'json');

    });

    //删除
    $('.speed_delete_model').click(function(e) {
        e.stopPropagation();
         departId = $(this).parent().parent().attr('data-depart_id');
        $('#myDeleteModal').modal('show');

    });
    $('#delete_btn').click(function() {
        $('#myDeleteModal').modal('hide');
        $.post('structure/depart/ajax_delete_depart',{depart_id:departId}, function(ret) {
            if (ret.code == 200) {
                show_message(ret.code,ret);
                setTimeout("window.location.href = '/structure/depart/depart_home_backup'",1500);
            } else {
                show_message(ret.code,ret);
            }
        }, 'json');

    });

    $('#online_btn').click(function() {
        $.get('structure/depart/ajax_push_db',{}, function(ret) {
            if (ret.code == 200) {
                show_message(ret.code,ret);
                setTimeout("window.location.href = '/structure/depart/depart_home'",1500);
            } else {
                show_message(ret.code,ret);
            }
        }, 'json');
    });
</script>
@endsection

