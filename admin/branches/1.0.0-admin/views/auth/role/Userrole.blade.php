@extends('layouts.master')

@section('content')
<style type="text/css">
#ui-id-1 {
	z-index: 9999;
}
</style>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
		@if($data && isset($roleInfo) && isset($userInfoAll))
		<button style="float: right;" class="btn btn-primary add_new edit_btn" type="button" data-toggle="modal" data-target="#cateModal">添加</button>
		@endif	
		角色列表
	</div>
	<!-- Table -->
	<table class="table table-striped">
		<thead>
			<tr>
				<th>角色名称</th>
				<th>用户名称</th>
				<th>状态</th>
				<th style="width: 100px;">操作</th>
			</tr>
		</thead>
		<tbody>
			@if($data && isset($roleInfo) && isset($userInfoAll))
			@foreach ($data as $t)
			<tr>
				<td style="display:none;">{{ $t['id'] }}</td>
				<td>{{ $roleInfo[$t['role_id']]['role_name'] }}</td>	
				<td>{{ $userInfoAll[$t['user_id']]['name_cn'] }}</td>							
				<td>
					@if ($t['status'] == 1)
					<button roleid="{{ $t['role_id'] }}" class="btn btn-success btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span> 有效
					</button>
					@else
					<button roleid="{{ $t['role_id'] }}" class="btn btn-default btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-close"></span> 无效
					</button>
					@endif
				</td>
				<td><a href="#" uid = "{{ $t['user_id'] }}"  rid="{{ $t['role_id'] }}" class="edit_btn" data-toggle="modal" data-target="#cateModal">编辑</a></td>
			</tr>	
			@endforeach
			@else
			<tr><td colspan="4" class="text-center"><h5>没有查看权限</h5><td><tr>
			@endif
		</tbody>
    </table>
	
	<!-- 添加/编辑 分类 -->
	<div class="modal fade" id="cateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">添加</h4>
				</div>
				<div class="modal-body">
					<form action="/auth/role/ajax_user_role_add" class="edit_form" id="myForm">
						<input type="hidden" name="id" value="0" />
						<div class="input-group select_input">
							<span class="input-group-addon" id="sizing-addon2">角色名称</span>
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" class="form-control" name="role_id" value="0" />
								<button type="button" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" class="btn btn-default form-control">选择角色</button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" id="tree" role="menu">
									@if(isset($roleInfo) && !empty($roleInfo))
									@foreach ($roleInfo as $t)
										@if($t['role_id'] != 1)
										<li><a class="select_item" data="{{ $t['role_id'] }}" href="#"> {{ $t['role_name'] }}</a></li>
										@endif
									@endforeach
									@endif
									<li class="divider"></li>
									<li><a class="select_item" data="0" href="#">选择角色</a></li>
								</ul>
							</div>
						</div>
						<br />
						<div class="input-group">
							<input id="user_id" type="hidden" class="form-control" name="user_id" value="0" />
							<span for="user_name" class="input-group-addon" id="sizing-addon2">用户名称</span>
							<input id="user_name" name="user_name" class="form-control" placeholder="用户名称" aria-describedby="sizing-addon2">
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
	$('.edit_btn').click(function(){
		if ($(this).hasClass('add_new')) {
			$('input[name=role_id]').val(0);
			$('input[name=user_id]').val(0);
			$('input[name=user_name]').val('');
			$('.select_item[data=0]').click();
			
			return;
		}
		
		var tds = $(this).parents('tr').children('td');
		var id = parseInt(tds.eq(0).text());
		var user_id = $(this).attr('uid');
		var role_id = $(this).attr('rid');
		var user_name = tds.eq(2).text();

		var status = $.trim(tds.eq(3).text());
		status = status == '有效' ? 1 : 0;
		$('input[name=id]').val(id);
		$('input[name=role_id]').val(role_id);
		$('input[name=user_id]').val(user_id);
		$('input[name=user_name]').val(user_name);
		$(':radio[name=status][value=' + status + ']').parents('label').click();
		
		if (role_id == 1) {
			$('.select_item[data=0]').click();
		} else {
			$('.select_item[data='+ role_id +']').click();
		}
	});

	$(function() {
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
  	});
</script>
@endsection