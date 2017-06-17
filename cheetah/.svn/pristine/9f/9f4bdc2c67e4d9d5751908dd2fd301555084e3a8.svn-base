fml.define("fex/doc/upload", ['jquery', 'component/upload'], function (require, exports) {
    "use strict";
    var $ = require('jquery');

    var upload = $("#upload-btn").QueenUpload({
        action: "/upload/file",
        multiple: true,
        draggable: false,
        maxFileCount: 10,
        maxFileSize: 50000,
        allowTypes: ['jpg','png','doc','ppt'],
        //showProgressBar: false,
        //初始化文件列表（用于增加已经上传的文件列表，数据修改的时候会用得到）
        //existFiles: [
        //    {"identify": "123", "name": "图片1.jpg", "type": "jpg"},
        //    {"identify": "1235", "name": "图片2.jpg", "type": "png"}
        //],
        onSuccess: function (response, file) {
            //response 是上传接口的返回数据
            //file是文件对象.
            console.log(response, file);
        },
        onError: function (message, file) {
            console.log(message, file);
        },
        //onProgress: function (percent, file) {
        //    console.log(percent, file);
        //},
        onFileRemove: function (file) {
            console.log(file);
        }
    });
    //upload.QueenUpload('addExistFiles',{"identify": "123", "name": "医院证明.jpg", "type": "jpg"});
    //upload.QueenUpload('addExistFiles',{"identify": "123", "name": "医院证明.jpg", "type": "jpg"});



    var dndUpload = $("#dnd-upload-btn").QueenUpload({
        action: "/upload/file",
        multiple: true,
        draggable: true,
        maxFileCount: 10,
        maxFileSize: 50000,
        allowTypes: ['jpg','png','doc','ppt'],
        //showProgressBar: false,
        //初始化文件列表（用于增加已经上传的文件列表，数据修改的时候会用得到）
        //existFiles: [
        //    {"identify": "123", "name": "图片1.jpg", "type": "jpg"},
        //    {"identify": "1235", "name": "图片2.jpg", "type": "png"}
        //],
        onSuccess: function (response, file) {
            //response 是上传接口的返回数据
            //file是文件对象.
            console.log(response, file);
        },
        onError: function (message, file) {
            console.log(message, file);
        },
        //onProgress: function (percent, file) {
        //    console.log(percent, file);
        //},
        onFileRemove: function (file) {
            console.log(file);
        }
    });
    //dndUpload.QueenUpload('addExistFiles',{"identify": "123", "name": "医院证明.jpg", "type": "jpg"});
    //dndUpload.QueenUpload('addExistFiles',{"identify": "123", "name": "医院证明.jpg", "type": "jpg"});
});