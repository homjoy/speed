@extends('layouts.master')

@section('content')
<style type="text/css">
#ui-id-1 {
	z-index: 9999;
}
</style>
<div>
	<div class="row">
		<form id="searchForm" action="/workflow/user/ajax_search_user_role_map" method="post" class="navbar-form navbar-left">
			<span class="btn">角色:</span>
			<div class="form-group">
				<select class="form-control" name="search_role" id="search_role">
					<option value="0">请选择</option>
					@if (!empty($roleInfo))
					@foreach ($roleInfo as $r)
						<option value="{{ $r['role_id'] }}">{{ $r['role_name'] }}</option>
					@endforeach
					@endif
				</select>
			</div>

			<span class="btn">用户:</span>
			<div class="form-group">
				<input id="search_user_id" type="hidden" class="form-control" name="search_user_id" value="0">
				<input id="search_user_name" name="search_user_name" class="form-control" stype="width:300px;">
			</div>

			<button type="submit" formId="searchForm" class="btn btn-default btn_submit">搜索用户角色</button>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
		<button style="float: right;" class="btn btn-primary add_new edit_btn" type="button" data-toggle="modal" data-target="#cateModal">添加</button>
			
		用户角色映射信息
	</div>
	<!-- Table -->
	<table class="table table-striped">
		<thead>
			<tr>
				<th>ID</th>
				<th>用户名称</th>
				<th>角色名称</th>
				<th>状态</th>
				<th style="width: 150px;">操作</th>
			</tr>
		</thead>
		<tbody id="show">
			@if (isset($userRole) && !empty($userRole))
			@foreach ($userRole as $t)
			<tr>
				<td scope="row">{{ $t['map_id'] }}</td>
				<td>{{ $t['user_name'] }}</td>
				<td>{{ $t['role_name'] }}</td>
				<td>
					@if ($t['status'] == 1)
					<button mapid="{{ $t['map_id'] }}" class="btn btn-success btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span> 有效
					</button>
					@else
					<button mapid="{{ $t['map_id'] }}" class="btn btn-default btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-close"></span> 无效
					</button>
					@endif
				</td>
				<td><a href="#" class="edit_btn" data-toggle="modal" roleid="{{ $t['role_id'] }}" userid="{{ $t['user_id'] }}" data-target="#cateModal">编辑映射</a></td>
			</tr>
			@endforeach
			@else 
				<tr><td colspan="5" class="text-center"><h5>没有用户角色映射数据信息</h5><td><tr>
			@endif
		</tbody>
    </table>
	
	<!-- 添加/编辑 用户角色映射 -->
	<div class="modal fade" id="cateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">添加</h4>
				</div>
				<div class="modal-body">
					<form action="/workflow/user/user_role_map_edit" class="edit_form" id="myForm">
						<input type="hidden" name="map_id" value="0" />

						<div class="input-group">
							<input id="user_id" type="hidden" class="form-control" name="user_id" value="0" />
							<span for="user_name" class="input-group-addon" id="sizing-addon2">用户名称</span>
							<input id="user_name" name="user_name" class="form-control" placeholder="用户名称" aria-describedby="sizing-addon2">
						</div>

						<br />

						<div class="input-group select_input">
							<span class="input-group-addon">角色选择</span>
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" class="form-control" name="role_id" value="0" />
								<button type="button" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" class="btn btn-default form-control">选择角色</button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									@foreach ($roleInfo as $r)
									<li><a class="select_item" data="{{ $r['role_id'] }}" href="#"> {{ $r['role_name'] }} </a></li>
									@endforeach
									<li class="divider"></li>
									<li><a class="select_item" data="0" href="#">选择角色</a></li>
								</ul>
							</div>
						</div>

						<br />
						<div class="input-group">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default active">
									<input type="radio" name="status" value="1" id="option1" autocomplete="off" checked="checked" class="form-control">
									<span aria-hidden="true" class="glyphicon glyphicon-record"></span> 
									有效
								</label>
								<label class="btn btn-default">
									<input type="radio" name="status" value="0" id="option2" autocomplete="off" class="form-control">
									<span aria-hidden="true" class="glyphicon glyphicon-record"></span> 
									无效
								</label>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="cate_cancel">取消</button>
					<button type="button" class="btn btn-success form-control" id="btn_submit" style="width: auto;">保存</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

	function init() {
		$("#search_role").val("0");
		$("input[name='search_user_id']").val("0");
		$("input[name='search_user_name']").val("");
	}

	$('.edit_btn').click(function() {
		if ($(this).hasClass('add_new')) {
			$('input[name=map_id]').val(0);
			$('input[name=user_id]').val(0);
			$('input[name=user_name]').val('');
			$('input[name=role_id]').val(0);
			$('.select_item[data=0]').click();
			$(':radio[name=status][value=1]').parents('label').click();
			
			return;
		}
		
		editUserRole($(this).closest('tr'),$(this));
	});

	var callback_func = function(list) {
		init();
		if (list['data']) {
			$("#show").empty();

			$.each(list['data'], function(i, val) {

				if (val['status'] == 1) {
					var row = "<tr><td scope='row'>" + val['map_id'] + "</td><td>" + val['user_name'] + "</td><td>" +
					val['role_name'] + "</td><td><button mapid='" + val['map_id'] +"' class='btn btn-success btn-xs'" + 
					" type='button'><span aria-hidden='true' class='glyphicon glyphicon-eye-open'></span> 有效</button>" 
					+ "</td><td><a href='#' class='edit_btn' data-toggle='modal' roleid='" + 
					val['role_id'] + "' userid='" + val['user_id'] + "' data-target='#cateModal'>编辑映射</a></td>"; 
				} else {
					var row = "<tr><td scope='row'>" + val['map_id'] + "</td><td>" + val['user_name'] + "</td><td>" +
					val['role_name'] + "</td><td><button mapid='" + val['map_id'] +"' class='btn btn-default btn-xs'" + 
					" type='button'><span aria-hidden='true' class='glyphicon glyphicon-eye-close'></span> 无效</button>" 
					+ "</td><td><a href='#' class='edit_btn' data-toggle='modal' roleid='" + 
					val['role_id'] + "' userid='" + val['user_id'] + "' data-target='#cateModal'>编辑映射</a></td>"; 
				} 
				
				$("#show").append(row);
			});
		} 
	}

	$(document).on("click",'#show .edit_btn',function(e) {
		var btn = $(e.target);
		editUserRole(btn.closest('tr'),btn);
	});

	function editUserRole(tr,btn)
	{
		if(!tr || tr.length <= 0){
			return;
		}
		var tds = tr.children('td');
		var map_id = parseInt(tds.eq(0).text());
		var user_name = tds.eq(1).text();
		var status = $.trim(tds.eq(3).text());
		status = status == '有效' ? 1 : 0;
		var role_id = btn.attr('roleid');
		var user_id = btn.attr('userid');
		
		$('input[name=map_id]').val(map_id);
		$('input[name=user_id]').val(user_id);
		$('input[name=user_name]').val(user_name);
		$('.select_item[data='+ role_id +']').click();
		$(':radio[name=status][value=' + status + ']').parents('label').click();
	}

	$(function() {
		init();
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

    	$("#search_user_name").autocomplete({
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
      			$("#search_user_id").val(ui.item.id);
      			$("#search_user_name").val(ui.item.value);

      			return false;
      		}
    	});
  	});
</script>
@endsection