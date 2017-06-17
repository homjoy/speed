function show_message(code, data){
	//if (message == '') {
	//	return false;
	//};
	//$('#message-container').html("<strong>"+message+"</strong>");
	
	$('.modal-dialog button.close').click();
	
	if (code != 200) {
		
		var msg = "<p><strong>" + data.error_msg + "</strong></p>";
		for (var i in data.error_detail) {
			msg += '<p>&nbsp;&nbsp;&nbsp;&nbsp;' + i + ': ' + data.error_detail[i] + '</p>';
		}
		$('#message-container').html(msg);
		
		$('#message-container').removeClass("alert-success");
		$('#message-container').addClass("alert-danger");
	}else {
		
		$('#message-container').html("<p><strong>操作成功！</strong></p>");
		
		$('#message-container').removeClass("alert-danger");
		$('#message-container').addClass("alert-success");
	};
	$('#message-container').slideDown();
	setTimeout(hide_message, 3500);
}

function hide_message(){
	$('#message-container').slideUp();
}

function show_loading(){
	$('#loading-container').show();
}

function hide_loading(){
	$('#loading-container').hide();
}

//submit form
$(function () {

	$("#btn_submit").click(function(){

		var _this = $(this);
		show_loading();
		var options = { 
			beforeSubmit:  function showRequest() {
				_this.addClass("disabled");
				$(".form-control").attr("disabled","disabled");
			},
			success:function showResponse(data)  {

				hide_loading();
				if(data.code == '200'){
					if (data.url) {
						show_message(data.code, data);
						setTimeout("window.location='"+data.url+"';",3500);
					}else if(typeof callback_func == 'function') {
						callback_func(data);
					}else {
						show_message(data.code, data);
						setTimeout("window.location.reload(true);",2500);
					}
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					
					return true;
				}else if (data.code == '400') {
					if (typeof callback_error == 'function') {
						callback_error();
					}
					show_message(data.code, data);
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					
					return false;
				}else{
					show_message(data.code, data);
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					return false;
				}
			},
			type:      'post',
			dataType:  'json',
			timeout:   30000 
		};

		$('#myForm').ajaxSubmit(options);
		return false;
	});
	
	
	$(".btn_submit").click(function(){

		var _this = $(this);
		show_loading();
		var options = { 
			beforeSubmit:  function showRequest() {
				_this.addClass("disabled");
				$(".form-control").attr("disabled","disabled");
			},
			success:function showResponse(data)  {

				hide_loading();	
				if(data.code == '200'){
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					
					if (data.url) {
						show_message(data.code, data);
						setTimeout("window.location='"+data.url+"';",3500);
					}else if(typeof callback_func == 'function') {
						callback_func(data);
					}else {
						show_message(data.code, data);
						setTimeout("window.location.reload();",2500);
					}
					
					
					return true;
				}else if (data.code == '400') {
					
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					
					show_message(data.code, data);
					
					return false;
				}else{
					
					_this.removeClass("disabled");
					$(".form-control").removeAttr("disabled");
					
					show_message(data.code, data);
					
					return false;
				}
			},
			type:      'post',
			dataType:  'json',
			timeout:   30000 
		};
		
		var formId = $(this).attr('formId');

		$('#' + formId).ajaxSubmit(options);
		
		return false;
	});

	//$("#btn_back").click(function(){
	//	history.back();
	//});

	// notice
//    htmlobj = $.ajax({url:"/dashboard/ajax_view_notice",async:false});
//	$("#task_notice").html(htmlobj.responseText);
//	$("#task_notice").slideDown(1000);
});

//set cookie
function setCookie(c_name, value, expiredays){
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie=c_name+ "=" + escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+";path=/";
}
//read cookie
function getCookie(c_name){
    if (document.cookie.length>0){
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1){
            c_start=c_start + c_name.length+1
            c_end=document.cookie.indexOf(";",c_start)
            if (c_end==-1) c_end=document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}

/*widget  select input*/
$('#container').delegate('.select_input a.select_item','click', function(){
	var data = $(this).attr('data');
	$(this).parents('.btn-group:first').find('input:hidden').val(data);
	
	var btn_group = $(this).parents('.btn-group');
	if (btn_group.hasClass('open')) {
		btn_group.find('button.dropdown-toggle').click()
	}
	
	btn_group.find('.btn-default:first').text($(this).text());
	
	return false;
});

/*widget  nav_tab_widget*/
$('.nav_tab_widget').each(function() {
	$(this).find('a').click(function(){
		var curLi = $(this).parent().addClass('active');
		var allLi = curLi.siblings('li').removeClass('active').andSelf();
		var idx = parseInt(allLi.index(curLi));
		var panelClass = $(this).attr('panel-class');
		
		$('.' + panelClass).hide().eq(idx).show();
		
		$(this).blur();
		return false;
	});
});