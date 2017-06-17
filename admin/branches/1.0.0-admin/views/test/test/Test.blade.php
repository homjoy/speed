@extends('layouts.dashboard')

@section('dashboard_content')
<link type="text/css" rel="stylesheet" href="/assets/css/pagination.css">
<script src="/assets/js/pagination.js"></script>
<div class="bs-callout bs-callout-danger">
    <h4>公告</h4>
    <p>为了保证信息安全，请不要将<code>Appkey</code>、<code>Appsecret</code>、<code>Token</code>等敏感信息泄露给其它人，谢谢合作！</p>
    <p>应用审核请发送邮件<code>jingjingzhang@meilishuo.com</code>抄送<code>speed-rd@meilishuo.com</code></p>
</div>

<h3>应用列表</h3>


<table class="table">
    <caption>正式应用</caption>
    <thead>
    <tr>
        <th>#</th>
        <th width="20%">应用名称</th>
        <th width="20%">应用类型</th>
        <th width="30%">时间</th>
        <th width="20%">状态</th>
        <th width="20%">操作</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="pagination-left">
</div>
<hr />

<h3>创建应用</h3>
<p>SPEED开放平台目前只支持美丽说内部系统的对接，如果您的系统需要接入用户邮箱登录、获取用户信息以及接入speed平台，客户端等需求，请点此 <a href="/app/apply" class="btn btn-info">申请创建应用</a> 。</p>

<script>
    $(function() {
        $('a.statusChange_btn').click(function(){
            var url = $(this).attr('href');
            $.getJSON(url,function(json){
                alert(json.msg);
                window.location.reload();
            });
            return false;
        });

        // 分页
        var count = '{!! @json_encode($count) !!}';
        try {
            count = $.parseJSON(count);
        } catch(e) {
            count = [];
        }

        var page = '{!! @json_encode($page) !!}';
        try {
            page = $.parseJSON(page);
        } catch(e) {
            page = [];
        }

        $(".pagination-left").pagination({
            //总页数
            totalPage:count,
            //初始选中页
            currentPage:page,
            //最前面的展现页数
            firstPagesCount: 0, //最前面的展现页数，默认值为2
            preposePagesCount: 2, //当前页的紧邻前置页数，默认值为2
            postposePagesCount: 0, //当前页的紧邻后置页数，默认值为1
            lastPagesCount: 2, //最后面的展现页数，默认值为0
            href: false, //不生成链接
            first: '', //取消首页
            prev: '<',
            next: '>',
            last: '', //取消尾页
            go: false //取消页面跳转
        }).on("switch",function(e,page){
            location.href="/dashboard/home?page="+page;
        });
    });
</script>

@endsection