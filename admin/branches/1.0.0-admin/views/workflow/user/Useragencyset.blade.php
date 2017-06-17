@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="/static/css/tokeninput.css" />
<style>
	ul.token-input-list li input {
		margin: 0;
		padding: 0 8px;
	}
	li.token-input-input-token {
		width: 100%;
	}
	li.token-input-input-token input {
		width: 100% !important;
	}
	li.token-input-token {
		padding: 0px 3px;
		line-height: 26px !important;
	}
	.token-input-selected-dropdown-item p {
		color: white !important;
	}
	.token-input-dropdown ul {
		max-height:150px;
		overflow: auto;
	}
	.dept-position-info {
		margin-bottom: 20px;
	}
	
	#relationInfo ul.token-input-list {
		border-radius: 0px;
	}
	#relationInfo .token-input-input-token input {
		padding: 6px 12px;
	}
	#relationInfo li.token-input-input-token {
		width: auto;
	}
</style>

<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading" style="overflow: hidden; line-height: 32px;">
		<form action="/workflow/user/ajax_search_user_depart_relation" id="searchRelation" method="get" class="navbar-form navbar-right" style="margin:0; width: 300px;">
			<button class="btn btn-primary btn_submit" formId="searchRelation" type="submit" style="float:right; margin-left: 20px;">搜索职位信息</button>
			<input name="data" class="form-control userInfoSearch" style="width:200px;" />
		</form>
		
		<span>编辑代理信息</span>
	</div>
	<form id="saveRelation" action="/workflow/user/ajax_user_depart_relation_save">
		<input type="hidden" name="o_uid" id="o_uid" />
		<div>
			<div class="modal-body" id="relationInfo">
				<center>请输入中文姓名，英文姓名，邮箱等的关键词进行查询</center>
			</div>
			<div class="modal-footer" id="control_panel" style="display: none;">
				<button style="width: auto;" formId="saveRelation" class="btn btn-success form-control btn_submit" type="button">保存</button>
			</div>
		</div>
		
	</form>
</div>

<script src="/static/js/tokeninput.js"></script>
<script>
	$('.userInfoSearch').tokenInput('/workflow/user/ajax_user_search',
		{
			queryParam:'search',
			tokenLimit:1,
			onResult: function (results) {
				var returnData = [];
				
				if (results.data) {
					$.each(results.data,function(idx,val){
						var tmpObj = {};
						tmpObj.user_id = val.id;
						tmpObj.name = val.name;
						tmpObj.mail = val.mail;
						returnData.push(tmpObj);
					});
				}
				
				//console.log(returnData);
				return returnData;
			},
			resultsFormatter: function(item){
				console.log(item);
				return "<li><p>" + item.name + "</p></li>"
			},
			tokenFormatter: function(item) {
				return "<li><p>" + item.name + "</p></li>"
			},
			onAdd: function (item) {
				if('keyword' == $(this).attr('name')) {
					$(this).val(item.user_id + '-' + item.name);
				}else if ('data' == $(this).attr('name')) {
					$(this).val(item.user_id + '{|{}}' + item.name + '{|{}}' + item.mail);
				}
				$(this).removeAttr('disabled');
			},
			onDelete: function (item) {
				$(this).val('').attr('disabled', 'true');
			},
		}
	);
	
	$('.token-input-input-token input').focus(function() {
		$('.token-input-dropdown').filter(':visible').width($(this).outerWidth());
	});
	
	$(':text').keypress(function(event){
		if (13 == event.keyCode) {
			return false;
		}
	});
	
	var formId = '';
	$('.btn_submit').click(function() {
		formId = $(this).attr('formId');
	});
	
	var callback_func = function(result) {
		if ('searchRelation' == formId) {
			callback_func_search(result);
		}else if ('saveRelation' == formId) {
			callback_func_save(result)
		}
	}
	
	//获取用户部门职位关系
	var callback_func_search = function(result) {
		var domString = '';
		var agencyInfo = [];
		
		if (result.data.relationInfo) {
			for(var i in result.data.relationInfo) {
				domString += '<div class="input-group dept-position-info"><span class="input-group-addon"><input type="checkbox" name="depart_id[]" ';
				if (result.data.relationInfo[i].agency_info_status) {
					domString += ' checked="checked" ';
				}
				domString += ' value="' + i + '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请员工</span>';
				domString += '<input type="text" class="form-control agent_input" name="agency_' + i + '" ';
				if (result.data.relationInfo[i].agency_info_string) {
					domString += 'value="' + result.data.relationInfo[i].agency_info_string + '"';
				}
				domString += ' />';
				domString += '<span class="input-group-addon">代理工作：';
				domString += result.data.relationInfo[i].dept_name + ' / ' + result.data.relationInfo[i].role_name + '</span>';
				domString += '</div>';
				
				agencyInfo['agency_' + i] = result.data.relationInfo[i].agency_info;
			}
		}
		console.log(agencyInfo);
		$('#o_uid').val(result.data.data[0]);
		
		if (domString == '') {
			$('#relationInfo').html('<center>请输入中文姓名，英文姓名，邮箱等的关键词进行查询</center>');
			$('#control_panel').hide();
		}else {
			$('#relationInfo').html(domString);
			$('#control_panel').show();
			
			$('.agent_input').tokenInput('/workflow/user/ajax_user_search',
				{
					queryParam:'search',
					preventDuplicates:true,
					onResult: function (results) {
						var returnData = [];
					
						if (results.data) {
							$.each(results.data,function(idx,val){
								var tmpObj = {};
								tmpObj.id = tmpObj.user_id = val.id;
								tmpObj.name = val.name;
								tmpObj.mail = val.mail;
								returnData.push(tmpObj);
							});
						}else {
							//自动通过
							returnData.push({
								depart_name: '程序自动运行',
								id: 0,
								user_id: 'auto',
								name: '机器自动跳过',
								mail: 'worker'
							});
						}
						
						//console.log(returnData);
						return returnData;
					},
					resultsFormatter: function(item){
						console.log(item);
						return "<li><p>" + item.name + "</p></li>"
					},
					tokenFormatter: function(item) {
						return "<li><p>" + item.name + "（" + item.user_id + "）</p></li>"
					},
					onAdd: function (item) {
						var originValue = $(this).data('agent-id');
						
						if ( !originValue ) {
							originValue = [];
						}
						
						originValue[item.user_id] = 1;
						
						$(this).data('agent-id', originValue);
						
						var userIds = [];
						for (var i in originValue) {
							userIds.push(i);
						}
						$(this).val( userIds.join(',') );
						
						$(this).removeAttr('disabled');
					},
					onDelete: function (item) {
						var originValue = $(this).data('agent-id');
						
						if (originValue) {
							for (var i in originValue) {
								if (i == item.user_id) {
									delete originValue[i];
								}
							}
						}
						
						$(this).data('agent-id', originValue);
						
						var userIds = [];
						for (var i in originValue) {
							userIds.push(i);
						}
						$(this).val( userIds.join(',') );
					},
				}
			);
			
			//if (agencyInfo.length > 0) {
				for (var i in agencyInfo) {
					for (var m in agencyInfo[i]) {
						$('.agent_input').filter('[name=' + i + ']').tokenInput("add", {id: agencyInfo[i][m].uid, name: agencyInfo[i][m].user_name, user_id: agencyInfo[i][m].uid });
					}
				}
			//}
			
			$('#relationInfo .token-input-input-token input').focus(function() {
				$('.token-input-dropdown').filter(':visible').width($(this).parents('.token-input-list:first').outerWidth() - 2);
			});
		}
	}
	
	//保存代理信息
	var callback_func_save = function(data) {
		show_message(data.code, data);
		//var data = $('#searchRelation :text[name=data]').val();
		//window.location.href = '/workflow/user/userAgencySet?data=' + data;
		$('#searchRelation .btn_submit').click();
	}
</script>
@endsection