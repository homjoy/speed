@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css" xmlns="http://www.w3.org/1999/html">
<script src="/static/js/tokeninputspeed.js"></script>
<link rel="stylesheet" href="/static/css/bootstrap-select.css">
<script src="/static/js/bootstrap-select.js"></script>
<style type="text/css">
    .bootstrap-select ul{max-height: 270px !important;}
    /*请不要报错 良辰必有重谢,然而你果断报错*/
    .token-input-token,.token-input-input-token{ line-height: 23px !important;}
    .selectpicker{
        display: none;
    }
    .password_notice{
        margin:0 auto;text-align: center ;
    }
</style>
<div class="panel" >
    <div class="panel-body" >
        <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">
            <li role="presentation"><a href="/structure/depart/depart_home" class="application_num">部门首页</a></li>
            <li  role="presentation"> <a href="/structure/depart/application_edit">申请修改</a></li>
            <li role="presentation"><a href="/structure/depart/add_title" class="approval_num">添加职位</a></li>
            <li class="active"  role="presentation" ><a href="/structure/depart/depart_leader_home">操作部门leader</a></li>
        </ul>
    <div id="">
            <form  class="form-inline  form-horizontal edit_from" role="form" >
                 <div class="row">
                    <label class="control-label col-lg-3" for="user_id"><h5>员工姓名:</h5></label>
                    <div class="col-lg-3 ">
                        <input type="text" class="form-control user_id"  name="user_id"/>
                    </div>
                     <div class="col-lg-1">
                         <a href="javascript:void(0);" class="btn btn-default  query_btn"  type="button"><span class="glyphicon glyphicon-search"></span>搜索</a>
                     </div>
                     <div class="col-lg-1">
                         <button  class="btn btn-primary  submit_btn" type="button" >提交</button>
                     </div>
                </div>
                <div class="row">
                    <label class="control-label col-lg-3" for="departId"><h5>部门名称:</h5></label>
                        <select    class="col-lg-3 selectpicker"  data-live-search="true" name="depart_id" id="departId" >
                            <option value=""></option>
                            @if (!empty($depart))
                            @foreach ($depart as $p)
                            <option value="{{ $p['depart_id'] }}">{{ $p['depart_name'] }}</option>
                            @endforeach
                            @endif
                        </select>
                </div>
                <div class="row">
                    <label class="control-label col-lg-3" for=""><h5>修改类型:</h5></label>

                    <div class="col-lg-6">
                        <h5><input type="radio" name="type" value="1" checked="checked"/>部门领导
                        <input type="radio" name="type" value="2" />部门代理领导
                        <input type="radio" name="type" value="3" />部门汇报关系(仅支持原有关系修改)</h5>
                    </div>
                </div>
                <div class="row">

                    <label class="control-label col-lg-3" for="leaderId"><h5>领导名字:</h5></label>
                     <select  class="col-lg-3 selectpicker"  data-live-search="true" name="leader_id" id="leaderId" >
                            <option value=""></option>
                            @if (!empty($leader))
                            @foreach ($leader as $p)
                            <option value="{{ $p['user_id'] }}">{{ $p['name_cn'] }}</option>
                            @endforeach
                            @endif
                      </select>

                </div>
           </form>
        <table class="table-hover table table-striped table-bordered">
            <thead>
            <tr>
                <th>部门名称</th>
                <th>leader</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(empty($data)){

                echo '<tr><td class="text-center" colspan=2>未找到相匹配的数据</td></tr>';
            }else{
                foreach($data as $key =>$v){
                    echo <<<DOC
        <tr>
            <td>{$v['depart_name']}</td>
            <td>{$v['name_cn']}</td>
        </tr>
DOC;
                }
            }
            ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-lg-12 alert alert-success">
                预计当前员工所在部门汇报流程为：<br>
                <?php
                if(empty( $all_depart_leader)&&empty($all_lower)){

                }else{

                if(array_filter($all_lower)){
                    $str =NULL;
                    foreach($all_lower as $key =>$v){


                        $v['name_cn']=   isset($v['name_cn'])?$v['name_cn']:'';
                        $str .=$v['name_cn'].',';

                    }
                    $str =rtrim($str,',');
                    $str .='<i class="glyphicon glyphicon-arrow-right"></i>';
                    echo $str;
                }
                if(array_filter($all_depart_leader)){

                    foreach($all_depart_leader as $key =>$v){
                        if(isset( $v['leader_user_name'])){
                            if(!array_diff_assoc($v,end($all_depart_leader))){
                                echo <<<DOC
                                                {$v['leader_user_name']}
DOC;
                            }else{
                                echo <<<DOC
                                                {$v['leader_user_name']}<i class="glyphicon glyphicon-arrow-right"></i>
DOC;
                            }
}

                        }
                }

                }
                ?>
                <br>若有流程问题，请联系 <a href="mailto:honghzou@meilishuo.com">@周宏</a> 修改员工关系
                <br>
            </div>
        </div>
    </div>
</div>
    </div>
<script>
    //tokeninput
    $('.user_id').tokenInput("/structure/depart/ajax_search_name",{
        queryParam:'search',tokenValue:'user_id',tokenLimit: 1,onAdd:function(ret) {
            $('.user_id').val(ret['user_id']);
        }
    });
    $('.query_btn').click(function() {
        location.href="/structure/depart/depart_leader_home?user_id="+$('.user_id').val();
    });
    $('.submit_btn').click(function() {
        var myForm = $('.edit_from').serializeArray();
        $.post('/structure/depart/ajax_update_leader',myForm, function(ret) {
            if(ret.code==200){
                show_message(ret.code,ret);
            }else{
                show_message(ret.code,ret);
            }
        }, 'json');
    });


</script>
@endsection