@extends('layouts.master')

@section('topNav')
<li class="@if ($dispathModule == 'process') active @endif"><a href="/workflow/process/index">工作流管理</a></li>
<li class="@if ($dispathModule == 'task') active @endif"><a href="/workflow/task/manage">任务管理</a></li>
<li class="dropdown @if ($dispathModule == 'user') active @endif">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">用户管理 <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="/workflow/user/roleInfoIndex">角色管理</a></li>
		<li class="divider"></li>
		<li><a href="/workflow/user/userRoleMapIndex">用户角色映射管理</a></li>
		<li class="divider"></li>
		<li><a href="/workflow/user/userAgencySet">汇报线人员代理设置</a></li>
	</ul>
</li>
@endsection