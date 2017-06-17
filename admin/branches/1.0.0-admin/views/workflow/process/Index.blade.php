@extends('layouts.master')

@section('content')
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
		<button style="float: right;" class="btn btn-primary add_new edit_btn" type="button" data-toggle="modal" data-target="#cateModal">添加</button>
			
		全部工作流
	</div>
	<!-- Table -->
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>名称</th>
				<th>状态</th>
				<th style="width: 150px;">操作</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($data as $t)
			<tr class="btn-info">
				<td scope="row">{{ $t['type_id'] }}</td>
				<td>{{ $t['type_name'] }}</td>
				<td>
					@if ($t['status'] == 1)
					<button typeid="{{ $t['type_id'] }}" class="btn btn-success btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span> 有效
					</button>
					@else
					<button typeid="{{ $t['type_id'] }}" class="btn btn-default btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-close"></span> 无效
					</button>
					@endif
				</td>
				<td><a href="#" style="color: white;" pid="{{ $t['type_parent_id'] }}" class="edit_btn" data-toggle="modal" data-target="#cateModal">编辑</a></td>
			</tr>
			@if ($t['sub'])
			@foreach ($t['sub'] as $st)
			<tr>
				<td scope="row">{{ $st['type_id'] }}</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $st['type_name'] }}</td>
				<td>
					@if ($t['status'] == 1 && $st['status'] == 1)
					<button typeid="{{ $st['type_id'] }}" class="btn btn-success btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span> 有效
					</button>
					@else
					<button typeid="{{ $st['type_id'] }}" class="btn btn-default btn-xs" type="button">
						<span aria-hidden="true" class="glyphicon glyphicon-eye-close"></span> 无效
					</button>
					@endif
				</td>
				<td>
					<a href="#" class="edit_btn" pid="{{ $st['type_parent_id'] }}" data-toggle="modal" data-target="#cateModal">编辑</a>&nbsp;&nbsp;&nbsp;<a href="/workflow/process/process_info?type_id={{ $st['type_id'] }}">编辑流程</a>
				</td>
			</tr>
			@endforeach
			@endif
			@empty
			<tr>
				<td colspan="4"><center>暂无数据</center></td>
			</tr>
			@endforelse
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
					<form action="/workflow/process/ajax_task_type_edit" class="edit_form" id="myForm">
						<input type="hidden" name="type_id" value="0" />
						<div class="input-group">
							<span class="input-group-addon" id="sizing-addon2">分类名称</span>
							<input type="text" name="type_name" class="form-control" placeholder="分类名称" aria-describedby="sizing-addon2">
						</div>
						<br />
						
						<div class="input-group select_input">
							<span class="input-group-addon">类型选择</span>
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" class="form-control" name="type_parent_id" value="0" />
								<button type="button" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" class="btn btn-default form-control">创建分类</button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									@foreach ($data as $t)
									<li><a class="select_item" data="{{ $t['type_id'] }}" href="#">创建 {{ $t['type_name'] }} 下工作流</a></li>
									@endforeach
									<li class="divider"></li>
									<li><a class="select_item" data="0" href="#">创建分类</a></li>
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
	$('.edit_btn').click(function(){
		if ($(this).hasClass('add_new')) {
			$('input[name=type_id]').val(0);
			$('input[name=type_name]').val('');
			$('.select_item[data=0]').click();
			$(':radio[name=status][value=1]').parents('label').click();
			
			$('h4.modal-title').text('添加');
			
			return;
		}
		
		
		var tds = $(this).parents('tr').children('td');
		var type_id = parseInt(tds.eq(0).text());
		var type_name = $.trim(tds.eq(1).text());
		var pid = $(this).attr('pid');
		var status = $.trim(tds.eq(2).text());
		status = status == '有效' ? 1 : 0;
		
		$('input[name=type_id]').val(type_id);
		$('input[name=type_name]').val(type_name);
		$('.select_item[data=' + pid + ']').click();
		$(':radio[name=status][value=' + status + ']').parents('label').click();
		
		$('h4.modal-title').text('编辑');
	});
</script>
@endsection