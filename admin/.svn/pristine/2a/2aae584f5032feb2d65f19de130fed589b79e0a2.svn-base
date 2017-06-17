@extends('layouts.master')
@section('content')
    <link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
    <script src="/static/js/pagination.js"></script>
    <link rel="stylesheet" href="/static/css/bootstrap-select.css">
    <style type="text/css">

        .ul_input {
            overflow: auto;
            height: 200px;
            border: 1px solid #ddd;
        }

        .ul_input li {
            padding: 3px 20px;
        }
    </style>

    <div class="panel">
        <div class="panel-body">

            <form id="searchForm" class="form-inline  form-horizontal" method="POST" role="form"
                  action="/mail/mail_home">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" placeholder="按照邮箱进行搜索，可用邮箱前缀进行搜搜"
                               value="@if(!empty($search_params['email'])){{$search_params['email']}} @endif" id="email"
                               name="email"/>
                    </div>
                    <div class="col-lg-1">
                        <input type="submit" value="搜索" class="btn btn-default">
                    </div>
                    <div class="col-lg-1">
                        <a href="/mail/mail_group_add" class="btn btn-primary add_new add_btn" type="button" id="mail_group_add">添加
                        </a>
                    </div>
                </div>
                <p></p>

                <div class="table-container">
                    <table class="table-hover table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>
                                邮箱
                            </th>
                            <th>
                                邮箱组
                            </th>
                            <th>
                                状态
                            </th>
                            <th>
                                操作
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (empty($data)) {
                            echo '<tr><td class="text-center" colspan=5>未找到相匹配的数据</td></tr>';
                        } else {
                            foreach ($data as $key => $v) {

                                if (isset($v['status']) && $v['status'] == 1) {
                                    $v['status'] = '有效';
                                } else {
                                    $v['status'] = '无效';
                                }

                                echo <<<DOC
            <tr>
                <td>{$v['group_id']}</td>
                <td>{$v['show_name']}</td>
                <td>{$v['group_name']}@meilishuo.com</td>
                <td>{$v['status']}</td>
                <td >
                     <a href='/mail/mail_group_list?email={$v["group_name"]}' class="btn btn-primary btn-sm  edit_btn" type="button">
                        <span class="glyphicon glyphicon-search"></span>查看
                    </a>
                    <a class="btn btn-sm  btn-danger  delete_btn" data-group-name="{$v['group_name']}"
                       title="删除邮件组"><span class="glyphicon glyphicon-remove rm-disable"></span>删除
                    </a>


                </td>
            </tr>
DOC;
                            }
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-left">
                </div>
            </form>
        </div>
    </div>

    <script src="/static/js/bootbox.min.js"></script>

    <script>
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
            var email = $('#email').val()
            location.href = "/mail/mail_home?page=" + page + '&email=' + email;

        });




        //删除邮件组
        $('.delete_btn').click(function () {
            var _this = $(this)
            bootbox.confirm('确认删除邮件组？', function (result) {
                        if (result) {
                            var group_name = _this.attr('data-group-name');
                            if (!group_name) {
                                show_message(0, '删除的邮件组不能为空', '删除的邮件组不能为空');
                                return;
                            }

                            $(this).attr('disabled', true);
                            $.ajax({
                                url: "/mail/ajax_mail_group_list_del",
                                method: "POST",
                                data: {group_name: group_name},
                                dataType: "json",
                                success: function (data) {
                                    _this.removeAttr('disabled');
                                    if (data.code == 200) {
                                        show_message(data.code, data);
                                        setTimeout("location.reload()", 4000);
                                    } else {
                                        var ret = [];
                                        ret['error_msg'] = data['error_detail'];
                                        show_message(data.code, ret);
                                    }
                                }
                            });
                        }
                    }
            )
        })
    </script>
@endsection