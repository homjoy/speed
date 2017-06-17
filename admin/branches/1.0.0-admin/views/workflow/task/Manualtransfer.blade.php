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
			任务详情
		</div>
		<div class="portlet-body" style="display: block;">
			<div class="row">
				<div class="col-md-12">
				@if (isset($taskInfo) && !empty($taskInfo))
					<h2>{{ $taskInfo['task_name'] }}</h2>
					<blockquote>
					<ul class="breadcrumb breadcrumb-taskType">
						<li>任务类型：</li>
						<li> {{ $tasktypeMap[$taskInfo['tasktype_id']] }}</li>
					</ul>
					<p>{{ $taskInfo['task_content'] }}</p>
					<small>创建于 {{ $taskInfo['create_time'] }}</small>
					</blockquote>
				@endif
				</div>
			</div>
		</div>
	</div>
	<!-- BEGIN PROGRESS CONTENT -->
	<div class="panel panel-default">
        <div class="row">
		<!--main content start-->
			<div class="col-md-12">
                <div class="panel-heading">
                    任务进度
                </div>
                <div class="panel-body">
                    <div class="media">
                    @if (isset($progressList) && !empty($progressList))
                    @foreach ($progressList as $v)
                    	<div class="media-body">
                    		@if ($v['process_id'] != 0)
                    		<h4 class="media-heading"> - {{ $processList[$v['process_id']]['process_name'] }} </h4>
                    		@else 
                    		<h4 class="media-heading"> - 创建任务 </h4>
                    		@endif
                    		<blockquote>
                    			<p style="float: left; line-height: 35px; padding: 0 20px; font-size: 14px; font-weight: bold;">操作记录：</p>
                    			<ul class="breadcrumb breadcrumb-taskProgress">
									<li><b><?php echo $v['progress_content'] ?></b></li>
									<li><b><?php echo $v['create_time'] ?></b></li>
								</ul>
                    		</blockquote>
                    	</div>
                    @endforeach
                    @endif
                    <button class="btn btn-primary" id="transfer" type="button" data-toggle="modal" data-target="#cateModal"><sub>︾</sub> 流程手动转移 <sub>︾</sub></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 流程手动转移 -->
    <div class="modal fade" id="cateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">流程手动转移</h4>
				</div>
				<div class="modal-body">
					<form action="/workflow/task/ajax_task_manual_transfer" class="edit_form" id="myForm">
						<input type="hidden" name="task_id" value="{{ $taskInfo['task_id'] }}" />
						<div class="input-group select_input">
							<span class="input-group-addon">节点选择</span>
							<div class="btn-group btn_group_select_input_widget">
								<input type="hidden" class="form-control" name="process_id" value="0" />
								<button type="button" style="border-radius: 0; border-left: none; text-align: left; width: 200px;" class="btn btn-default form-control">选择节点</button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									@foreach ($processList as $p)
									<li><a class="select_item" data="{{ $p['process_id'] }}" href="#"> {{ $p['process_name'] }} </a></li>
									@endforeach
									<li class="divider"></li>
									<li><a class="select_item" data="0" href="#">选择节点</a></li>
								</ul>
							</div>
						</div>

						<!-- <br />

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
									@foreach ($roleList as $r)
									<li><a class="select_item" data="{{ $r['role_id'] }}" href="#"> {{ $r['role_name'] }} </a></li>
									@endforeach
									<li class="divider"></li>
									<li><a class="select_item" data="0" href="#">选择角色</a></li>
								</ul>
							</div>
						</div> -->

						<br />

						<div class="input-group">
							<input id="user_id" type="hidden" class="form-control" name="user_id" value="0" />
							<span for="user_name" class="input-group-addon" id="sizing-addon2">用户名称</span>
							<input id="user_name" name="user_name" class="form-control" placeholder="用户名称" aria-describedby="sizing-addon2">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="cate_cancel">取消</button>
					<button type="button" class="btn btn-success form-control" id="btn_submit" style="width: auto;">转移</button>
				</div>
			</div>
		</div>
	</div>
	<script>
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