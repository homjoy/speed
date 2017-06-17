@extends('layouts.master')

@section('content')
<style>
	.process_panel {
		border-top:none;
		border-radius: 0 0 4px 4px;
	}
	
	#svg_panel {
		width: 100%;
		position: relative;
		padding-top: 30px;
		overflow: auto;
	}
	#svg_panel #process_title {
		position: absolute;
		top: 0;
		right: 0;
		z-index: 50;
	}
	#svg_panel #process_nodes {
		position: relative;
		min-width: 100%;
		text-align: center;
		z-index: 20;
	}
	#svg_panel #process_nodes table {
		width: 100%;
	}
	#svg_panel .process_node {
		margin: 0 auto;
		margin-bottom: 80px;
		border: 1px solid #666;
		width: 250px;
		padding: 15px;
		border-radius: 15px;
		text-align: left;
	}
	#svg_panel .process_node .process_title {
		font-weight: bold;
		font-size: 18px;
		margin: 0px;
		line-height: 25px;
	}
	#svg_panel .process_node .edit_process {
		padding:5px 0;
		display:block;
	}
	#svg_panel svg {
		width: 100%;
		height: 100%;
		position: absolute;
		z-index: 10;
		top: 30px;
		left: 0;
	}
	
	.next_process_ids {
		margin-bottom: 20px;
	}
	
	p.process_node_btn {
		margin:0 10px;
		overflow: hidden;
		border-bottom: 1px solid #ccc;
		line-height: 30px;
		height: 30px;
	}
	.process_node_btn a {
		float: right;
		line-height: 25px;
	}
	.action_box {
		margin-bottom: 20px;
	}
	#agree, #prev_node {
		padding-bottom: 10px;
		border-bottom: 1px solid gray;
		margin-bottom: 10px;
	}
	.action_box .select_input, #ruleEdit .select_input {
		margin-bottom: 20px;
	}
	.action_box .select_input .glyphicon, #ruleEditModal .select_input .del_rule {
		cursor: pointer;
		height: 14px;
		overflow: hidden;
		position: absolute;
		right: 90px;
		top: 10px;
		width: 14px;
	}
	#actionEditModal .action_box .input-group-addon, #ruleEditModal .select_input .input-group-addon {
		border-bottom-left-radius: 4px;
		border-top-left-radius: 4px;
	}
	
	#process_nodes .hide {
		display: block !important;
		visibility: hidden;
	}
	.dropdown-toggle {
		height: 34px;
	}
</style>
<ul class="nav nav-tabs nav_tab_widget">
	<button style="float: right;" class="btn btn-primary add_new edit_process" type="button" data-toggle="modal" data-target="#nodeEditModal">添加节点</button>
	
	<li role="presentation" class="active"><a href="#" panel-class="nav_tab_panel">流程视图</a></li>
	<li role="presentation"><a href="#" panel-class="nav_tab_panel">表格视图</a></li>
</ul>
<div class="panel panel-default process_panel nav_tab_panel">
	<div class="panel-body">
		<div id="svg_panel">
			<button id="process_title" class="btn btn-default" type="button"><span aria-hidden="true" class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;&nbsp; {{ $typeInfo['type_name'] }} 流程图</button>
			{!! $flowHtml !!}
			<svg version="1.1" xmlns="http://www.w3.org/2000/svg">
			</svg>
		</div>
	</div>
</div>

<div class="panel panel-default process_panel nav_tab_panel" style="display:none;">
	<div class="panel-body">
		<div data-example-id="panel-without-body-with-table" class="bs-example">
			<div class="panel panel-default">
			<!-- Default panel contents -->
				<div class="panel-heading"><b>{{ $typeInfo['type_name'] }}</b> 流程节点表格</div>
				<!-- Table -->
				<table class="table table-striped">
					<thead>
						<tr>
							<th>process_id</th>
							<th>节点名称</th>
							<th>上级节点id</th>
							<th>下级节点id</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					@if($processTab)
					<tbody>
						@foreach ($processTab as $p)
						<tr>
							<th scope="row">{{ $p['process_id'] }}</th>
							<td>{{ $p['process_name'] }}</td>
							<td>{{ $p['pre_process_ids'] }}</td>
							<td>{{ $p['next_process_ids'] }}</td>
							<td>
								@if ($p['status'] == 1)
								<button class="btn btn-success btn-xs" type="button">
									<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span> 有效
								</button>
								@else
								<button class="btn btn-default btn-xs" type="button">
									<span aria-hidden="true" class="glyphicon glyphicon-eye-close"></span> 无效
								</button>
								@endif
							</td>
							<td class="process_node" pid="{{ $p['process_id'] }}">
								<a data-target="#nodeEditModal" data-toggle="modal" class="edit_process" href="#">编辑信息</a>
								<a data-target="#ruleEditModal" data-toggle="modal" class="edit_process_rule" href="#">编辑规则</a>
								<a data-target="#actionEditModal" data-toggle="modal" class="edit_process_action" href="#">编辑动作</a>
							</td>
						</tr>
						@endforeach
					</tbody>
					@else
					<tbody>
						<tr>
							<td colspan="4"><center>请添加节点信息</center></td>
						</tr>
					</tbody>
					@endif
				</table>
			</div>
		</div>
	</div>
</div>

<script src="/static/js/jqueryui.combobox.js"></script>
<script>
	var process_arr = new Array();
	var multi_node_id = new Array();
	@if($processTab)
	var processInfo = {!! $processJson or '{}' !!};
	@foreach ($processTab as $p)
	process_arr[{{ $p['process_id'] }}] = '{{ $p['next_process_ids'] }}';
	/*@ if (count($p['pre_process_ids_arr']) > 1) 
	multi_node_id.push({{ $p['process_id'] }});
	@ endif */
	@endforeach
	@endif
	
	var ruleInfo = {!! $ruleInfo or '{}' !!};
	var actionInfoOrigin = {!! $actionInfo or '{}' !!};
	var actionInfo = [];
	
	for (var i in actionInfoOrigin) {
		if (!actionInfo[actionInfoOrigin[i]['process_id']]) {
			actionInfo[actionInfoOrigin[i]['process_id']] = [];
		}
		
		if(!actionInfo[actionInfoOrigin[i]['process_id']][actionInfoOrigin[i]['action_type']]) {
			actionInfo[actionInfoOrigin[i]['process_id']][actionInfoOrigin[i]['action_type']] = [];
		}
		
		actionInfo[actionInfoOrigin[i]['process_id']][actionInfoOrigin[i]['action_type']].push(actionInfoOrigin[i]);
	}
</script>
<script src="/static/js/process_node.js"></script>

<!-- 添加/编辑 节点 -->
<div class="modal fade" id="nodeEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="nodeEditModal">编辑节点</h4>
			</div>
			<div class="modal-body">
				<form action="/workflow/process/ajax_porcess_node_edit" class="edit_form" id="nodeEdit">
					<input type="hidden" name="process_id" value="0" />
					<input type="hidden" name="tasktype_id" value="{{ $typeInfo['type_id'] }}" />
					<div class="input-group">
						<span class="input-group-addon" id="sizing-addon2">节点名称</span>
						<input type="text" name="process_name" class="form-control" placeholder="节点名称" aria-describedby="sizing-addon2">
					</div>
					<br />
					
					<div class="input-group select_input">
						<span class="input-group-addon">角色操作限制</span>
						<div class="btn-group btn_group_select_input_widget">
							<input type="hidden" class="form-control" name="role_id" value="0" />
							<button type="button" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" class="btn btn-default form-control">无限制</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a class="select_item" data="0" href="#">无限制</a></li>
								<li class="divider"></li>
								@foreach ($speedRoleInfo as $r)
								<li><a class="select_item" data="speed_{{ $r['role_id'] }}" href="#">{{ $r['role_name'] }}</a></li>
								@endforeach
								<li class="divider"></li>
								<li><a class="select_item" data="direct_1" href="#">直属上级审批</a></li>
								<li class="divider"></li>
								@foreach ($speedRoleInfo as $r)
								<li><a class="select_item" data="speed_{{ $r['role_id'] }}_till" href="#">逐级审批，直到 {{ $r['role_name'] }} 为止</a></li>
								@endforeach
								<li class="divider"></li>
								@foreach ($roleInfo as $r)
								<li><a class="select_item" data="wf_{{ $r['role_id'] }}" href="#">{{ $r['role_name'] }}</a></li>
								@endforeach
								<!--//逐级审批直到某职位之前或之后-->
								<li class="divider"></li>
								@foreach ($speedRoleInfo as $r)
								<li><a class="select_item" data="speed_{{ $r['role_id'] }}_before" href="#">逐级审批，直到 {{ $r['role_name'] }} 之前</a></li>
								@endforeach
								<li class="divider"></li>
								@foreach ($speedRoleInfo as $r)
								<li><a class="select_item" data="speed_{{ $r['role_id'] }}_after" href="#">逐级审批，直到 {{ $r['role_name'] }} 之后</a></li>
								@endforeach
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
				<button type="button" class="btn btn-success form-control btn_submit" formId="nodeEdit" style="width: auto;">保存</button>
			</div>
		</div>
	</div>
</div>

<!-- 添加/编辑 动作 -->
<div class="modal fade bs-example-modal-lg" id="actionEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 900px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">编辑动作</h4>
			</div>
			<div class="modal-body">
				<form action="/workflow/process/ajax_porcess_action_edit" class="edit_form" id="actionEdit">
					<input type="hidden" name="process_id" value="0" />
					
					<div id="agree" class="action_box">
						<div class="input-group select_input">
							<input type="hidden" name="agree_action_id[]" value="0" />
							<span aria-hidden="true" class="glyphicon glyphicon-remove del_action"></span>
							<span id="sizing-addon2" class="input-group-addon">“同意”附加动作</span>
							<input type="text" name="agree_action_name[]" class="form-control" placeholder="动作名称" aria-describedby="sizing-addon2" style="width: 50%;">
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" value="0" name="agree_action[]" class="form-control">
								<button class="btn btn-default form-control" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" type="button">请选择附加动作</button>
								<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul role="menu" class="dropdown-menu">
									<li><a href="#" data="0" class="select_item">请选择附加动作</a></li>
									<li class="divider"></li>
									@if (!empty($actions))
									@foreach ($actions as $a)
									<li><a href="#" data="{{ $a }}" class="select_item">{{ $a }}</a></li>
									@endforeach
									@endif
								</ul>
							</div>
						</div>
					</div>
					
					<div id="disagree" class="action_box">
						<div class="input-group select_input">
							<input type="hidden" name="disagree_action_id[]" value="0" />
							<span aria-hidden="true" class="glyphicon glyphicon-remove del_action"></span>
							<span id="sizing-addon2" class="input-group-addon">“驳回”附加动作</span>
							<input type="text" name="disagree_action_name[]" class="form-control" placeholder="动作名称" aria-describedby="sizing-addon2" style="width: 50%;">
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" value="0" name="disagree_action[]" class="form-control">
								<button class="btn btn-default form-control" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" type="button">请选择附加动作</button>
								<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul role="menu" class="dropdown-menu">
									<li><a href="#" data="0" class="select_item">请选择附加动作</a></li>
									<li class="divider"></li>
									@if (!empty($actions))
									@foreach ($actions as $a)
									<li><a href="#" data="{{ $a }}" class="select_item">{{ $a }}</a></li>
									@endforeach
									@endif
								</ul>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info add_action agree" style="float:  left;">添加“同意”动作</button>
				<button type="button" class="btn btn-info add_action disagree" style="float:  left;">添加“驳回”动作</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" id="cate_cancel">取消</button>
				<button type="button" class="btn btn-success form-control btn_submit" formId="actionEdit" style="width: auto;">保存</button>
			</div>
		</div>
	</div>
</div>

<!-- 添加/编辑 规则 -->
<div class="modal fade bs-example-modal-lg" id="ruleEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 900px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">编辑上下级节点</h4>
			</div>
			<div class="modal-body">
				<form action="/workflow/process/ajax_porcess_rule_edit" class="edit_form" id="ruleEdit">
					<input type="hidden" name="process_id" value="0" />
					
					<div id="prev_node">
						<div class="input-group select_input">
							<span aria-hidden="true" class="glyphicon glyphicon-remove del_rule"></span>
							<span id="sizing-addon2" class="input-group-addon">上级节点</span>
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" value="0" name="prev_node[]" class="form-control">
								<button class="btn btn-default form-control" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" type="button">请选择上级节点</button>
								<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul role="menu" class="dropdown-menu">
									<li><a href="#" data="0" class="select_item">起始</a></li>
									<li class="divider"></li>
									@foreach ($processTab as $p)
									<li><a href="#" data="{{ $p['process_id'] }}" class="select_item">{{ $p['process_name'] }}</a></li>
									@endforeach
								</ul>
							</div>
						</div>
					</div>
					
					<div id="next_node">
						<div class="input-group select_input">
							<input type="hidden" name="rid[]" value="0" />
							<span aria-hidden="true" class="glyphicon glyphicon-remove del_rule"></span>
							<span id="sizing-addon2" class="input-group-addon">下级节点</span>
							<input type="text" name="rule[]" class="form-control" placeholder="规则表达式" aria-describedby="sizing-addon2" style="width: 50%;">
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" value="0" name="next_node[]" class="form-control">
								<button class="btn btn-default form-control" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" type="button">请选择下级节点</button>
								<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul role="menu" class="dropdown-menu">
									@foreach ($processTab as $p)
									<li><a href="#" data="{{ $p['process_id'] }}" class="select_item">{{ $p['process_name'] }}</a></li>
									@endforeach
									<li class="divider"></li>
									<li><a href="#" data="0" class="select_item">结束</a></li>
								</ul>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info add_node_relation prev" style="float:  left;">添加上级节点</button>
				<button type="button" class="btn btn-info add_node_relation next" style="float:  left;">添加下级节点</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" id="cate_cancel">取消</button>
				<button type="button" class="btn btn-success form-control btn_submit" formId="ruleEdit" style="width: auto;">保存</button>
			</div>
		</div>
	</div>
</div>


@endsection