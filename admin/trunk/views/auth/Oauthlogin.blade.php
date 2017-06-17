@extends('layouts.master')

@section('content')
<div class="modal fade" id="myModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">错误提示</h4>
			</div>
			<div class="modal-body">
				<?php if(200 != $code) { ?>
				<div role="alert" class="alert alert-danger">
					<strong><?php echo $error_msg ?></strong>
					<br />
					请重新尝试登陆！
				</div>
				<?php }else { ?>
				<div role="alert" class="alert alert-success">
					<strong><?php echo $error_msg ?></strong>
				</div>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_href">确定</button>
			</div>
		</div>
	</div>
</div>
<script>
	var url = '<?php echo empty($url)?'':$url ?>';
	<?php if(200 == $code) { ?>
	var defaultUrl = '/';
	
	if (url == defaultUrl) {
		window.location.href = url;
	}else {
		$('#myModal').modal('show');
		setTimeout(function(){
			$('#btn_href').click();
		},3000);
	}
	
	 
	<?php }else { ?>
	var defaultUrl = '/';
	$('#myModal').modal('show');
	<?php } ?>
	
	
	
	$('#btn_href').click(function(){
		window.location.href = url ? url : defaultUrl;
		return false;
	});
</script>
@endsection