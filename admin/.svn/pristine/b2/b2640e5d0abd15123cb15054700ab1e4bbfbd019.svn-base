var isChrome = /chrome/.test(navigator.userAgent.toLowerCase());
jQuery.fn.fixPosition = function() {
    var offset = this.offset();
    var pOffset = this.parent().offset();
    var position = this.position();
	//仅针对chrome进行修复.
	var diff = offset.left - pOffset.left;
    return {
        top: position.top,
		left: (isChrome ? position.left + diff : position.left)
    };
};

$(function() {
	var genSVGLine = function (p1, p2) {
			
		var lineDom = '';
		var colorBox = ['#2191c0', '#6eac2c', '#f8da4e', '#e14f1c', '#0078ae', '#6eac2c'];
		var defaultColor = 'rgb(99,99,99)';
		
		var colorValue = '';
		var colorIdx = parseInt(Math.random() * (colorBox.length - 1));
		
		if (colorBox[colorIdx]) {
			colorValue = colorBox[colorIdx];
		}else {
			colorValue = defaultColor;
		}
		
		//箭头
		//右
		//lineDom += '<line x1="' + p2.x + '" y1="' + p2.y + '" x2="' + (p2.x + 10) + '" y2="' + (p2.y - 10) + '" style="stroke:rgb(99,99,99);stroke-width:2" />';
		//左
		//lineDom += '<line x1="' + p2.x + '" y1="' + p2.y + '" x2="' + (p2.x - 10) + '" y2="' + (p2.y - 10) + '" style="stroke:rgb(99,99,99);stroke-width:2" />';
		
		var arrow_point = new Array();
		arrow_point.push(p2.x + ',' + p2.y);
		arrow_point.push((p2.x + 5) + ',' + (p2.y - 10));
		arrow_point.push(p2.x + ',' + (p2.y - 5));
		arrow_point.push((p2.x - 5) + ',' + (p2.y - 10));
		
		lineDom += '<polygon points="' + arrow_point.join(' ') + '" style="fill:' + colorValue + ';stroke:' + colorValue + ';stroke-width:2" />';
		
		if (p1.x == p2.x) {
			lineDom += '<line x1="' + p1.x + '" y1="' + p1.y + '" x2="' + p2.x + '" y2="' + p2.y + '" style="stroke:' + colorValue + ';stroke-width:2" />';
			
			return lineDom;
		}
		
		var tmp_x = parseInt(Math.abs(p1.x - p2.x));
		if (tmp_x < 30) {
			lineDom += '<line x1="' + p2.x + '" y1="' + p1.y + '" x2="' + p2.x + '" y2="' + p2.y + '" style="stroke:' + colorValue + ';stroke-width:2" />';
			
			return lineDom;
		}
		
		//水平高度
		var h = p2.y - margin_height;
		
		//水平线
		lineDom += '<line x1="' + p1.x + '" y1="' + h + '" x2="' + p2.x + '" y2="' + h + '" style="stroke:' + colorValue + ';stroke-width:2" />';
		
		//垂直线
		lineDom += '<line x1="' + p1.x + '" y1="' + h + '" x2="' + p1.x + '" y2="' + p1.y + '" style="stroke:' + colorValue + ';stroke-width:2" />';
		lineDom += '<line x1="' + p2.x + '" y1="' + h + '" x2="' + p2.x + '" y2="' + p2.y + '" style="stroke:' + colorValue + ';stroke-width:2" />';
		
		return lineDom;
	}
	
	//svg 绘图
	var node_block = $('#svg_panel .process_node:first');
	var offset_height = node_block.outerHeight();
	var margin_height = parseInt((node_block.outerHeight(true) - offset_height) / 2);
	var offset_width = node_block.outerWidth() / 2;
	
	var nodes = $('#svg_panel .process_node');
	var lineHtml = '';
	
	var multi_node_id = new Array();
	var node_id_count = new Array();
	
	$('.process_node').each(function(){
		var pid = $(this).attr('pid');
		
		if (node_id_count[pid]) {
			++node_id_count[pid];
		}else {
			node_id_count[pid] = 1;
		}
	});
	
	for (var i in node_id_count) {
		if (node_id_count[i] > 1) {
			multi_node_id.push(i);
		}
	}
	
	
	if (multi_node_id.length) {
		for (var i in multi_node_id) {
			var multi_node = nodes.filter('[pid=' + multi_node_id[i] + ']');
			
			var topMax = 0;
			var showN = multi_node.eq(0);
			
			
			multi_node.each(function(){
				var p = $(this).fixPosition();
				if (p.top > topMax) {
					topMax = p.top;
					showN = $(this);
				}
			});
			
			multi_node.addClass('hide');
			showN.removeClass('hide').addClass('multi');
		}
	}


	nodes.filter(':not(.hide)').each(function(){
		var position = $(this).fixPosition();
		var pid = $(this).attr('pid');
		
		if (process_arr[pid] == 0) {
			return;
		}
		
		offset_height = $(this).outerHeight();
		
		var x1 = parseInt(position.left + offset_width);
		var y1 = parseInt(position.top + offset_height);
		
		var next_pid = process_arr[pid].toString().split(',');
		
		for (var i in next_pid) {
			var endPosition = nodes.filter('[pid=' + next_pid[i] + ']').filter(':not(.hide)');
			
			if (endPosition.size() == 0) {
				continue;
			}
			endPosition = endPosition.fixPosition();
			
			var x2 = parseInt(endPosition.left + offset_width);
			var y2 = parseInt(endPosition.top);
			
			//$('<line style="stroke:rgb(99,99,99);stroke-width:2" />').attr({x1: x1, y1: y1, x2: x2, y2: y2}).appendTo('#svg_panel svg');
			x1 = x1 + parseInt(i * 4);
			
			lineHtml += genSVGLine({x: x1, y: y1}, {x: x2, y: y2});
			
			//lineHtml += '<line x1="' + x1 + '" y1="' + y1 + '" x2="' + x2 + '" y2="' + y2 + '" style="stroke:rgb(99,99,99);stroke-width:2" />'
		}
	});
	
	$('#svg_panel svg').html(lineHtml);
});



//编辑节点，预处理
var ReSetNodeEditPanel = function(title, role_id, status) {
	//process_name
	$('#nodeEditModal :text[name=process_name]').val(title);
	//status
	$('#nodeEditModal :radio[name=status][value=' + status + ']').parents('label:first').val('');
	//role_id
	$('#nodeEditModal input[name=role_id]').val(role_id);
	$('#nodeEditModal .dropdown-menu li a[data=' + role_id + ']').click();
}

//编辑动作预处理
var ReSetActionDelBtn = function() {
	/*$('#actionEditModal .action_box').each(function(){
		if(1 == $(this).find('.del_action').size()) {
			$(this).find('.del_action').hide();
		}else {
			$(this).find('.del_action').show();
		}
	});*/
}
/*var ReSetActionEditPanel = function() {
	
}*/

$(function(){
	//弹出节点编辑面板
	$('.edit_process').click(function(){
		if ($(this).hasClass('add_new')) {
			ReSetNodeEditPanel('', 0, 1);
			$('#nodeEditModal [name=process_id]').val(0);
			
			$('#nodeEditModal .modal-title').text('新建节点');
			
			return;
		}
		
		var key = $(this).parents('.process_node:first').attr('pid');
		
		$('#nodeEditModal .modal-title').text('编辑 〔 ' + processInfo[key]['process_name'] + ' ﹞ 基本信息');
		
		ReSetNodeEditPanel(processInfo[key]['process_name'], processInfo[key]['role_id'], processInfo[key]['status']);
		$('#nodeEditModal :hidden[name=process_id]').val(processInfo[key]['process_id']);
	});
	
	//弹出规则编辑面板
	
	//初始化动作编辑
	var agreeDom = $('#actionEditModal #agree').find('.select_input:first').clone();
	var disagreeDom = $('#actionEditModal #disagree').find('.select_input:first').clone();
	
	//弹出动作编辑面板
	$('.edit_process_action').click(function(){
		var key = $(this).parents('.process_node:first').attr('pid');
		$('#actionEditModal input[name=process_id]').val(key);
		
		$('#actionEditModal .modal-title').text('编辑 〔 ' + processInfo[key]['process_name'] + ' ﹞ 附加处理动作');
		
		$('#actionEditModal .action_box').empty();
		if(actionInfo[key]) {
			for (var action_type in actionInfo[key]) {
				for (var i in actionInfo[key][action_type]) {
					if (1 == action_type) {
						var tmpDom = agreeDom.clone().appendTo('#actionEditModal #agree');
						tmpDom.find('[name^=agree_action_name]').val(actionInfo[key][action_type][i]['action_name'])
						tmpDom.find('.dropdown-menu .select_item[data=' + actionInfo[key][action_type][i]['action_behavior'] + ']').click();
						tmpDom.find('[name^=agree_action_id]').val(actionInfo[key][action_type][i]['id']);
					}else if (2 == action_type) {
						var tmpDom = disagreeDom.clone().appendTo('#actionEditModal #disagree');
						tmpDom.find('[name^=disagree_action_name]').val(actionInfo[key][action_type][i]['action_name'])
						tmpDom.find('.dropdown-menu [data=' + actionInfo[key][action_type][i]['action_behavior'] + ']').click();
						tmpDom.find('[name^=disagree_action_id]').val(actionInfo[key][action_type][i]['id']);
					}
				}
			}
		}else {
			agreeDom.clone().appendTo('#actionEditModal #agree');
			disagreeDom.clone().appendTo('#actionEditModal #disagree');
		}
		
		ReSetActionDelBtn();
	});
	
	//添加动作项
	$('.add_action').click(function(){
		if($(this).hasClass('agree')) {
			agreeDom.clone().appendTo('#actionEditModal #agree');
		}else if($(this).hasClass('disagree')) {
			disagreeDom.clone().appendTo('#actionEditModal #disagree');
		}
		
		ReSetActionDelBtn();
	});
	
	$('#actionEditModal').delegate('.del_action', 'click', function(){
		$(this).parents('.select_input:first').remove();
		ReSetActionDelBtn();
	});
	
	//初始化规则编辑面板
	var ReSetEditRulePanel = function(){
		var prevDom = $('#ruleEdit #prev_node .select_input');
		var nextDom = $('#ruleEdit #next_node .select_input');
		
		if (prevDom.size() == 1) {
			prevDom.find('.del_rule').hide();
		}else {
			prevDom.find('.del_rule').show();
		}
		
		if(1 == nextDom.size()) {
			nextDom.find(':input[name^=rule]').attr('disabled', true).hide();
			nextDom.find(':hidden[name^=rid]').attr('disabled', true)
			nextDom.find('.del_rule').hide();
		}else {
			nextDom.find(':input[name^=rule]').removeAttr('disabled').show();
			nextDom.find(':hidden[name^=rid]').removeAttr('disabled');
			nextDom.find('.del_rule').show();
		}
	}
	
	//ReSetEditRulePanel();
	
	//var ruleDom = $('#ruleEdit .select_input:first').clone();
	
	var prevDom = $('#ruleEdit #prev_node .select_input:first').clone();
	var nextDom = $('#ruleEdit #next_node .select_input:first').clone();
	
	//弹出预处理
	$('.edit_process_rule').click(function(){
		var key = $(this).parents('.process_node:first').attr('pid');
		$('#ruleEdit :hidden[name=process_id]').val(key);
		
		$('#ruleEditModal .modal-title').text('编辑 〔 ' + processInfo[key]['process_name'] + ' ﹞ 上下级节点');
		//console.log(processInfo[key]);
		//隐藏自身节点
		prevDom.find('.dropdown-menu li').show().find('a[data=' + key + ']').parent().hide();
		nextDom.find('.dropdown-menu li').show().find('a[data=' + key + ']').parent().hide();
		
		$('#ruleEdit .select_input').remove();
		
		if (processInfo[key]['pre_process_ids'] || processInfo[key]['next_process_ids']) {
			for (var i in processInfo[key]['pre_process_ids_arr']) {
				prevDom.clone().appendTo('#ruleEdit #prev_node').find('.dropdown-menu li a[data=' + processInfo[key]['pre_process_ids_arr'][i] + ']').click();
			}
			
			for (var i in processInfo[key]['next_process_ids_arr']) {
				var tmp = nextDom.clone();
				tmp.appendTo('#ruleEdit #next_node').find('.dropdown-menu li a[data=' + processInfo[key]['next_process_ids_arr'][i] + ']').click();
				
				if (ruleInfo[key]) {
					tmp.find('[name^=rule]').val(ruleInfo[key][processInfo[key]['next_process_ids_arr'][i]]['rule']);
					tmp.find('[name^=rid]').val(ruleInfo[key][processInfo[key]['next_process_ids_arr'][i]]['id'])
				}
				
			}
		}else {
			prevDom.clone().appendTo('#ruleEdit #prev_node');
			nextDom.clone().appendTo('#ruleEdit #next_node');
		}
		
		ReSetEditRulePanel();
	});
	
	//添加规则
	$('.add_node_relation').click(function(){
		
		if ($(this).hasClass('prev')) {
			prevDom.clone().appendTo('#ruleEdit #prev_node');
		}else if($(this).hasClass('next')) {
			nextDom.clone().appendTo('#ruleEdit #next_node');
		}
		
		ReSetEditRulePanel();
		
		return false;
	});
	
	//删除规则
	$('#ruleEdit').delegate('.del_rule', 'click', function(){
		$(this).parents('.select_input:first').remove();
		ReSetEditRulePanel();
	});
	
	
});





//编辑节点面板widget初始化
/*$(function(){
	
	//下级节点
	//$( "select[name^=next_process_ids]" ).each(function() {
	//	$(this).combobox();
	//});
	$('.custom-combobox-toggle').addClass('glyphicon glyphicon-chevron-down').click(function(){
		$(this).blur();
	});
	
	var ReSetEditRulePanel = function(){
		var nextDom = $('#ruleEdit .next_process_ids');
		if(1 == nextDom.size()) {
			nextDom.find(':input[name^=rule]').attr('disabled', true).hide();
			nextDom.find('.removeRule').hide();
		}else {
			nextDom.find(':input[name^=rule]').removeAttr('disabled').show();
			nextDom.find('.removeRule').show();
		}
	}
	ReSetEditRulePanel();
	//删除规则
	$('#ruleEdit').delegate('.removeRule', 'click', function(){
		$(this).parents('.next_process_ids:first').remove();
		ReSetEditRulePanel();
	});
	//添加规则
	$('#add_rule').click(function(){
		var dom = $('#ruleEdit .next_process_ids:last').clone();
		dom.find('[name^=rule]').val('');
		dom.insertAfter('#ruleEdit .next_process_ids:last');
		
		ReSetEditRulePanel();
		
		return false;
	});
	//编辑规则
	$('.edit_process_rule').click(function(){
		var key = $(this).parents('.process_node:first').attr('pid');
		$('#ruleEdit :hidden[name=process_id]').val(key);
		
		if (!ruleInfo[key]) {
			ReSetEditRulePanel();
		}
	});
});*/