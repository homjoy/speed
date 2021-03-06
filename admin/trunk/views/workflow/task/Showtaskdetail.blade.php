@extends('layouts.master')

@section('content')
<style type="text/css">
	#avatar {
		height: 60px;
		width: 60px;
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
						<li> {{ $taskInfo['tasktype_name'] }}</li>
					</ul>
					<p>{{ $taskInfo['task_content'] }}</p>
					<div class="well" id="div_task_info">
						<div class="col-md-2 name pull-left">
							创建人：<br/>
							当前审批人：<br/>
							当前节点：<br/>
							审批状态：
						</div>
						<div>
							{{ $taskInfo['user_name'] }} <br/>
							{{ $taskInfo['current_user_name'] }} <br/>
							{{ $taskInfo['current_node_name'] }} <br/>
							{{ $taskInfo['status_name'] }}
						</div>
					</div>
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
                    @foreach ($progressList as $k)
                    	<img id="avatar" class="pull-left" src="{{ $k['current_user_avatar'] }}"> </img>
                    	<div class="media-body">
                    		@if ($k['current_user_id'] == 0)
                    			<h4 class="media-heading"> 系统 </h4>
                    		@else
                    			<h4 class="media-heading">{{ $k['current_user_name'] }}</h4>
                    		@endif
                    		<h5>{{ $k['process_name'] }}</h5>
                    		<blockquote>
                    			{{ $k['progress_content'] }}
                    			<br/>
                    			<small class="pull-right">{{ $k['create_time'] }}</small>
                    		</blockquote>
                    	</div>
                    @endforeach
                    @endif
                    </div>
                </div>
            </div>
        </div>
	</div>
	<!-- END PROGRESS CONTENT -->

@endsection