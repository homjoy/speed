@extends('layouts.master')
@section('content')
    <style type="text/css">
        .online_btn {
            margin-left: 10px;
            float: right;
        }

    </style>
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <div class="panel">
        <div class="panel-body">

                <ul class="nav nav-tabs slope col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 30px">

                    <li role="presentation"><a href="/assets/assets_supply_home" class="application_num">信息</a></li>
                    <li class="active" role="presentation"><a>供应商</a></li>
                </ul>
                <div class="from-container">
                    <form id="searchForm" class="form-inline  form-horizontal" role="form"
                          action="/assets/assets_company_home" method="get">
                        <div class="row">

                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <select class="form-control" name="status" id="status"
                                        value="{{$search_params['status'] or  ''}}">
                                    <option value="" @if(empty($search_params['status'])) selected @endif> 状态</option>
                                    <option value="0"
                                            @if(isset($search_params['status']) && $search_params['status'] == '0') selected @endif>
                                        无效
                                    </option>
                                    <option value="1"
                                            @if(isset($search_params['status']) && $search_params['status'] == '1') selected @endif>
                                        有效
                                    </option>
                                    <option value="2"
                                            @if(isset($search_params['status']) && $search_params['status'] == '2') selected @endif>
                                        禁用
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-2">
                                <input type="submit" class="btn btn-default" id="retBtn" value="搜索"/>
                            </div>
                            <div class="col-lg-1  col-md-1 col-sm-2">
                                <button class="btn btn-primary add_new addBtn" type="button" data-toggle="modal"
                                        data-target="#cateModal">添加
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <div class="table-container">
                <table class="table table-striped table-hover" style="font-size:14px;">
                    <thead>
                    <tr>
                        <th class="col-xs-2">id</th>
                        <th class="col-xs-2">名称</th>
                        <th class="col-xs-2">状态</th>
                        <th class="col-xs-2">备注</th>
                        <th class="col-xs-2">操作消息</th>
                    </tr>
                    </thead>
                    <tbody id="show">
                    @if (!empty($data))

                        @foreach ($data as $t)
                            <tr>
                                <td>{{ $t['id'] }}</td>
                                <td>{{ $t['name'] }}</td>
                                <td>{{ $t['status'] }}</td>
                                <td>{{ $t['memo'] }}</td>
                                <td>
                                    <a class="btn btn-sm btn-warning editBtn" type="button" data-toggle="modal"
                                       data-name="{{$t['name']}}"  data-status="{{$t['status']}}"   data-memo="{{$t['memo']}}"  data-id="{{$t['id']}}"><span
                                         class="glyphicon glyphicon-log-in rm-disable"></span>修改
                                    </a>
                                    <a class="btn btn-sm btn-danger  deleteBtn" type="button" data-toggle="modal"
                                       data-id="{{$t['id']}}"><span class="glyphicon glyphicon-remove rm-disable"></span>删除
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

            </div>
            <div class="pagination-left">
            </div>
    </div>
<!--        </div>-->
        <!-- 添加修改模版 -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">添加/修改</h4>
                    </div>
                    <div class="modal-body">
                        <form  id="addForm" class="form-horizontal">
                            <div class="form-group">
                                <label for="depart_id" class="col-sm-2 control-label">ID</label>
                                <div class="col-sm-10">
                                    <input type="text" readonly class="form-control m_id" name ='id'  placeholder="ID">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="depart_id" class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control m_name" name ='name' placeholder="名称">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="memo" class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control m_memo" name ='memo'  placeholder="备注">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="e_status" class="col-sm-2 control-label">类型</label>
                                <div class="col-sm-10">
                                    <select     class="form-control m_status" name="status">
                                        <option value="1" >有效</option>
                                        <option value="0" >无效</option>
                                        <option value="2" >禁用</option>
                                    </select>
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
        <!-- 添加修改模版 -->
    <script src="/static/js/bootbox.min.js"></script>
    <script type="text/javascript">

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
            location.href = "/assets/assets_company_home?page=" + page + '&status='+ $('#status').val();
        });

        $('.editBtn').click(function(e) {

            e.stopPropagation();
            var _this=$(this);
            $('.m_id').val(_this.attr('data-id'));
            $('.m_name').val(_this.attr('data-name'));
            $('.m_memo').val(_this.attr('data-memo'));
            switch(_this.attr('data-status')){
                case '有效':
                    $('.m_status').val(1);
                    break;
                case '禁用':
                    $('.m_status').val(2);
                    break;
                case '无效':
                default:
                    $('.m_status').val(0);
                    break;

           }
            $('#myModal').modal('show');
        });

        $('.addBtn').click(function(e) {
            e.stopPropagation();
            var _this=$(this);
            $('.m_id').val('');
            $('.m_name').val('');
            $('.m_memo').val('');
            $('#myModal').modal('show');
        });
        $('#add_btn').click(function() {
            $('#myModal').modal('hide');
            var myForm = $('#addForm').serializeArray();
            $.post('/assets/ajax_update_add_company',myForm, function(ret) {
                if (ret.code == 200) {
                    show_message(ret.code,ret);
                    setTimeout("window.location.href = '/assets/assets_company_home'",1500);
                } else {
                    show_message(ret.code,ret);
                }
            }, 'json');

        });
        //删除
        $('.deleteBtn').click(function(){

            var _this = $(this)
            bootbox.confirm('确认删除吗？', function (result) {
                if (result) {
                    _this.attr('disabled',true);
                    var id = _this.attr('data-id');

                    $.ajax({
                        url: "/assets/ajax_update_add_company",
                        method: "POST",
                        data: {id: _this.attr('data-id'),status:0},
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

