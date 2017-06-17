@extends('layouts.master')

@section('content')
	<div role="alert" class="alert alert-danger">
			<strong>{{ $data['error_msg'] }}</strong> 
	</div>
    <img style="margin:50px auto;margin-top: 150px;" src="/static/img/404.jpg" alt="" />
	<style>
		#right_container {
			text-align: center;
		}
	</style>
@endsection