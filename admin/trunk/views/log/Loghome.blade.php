@extends('layouts.master')

@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <link rel="stylesheet" href="/static/css/tokeninput.css">
    <script src="/static/js/tokeninputspeed.js"></script>
    <style type="text/css">

        .token-input-token,.token-input-input-token{line-height: 23px !important;}
    </style>
    <div class="panel" >
        <div class="panel-body" >


        <div class="container">
            <div class="from-container">
                <form id="searchForm" class="form-inline  form-horizontal" role="form" action="/log/log_home" method="get">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <input type="text" class="form-control" placeholder="操作人" id="search" name="user_id" value="{{$search_params['user_id']}}"/>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <input type="text" class="form-control" placeholder="操作后数据关键词" id="after_data" name="after_data" value="{{$search_params['after_data']}}"/>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <input type="text" class="form-control" placeholder="对应的ID" id="handle_id" name="handle_id" value="{{$search_params['handle_id']}}"/>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <select name="handle_type" id="handle_type" class="form-control">
                                <option value="" >请选择</option>
                           @foreach ($handle_type as $handle_key => $handle_value)
                                    <option @if($search_params['handle_type'] == $handle_key) selected @endif value="{{$handle_key}}" >{{$handle_value}}</option>
                           @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-2">
                                <input type="submit" class="btn btn-primary" id="retBtn" value="搜索"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-container">
            <table class="table table-striped table-hover"  style="font-size:14px;">
                <thead>
                <tr>
                    <th class="col-xs-1">姓名</th>
                    <th class="col-xs-1">操作ID</th>
                    <th class="col-xs-1">操作类型</th>
                    <th class="col-xs-5">操作后数据</th>
                    <th class="col-xs-2">更新时间</th>
                    <th class="col-xs-2">类型</th>
                </tr>
                </thead>
                <tbody id="show">
                @if (!empty($data))

                    @foreach ($data as $t)
                        <tr>
                            <td>{{ $t['name'] }}</td>
                            <td>{{ $t['handle_id'] }}</td>
                            <td>{{ $t['operation_type'] or  '' }}</td>
                            <td><pre>{{ $t['after_data'] }}</pre></td>
                            <td> {{ $t['update_time'] }} </td>
                            <td> {{ $t['handle_type'] }} </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="pagination-left">
        </div>
        <!--</div>-->
</div>

        <script type="text/javascript">
            //分页
            var count = '{!! json_encode($count) !!}';
            try {
                count = $.parseJSON(count);
            } catch (e) {
                count = [];
            }
            var page = '{!! json_encode($page) !!}';
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
                go: false //取消页面跳转
            }).on("switch", function (e, page) {
                var user_id = $("#search").val();
                var after_data = $('#after_data').val();
                var handle_type = $('#handle_type').val();
                var handle_id =  $('#handle_id').val();
                location.href = "/log/log_home?page=" + page +'&user_id='+user_id+'&after_data='+after_data+'&handle_type='+handle_type+'&handle_id='+handle_id;
            });
            //用户
            var initUser= '{!!@json_encode($search_params["name_config"]) !!}';

            try{
                initUser = $.parseJSON(initUser);
            }catch(e){
                initUser = [];
            }
            $('#search').tokenInput("/structure/depart/ajax_search_name",{
                queryParam:'search',
                tokenValue:'user_id',
                tokenLimit: 1,
                prePopulate: initUser,
                onAdd:function(ret) {
                    $('#search').val(ret['user_id']);
                }
            });
        </script>

@endsection
