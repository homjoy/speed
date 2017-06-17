@extends('layouts.master')

@section('content')
<link type="text/css" rel="stylesheet" href="/static/css/pagination.css">
<script src="/static/js/pagination.js"></script>
<style type="text/css">
.form-group {
	width: 125px;
}

.form-group>#parent_task_type,#sub_task_type,#user_name{
	width: 135px;
}
</style>
<div>
	<div class="row">
		<form id="myForm" action="/workflow/task/ajax_get_task_list" method="post" class="navbar-form navbar-left">
			<span class="btn">任务大类:</span>
			<div class="form-group">
				<select class="form-control" name="parent_task_type" id="parent_task_type">
					<option value="0">请选择</option>
					@if (!empty($parentType))
					@foreach ($parentType as $p)
						<option value="{{ $p['type_id'] }}">{{ $p['type_name'] }}</option>
					@endforeach
					@endif
				</select>
			</div>

			<span class="btn">任务小类:</span>
			<div class="form-group ">
				<select class="form-control" name="sub_task_type" id="sub_task_type">
					<option value="0">请选择</option>				
	            </select>
			</div>

			<span class="btn">创建人:</span>
			<div class="form-group">
				<input id="user_id" type="hidden" class="form-control" name="user_id" value="0">
				<input id="user_name" name="user_name" class="form-control">
			</div>

			<span  class="btn ">审批人:</span>
			<div class="form-group">
				<input id="current_user_id" type="hidden" class="form-control" name="current_user_id" value="0">
				<input id="current_user_name" name="current_user_name" class="form-control">
			</div>

			<button id="btn_submit" type="submit" class="btn btn-default">搜索任务</button>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
			
		任务管理
	</div>
	<!-- Table -->
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th width="5%">ID</th>
				<th width="15%">任务名称</th>
				<th width="10%">任务类型</th>
				<th width="22%" style="white-space: pre-wrap;word-break: break-all;text-align: justify; width: 200px; table-layout: fixed; word-wrap: break-word; word-break: break-all;white-space:normal; word-break:break-all;overflow:hidden;">任务内容</th>
				<th width="8%">创建人</th>
				<th width="10%">当前审批人</th>
				<th width="15%">创建时间</th>
				<th width="15%">操作</th>
			</tr>
		</thead>
		<tbody id="task_list">
			@if (isset($taskList) && !empty($taskList)) 
				@foreach ($taskList as $t)
					<tr>
						<td>{{ $t['task_id'] }}</td>
						<td>{{ $t['task_name'] }}</td>
						<td>{{ $t['tasktype_name'] }}</td>
						<td>{{ $t['task_content'] }}</td>
						<td>{{ $t['user_name'] }}</td>
						<td>{{ $t['current_user_name'] }}</td>
						<td>{{ $t['create_time'] }}</td>
						<td><a href="/workflow/task/show_task_detail?task_id={{$t['task_id']}}">查看详情</a>&nbsp;&nbsp;<a href="/workflow/task/manual_transfer?task_id={{$t['task_id']}}">手动转移</a></td>
					</tr>
				@endforeach 
			@endif
		</tbody>
	</table>
	<div class="pagination-left">
    </div>
</div>
<script type="text/javascript">
	
	function init() {
		
		$("select").val("0");
		$("input[name$='id']").val("0");
		$("input[name$='name']").val("");
	}

	var callback_func = function(list) {
		init();
		$("#task_list").empty();
		if (list['data']) {
			$(".pagination-left").hide();

			$.each(list['data'], function(i, val) {
				var row = "<tr><td scope='row'>" + val['task_id'] + "</td><td>" + val['task_name'] + "</td><td>" + 
				val['tasktype_name'] + "</td><td stype='width: 200px; table-layout: fixed; word-wrap: break-word; word-break: break-all'>" + val['task_content'] + "</td><td>" + val['user_name'] + "</td><td>" 
				+ val['current_user_name'] + "</td><td>" + val['create_time'] + 
				"</td><td><a href='/workflow/task/show_task_detail?task_id=" + val['task_id'] + 
				"'>查看详情</a>&nbsp;&nbsp;<a href='/workflow/task/manual_transfer?task_id=" + val['task_id'] + 
				"'>手动转移</a></td></tr>";
	    		$("#task_list").append(row);
	    	});
		}	
    };

    var callback_error = function() {
    	init();
    	$("#task_list").empty();
    }

	$(function() {
		init();

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
	        firstPagesCount: 4, //最前面的展现页数，默认值为2
	        preposePagesCount: 2, //当前页的紧邻前置页数，默认值为2
	        postposePagesCount: 2, //当前页的紧邻后置页数，默认值为1
	        lastPagesCount: 4, //最后面的展现页数，默认值为0
	        href: false, //不生成链接
	        first: '', //取消首页
	        prev: '<',
	        next: '>',
	        last: '', //取消尾页
	        go: 'Go' //取消页面跳转
	    }).on("switch",function(e,page){
	        location.href="/workflow/task/manage?page="+page;
	    });

		var subType = <?php echo json_encode($subType) ?>;

		$("#parent_task_type").change(function(){
			$("#sub_task_type").empty();
			var parent_id = $("#parent_task_type").val();
			var option = $("<option>").val("0").text("请选择");
			$("#sub_task_type").append(option);

			if (subType[parent_id]) {
				var sub_type = subType[parent_id];
				$.each(sub_type, function(i,val) {
					var option = $("<option>").val(val['type_id']).text(val['type_name']);
	      			$("#sub_task_type").append(option);
	    		});
			} 
		});

    	var cache = {};
    	$("#user_name").autocomplete({
      		minLength: 2,
      		source: function( request, response ) {
        		var term = request.term;
        		if ( term in cache ) {
        			data = cache[ term ];
          			response( $.map(data['data'], function(item){
	          			return { label: item.name, value: item.name, id: item.id}
	          		}) );
          			return;
        		}
 
	        	$.getJSON( "/workflow/user/ajax_user_search", request, function( data, status, xhr ) {
	          		if (data && data['data']) {
	          			cache[ term ] = data;
	          			response( $.map(data['data'], function(item){
		          			return { label: item.name, value: item.name, id: item.id}
		          		}) );
	          		}	
	        	});
      		},
      		select: function(event, ui) {
      			$("#user_id").val(ui.item.id);
      			$("#user_name").val(ui.item.value);

      			return false;
      		}
    	});
    	$("#current_user_name").autocomplete({
      		minLength: 2,
      		source: function( request, response ) {
        		var term = request.term;
        		if ( term in cache ) {
        			data = cache[ term ];
          			response( $.map(data['data'], function(item){
	          			return { label: item.name, value: item.name, id: item.id}
	          		}) );
          			return;
        		}
 
	        	$.getJSON( "/workflow/user/ajax_user_search", request, function( data, status, xhr ) {
	          		if (data && data['data']) {
	          			cache[ term ] = data;
	          			response( $.map(data['data'], function(item){
		          			return { label: item.name, value: item.name, id: item.id}
		          		}) );
	          		}
	        	});
      		},
      		select: function(event, ui) {
      			$("#current_user_id").val(ui.item.id);
      			$("#current_user_name").val(ui.item.value);

      			return false;
      		}
    	});
  	});
</script>
@endsection