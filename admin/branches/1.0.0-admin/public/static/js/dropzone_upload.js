//http://blog.csdn.net/yangxujia/article/details/36672917
var myDropzoneOptions = {
    url : '/task/Ajax_attachment_batch_upload',
    maxFilesize: 10,
    maxFiles : 6,
    dictMaxFilesExceeded: '最大只能上传6个', //超过最大文件数量的提示文本
    paramName : 'attachment',
    dictDefaultMessage: '将文件拖拽至此区域进行上传（或点击此区域）,最多只能上传6个',
    addRemoveLinks: true,
    dictRemoveLinks: "x",
    dictCancelUpload : '取消上传', //'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'
    dictRemoveFile : '取消上传',
       // clickable: dropzone,
    acceptedFiles : "image/*,application/pdf,.doc,.docx,.xlsx,.xls,.ppt,.pptx,.eml",
    init:function(){
    	
    	if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)){
    		//alert('你是使用IE');
    		$($(this.element).children()[1]).css('top','22px');
    		$($(this.element).children()[1]).children().css('top','20px');
    	}else if (navigator.userAgent.indexOf('Firefox') >= 0){
    		//alert('你是使用Firefox');
    		$($(this.element).children()[1]).css('top','65px');
    		$($(this.element).children()[1]).children().css('top','19px');
    	}else if (navigator.userAgent.indexOf('Chrome') >= 0){
    		//alert('你是使用Chrome');
    		$($(this.element).children()[1]).css('top','82px');
    		$($(this.element).children()[1]).children().css('top','0px');
    	}else{
    		//safari
    		//alert('你是使用其他的浏览器浏览网页！');
    		$($(this.element).children()[1]).css('top','85px');
    		$($(this.element).children()[1]).children().css('top','0px');
    	}
    	
        var arr = [];
        this.on('success',function(file,Data){
            var result = JSON.parse(Data);
            arr.push(result.data);
            $("#attachment").attr('value', arr);

            var dom = $(this.element).children();
            for(var i = 2; i< dom.length; i++){
                if($($($(dom[i]).children()[0]).children()[0]).children().text() == file.name){
                    $(dom[i]).attr('value',result.data);
                }
            }
        });
        this.on('addedfile',function(file){
        	var spanDom = $($(this.element).children()[1]).children();
	        var divHeight = $(this.element).parent().css('height');
	        spanDom.css('height',divHeight);
        });
        this.on('removedfile',function(file){
            var _result;
            if (file.previewElement) {
              if ((_ref = file.previewElement) != null) {
                _result = $(file.previewElement).attr('value');
              }
            }
            var str = $("#attachment").val();
            var arr = new Array();
            arr = str.split(',');
            for(var i = 0; i< arr.length; i++){
                if(arr[i]==_result){
                    arr.splice(i,1);
                }
            }
            $("#attachment").attr('value', arr);
            
            var spanDom = $($(this.element).children()[1]).children();
	        var divHeight = $(this.element).parent().css('height');
	        spanDom.css('height',divHeight);
        });
    }
}; 

var myDropzone = new Dropzone("#dropzone", myDropzoneOptions);


