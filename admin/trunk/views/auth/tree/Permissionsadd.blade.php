@extends('layouts.master')
@section('content')
<style type="text/css">
    .tree {
        min-height:20px;
        padding:19px;
        margin-bottom:20px;
    }
    .tree li {
        list-style-type:none;
        margin:0;
        padding:5px 5px 0 5px;
        position:relative
    }
    .tree li::before, .tree li::after {
        content:'';
        left:-20px;
        position:absolute;
        right:auto
    }
    .tree li::before {
        border-left:1px solid #999;
        bottom:40px;
        height:100%;
        top:0;
        width:1px
    }
    .tree li::after {
        border-top:1px solid #999;
        height:20px;
        top:25px;
        width:25px
    }
    .tree li span {
        -moz-border-radius:5px;
        -webkit-border-radius:5px;
        border:1px solid #999;
        border-radius:5px;
        display:inline-block;
        padding:1px 8px;
        text-decoration:none
    }
    .tree li.parent_li>span {
        cursor:pointer
    }
    .tree>ul>li::before, .tree>ul>li::after {
        border:0
    }
    .tree li:last-child::before {
        height:25px
    }
    .tree li.parent_li>span:hover, .tree li.parent_li>span:hover+ul li span {
        background:#eee;
        border:1px solid #94a0b4;
        color:#000
    }
</style>
<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading" style="overflow: hidden; line-height: 32px;">
        首页/权限管理/<a href="/auth/role/role">角色管理</a>/【{{$role_info['role_name']}}】权限列表
    </div>
    <!-- Table -->
    <form action="/auth/tree/batch_permissions_add" method="post" name="post_permissions" id="post_permissions">

    <div class="tree">
            <input type="hidden" value="{{$role_info['role_id']}}" name="role_id"/>

            <?php
            if (empty($tree)) {
                echo '<ul  class="list-group"><li  class="list-group-item"></li></ul>';
            } else {
                $dept = $tree;

                function dept_ul_li($dept, $has_permissions) {
                    $dept_str = '<ul >';
                    foreach ($dept as $key => $val) {
                        $dept_str .= "<li  data-depart_id='" . $val['tree_id'] . "'>"  . ' <input ';
                        if (in_array($val['tree_id'], $has_permissions)) {
                            $dept_str .=' checked ';
                        }
                        $dept_str .= ' class="tree_info" type="checkbox" name="tree_id[]" value="' . $val['tree_id'] . '"/> <span><i class="icon-folder-open"></i> ' . $val['tree_name'].'</span>';
                        if ($val['child']) {
                            $dept_str .= dept_ul_li($val['child'], $has_permissions);
                        }
                    }
                    $dept_str .= '</li></ul>';
                    return $dept_str;
                }

                $ul_li = dept_ul_li($dept, $has_permissions);
                echo $ul_li;
            }
            ?>

    </div>
        <div class="text-center"> <button type="button" class="btn btn-success form-control" id="btn_submit" style="width: auto;">保存</button> <a class="btn btn-success form-control"  style="width: auto;" href="/auth/role/role">返回</a></div>
    </form>

    <!-- 添加/编辑 分类 -->

</div>
<script>
$('.tree_info').click( function () {
    var _this = $(this);
    //_this.siblings('ul').children('.tree_info').attr('checked',_this.checked);
    var is_checked = $(this).is(":checked");
     _this.siblings('ul').find('.tree_info').each(function(){
           $(this).prop("checked",is_checked);
     })
       // e.stopPropagation();
});
$('#btn_submit').click(function () {
    var _this = $(this);
    _this.attr('disabled',true);
    $.ajax({
        method: "POST",
        url: "/auth/tree/batch_permissions_add",
        data: $('#post_permissions').serialize(),
        success: function (msg) {
            _this.removeAttr('disabled');
            msg = JSON.parse(msg);
            if (msg.code == 200) {
                show_message(msg.code, msg);

                setTimeout("location.reload()", 1500);
            } else {
                var ret = [];
                ret['error_msg'] = msg['error_msg'];
                show_message(msg.code, ret);
            }
        }
    })
})
</script>
@endsection