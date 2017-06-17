@extends('layouts.master')
@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/bootstrapValidator.min.css">
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <script src="/static/js/pagination.js"></script>
    <div class="panel" >
        <div class="panel-body" >

    <div class="container">
        <div class="row row-offcanvas row-offcanvas-right">
            <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
                <div class="list-group">
                    <a href="/meeting/meeting_edit" class="list-group-item active">添加/修改会议室</a>
                    <a href="/meeting/home" class="list-group-item ">会议室列表</a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <form class="form-horizontal" id="meetingForm" role="form" action="" method="post" data-bv-message="This value is not valid"
                      data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                      data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                      data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
                    <input type="hidden" name="room_id" id="room_id" value="{{ $data['room_id'] or '' }} ">

                    <div class="container">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室编号：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室编号，例如1001" id="room_sn" name="room_sn" value="{{ $data['room_sn'] or ''}}" data-bv-notempty="true" data-bv-notempty-message="请填写会议室编号"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室名称：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室名称" id="room_name" name="room_name" value="{{ $data['room_name'] or ''}}" data-bv-notempty="true" data-bv-notempty-message="请填写会议室名称"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室位置：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室位置" id="room_position" name="room_position" value="{{ $data['room_position'] or ''}}" data-bv-notempty="true" data-bv-notempty-message="请填写会议室位置"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室容量：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室容纳人数" id="room_capacity" name="room_capacity" value="{{ $data['room_capacity'] or ''}}" data-bv-notempty="true" data-bv-notempty-message="请填写会议室容纳人数"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="extension" class="col-xs-2 control-label">会议室分机：</label>

                                <div class="col-xs-8">
                                    <input type="text" class="form-control form-input input-xlarge" placeholder="请填写会议室分机" id="extension" name="extension" value="{{ $data['extension'] or '' }}" data-bv-notempty-message="请填写会议室分机"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type" class="col-xs-2 control-label">会议室类型：</label>

                                <div class="col-xs-8">
                                    <select class="form-control form-input input-xlarge" id="type" name="type">
                                        <option value='1' @if($data['type'] ==1)  SELECTED @endif >会议室</option>
                                        <option value='2' @if($data['type'] ==2)  SELECTED @endif>活动场地 - 用于活动</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="room_area" class="col-xs-2 control-label">会议室地区：</label>

                                <div class="col-xs-8">
                                    <select class="form-control form-input input-xlarge" id="office_id" name="office_id">
                                        @foreach($office_list as $val)
                                            <option value='{{$val['office_id']}}' @if($data['type'] ==$val['office_id'])  SELECTED @endif >{{$val['office_position']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">会议室说明：</label>
                                <div class="col-xs-8">
                                @foreach($service_all as $service_val)
                                    <div class="col-xs-10">
                                        <input  type="checkbox" name="room_detail[]" value='{{ $service_val['service_id'] }}' @if( in_array($service_val['service_id'],$select_rule) ) checked @endif />{{ $service_val['name'] }}
                                        <input type="input"  name="room_detail_{{ $service_val['service_id'] }}" value="{{ $service_rule[$service_val['service_id']]['user'][0]  or ''}}" class="form-control from-input room_detail" />
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-xs-2 control-label">状态：</label>

                                <div class="col-xs-8">
                                    <input type="radio" value="1" @if($data['status'] ==1)  checked @endif name="status"/>可预定
                                    <input type="radio" value="0" @if($data['status'] ==0)  checked @endif name="status"/>不可预定
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success" id="saveMeetingBtn">
                                    <i class="glyphicon glyphicon-plus"></i>保存
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
                <!--end container-->
                <script type="text/javascript" src="/static/js/bootstrapValidator.min.js"></script>
                <script type="text/javascript">
                    $(function () {
                        //分页
                        var count = '{!! @json_encode($count) !!}';
                        try {
                            count = $.parseJSON(count);
                        } catch (e) {
                            count = [];
                        }
                        var page = '{!! @json_encode($page) !!}';
                        try {
                            page = $.parseJSON(page);
                        } catch (e) {
                            page = [];
                        }
                        $(".pagination-left").pagination({
                            //总页数
                            totalPage: count,
                            //初始选中页
                            currentPage: page,
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
                            go: 'Go' //取消页面跳转
                        }).on("switch", function (e, page) {

                            location.href = "/meeting/home?page=" + page;

                        });

                        $('#meetingForm').bootstrapValidator();

                        $('.room_detail').tokenInput("/structure/depart/ajax_search_name",{
                            queryParam:'search' ,
                            tokenLimit: 10,
                            onAdd:function(ret) {
                            }
                        });

                    })
                </script>
@endsection