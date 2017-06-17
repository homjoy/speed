fml.define('speed/user/avatar', ['jquery', 'plugin/imagecropper','component/notify'], function (require, exports) {

    var $ = require('jquery');
    var ImageCropper = require('plugin/imagecropper');
    var notify = require('component/notify');

// 头像上传
    var cropper;
    var result; //全局数据，用于接收上传返回的消息
    var upflag = false; //是否可以执行上传操作

    //初史化

    cropper = new ImageCropper(200, 200, 180, 180);
    cropper.setCanvas("cropper");
    cropper.addPreview("preview100");
    cropper.addPreview("preview50");
    if (!cropper.isAvaiable()) {
        alert("亲，您的浏览器过时啦，赶紧升级为Firefox3.6+ or Chrome10+，更有炫酷体验！");
    }
    //初史化提示信息
    $("upRemind").show();

    //选择区域
    $('#input').on('change', function () {
        selectImage($('#input')[0].files[0]);
    });
    function selectImage(fileList) {

        $("#upRemind").hide();

        var oFile = $('#input')[0].files[0];
        if (oFile.size > 20 * 1024 * 1024) {
            $("#errors")[0].innerHTML = "<font color='red'>不能超过20M,您上传的是：" + (oFile.size / (1024 * 1024)).toFixed(2) + "M</font>";
            return;
        }

        var rFilter = /^(image\/jpeg|image\/png|image\/gif)$/i;
        if (!rFilter.test(oFile.type)) {
            $('#errors')[0].innerHTML = "<font color='red'>请选择图片上传</font>";
            return;
        }
        console.warn(fileList);
        cropper.loadImage(fileList);
        $("#errors")[0].innerHTML = ""; //清空错误消息
        //提交表单，保存图片
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (e) {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var ret = $.parseJSON(xhr.responseText);
                    if(!ret){
                        notify.error('头像上传失败.');
                        return false;
                    }
                    $.getJSON('/aj/user/user_avatar_upload', {'url': ret.data}, function (data) {

                        result = data;
                        console.log(result);
                        if (data.code != 200) {
                            //上传失败
                            $("#errors")[0].innerHTML = "<font color='red'>" + data.error_msg + "</font>";
                            //$("#upRemind").show();
                            upflag = false;
                        } else {
                            //头像上传正确，隐藏提示信息
                            //	$("#upRemind").hide();
                            upflag = true;
                        }
                    });

                }
            }
        };
        xhr.open("post", "/upload/picture", true);
        var formData = new FormData(document.getElementById('upload_form'));
        formData.append('type','avatar');
        xhr.send(formData); //提交表单
    }

    //切图片
    $('#saveBtn').click(function () {

        // if(2 == result.status) {
        // 	//像素不符合
        // 	$("#errors")[0].innerHTML = "<font color='red'>图片像素不能小于400*400</font>";
        // 	return;
        // }
        console.log("//////");
        console.log(result);
        if (!upflag) {
            //文件格式不正确或没有选择上传文件
            $("#errors")[0].innerHTML = "<font color='red'>请先选择头像</font>";
            return;
        }

        var ratio = (200 - cropper.imageViewLeft * 2) / (result.data.info.width); //取得切图比例
        var px = (cropper.cropLeft - cropper.imageViewLeft) / ratio;
        var py = (cropper.cropTop - cropper.imageViewTop) / ratio;
        var w = cropper.cropViewWidth / ratio;
        var h = cropper.cropViewHeight / ratio;
        var src = result.data.data;
        $.ajax({
            type: 'POST',
            url: "/aj/user/user_avatar_save",
            data: {
                avatar_x: px,
                avatar_y: py,
                avatar_w: w,
                avatar_h: h,
                avatar_src: src
            },
            success: function (result) {
                notify.success('保存成功！');
                setTimeout(function () {
                    "use strict";
                    window.location.reload();
                }, 2000);
            },
            dataType: 'json'
        });
    });

    $('#myphoto').click(function () {
        init();
        $("#upRemind").show();
        $("#errors")[0].innerHTML = "";
    });


});