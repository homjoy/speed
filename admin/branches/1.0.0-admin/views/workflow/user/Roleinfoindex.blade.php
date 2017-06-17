@extends('layouts.master')

@section('content')

<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
		<button style="float: right;" class="btn btn-primary add_new edit_btn" type="button" data-toggle="modal" data-target="#cateModal">添加</button>
			
		角色信息
	</div>
	<!-- Table -->
	<table class="table table-striped">
		<thead>
			<tr>
				<th>ID</th>
				<th>角色名称</th>
				<th>状态</th>
				<th style="width: 150px;">操作</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($data))
			@foreach ($data as $t)
			<tr>
				<td scope="row">{{ $t['role_id'] }}</td>
				<td>{{ $t['role_name'] }}</td>
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
				<td><a href="#" class="edit_btn" data-toggle="modal" data-target="#cateModal">编辑角色</a></td>
			</tr>
			@endforeach
			@else 
				<tr><td colspan="4" class="text-center"><h5>没有角色数据信息</h5><td><tr>
			@endif
		</tbody>
    </table>
	
	<!-- 添加/编辑 角色 -->
	<div class="modal fade" id="cateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">添加</h4>
				</div>
				<div class="modal-body">
					<form action="/workflow/user/role_info_edit" class="edit_form" id="myForm">
						<input type="hidden" name="role_id" value="0" />
						<div class="input-group">
							<span class="input-group-addon" id="sizing-addon2">角色名称</span>
							<input type="text" name="role_name" class="form-control" placeholder="角色名称" aria-describedby="sizing-addon2">
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
			$('input[name=role_name]').val('');
			$(':radio[name=status][value=1]').parents('label').click();
			
			return;
		}
		
		var tds = $(this).parents('tr').children('td');
		var role_id = parseInt(tds.eq(0).text());
		var role_name = $.trim(tds.eq(1).text());
		var status = $.trim(tds.eq(2).text());
		status = status == '有效' ? 1 : 0;
		
		$('input[name=role_id]').val(role_id);
		$('input[name=role_name]').val(role_name);
		$(':radio[name=status][value=' + status + ']').parents('label').click();
	});
</script>
@endsection