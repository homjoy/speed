fml.define("fex/doc/notify", ['jquery', 'component/notify'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var notify = require('component/notify');


    //操作反馈
    var messageText = {
        "success": "恭喜恭喜，你要干的事成功了。",
        "warning": "桥到嘛太！好像有什么东西不对~",
        "error": "悲剧了，服务出错了。"
    };

    $(".feedback-message").on('click', function () {
        var type = $(this).attr('data-type');
        var message = messageText[type] || '';

        notify[type].call(this, message);
    });


    var k = [{icon: "fa fa-info fa-lg", title: "Info", type: "info"}, {
        icon: "fa fa-star fa-lg",
        title: "Primary",
        type: "primary"
    }, {icon: "fa fa-thumbs-up fa-lg", title: "Success", type: "success"}, {
        icon: "fa fa-bolt fa-lg",
        title: "Warning",
        type: "warning"
    }, {icon: "fa fa-times fa-lg", title: "Danger", type: "danger"}, {
        icon: "fa fa-leaf fa-lg",
        title: "Mint",
        type: "mint"
    }, {icon: "fa fa-shopping-cart fa-lg", title: "Purple", type: "purple"}, {
        icon: "fa fa-heart fa-lg",
        title: "Pink",
        type: "pink"
    }, {icon: "fa fa-sun-o fa-lg", title: "Dark", type: "dark"}];

    $('#demo-nifty-noty').on("click", function (e) {
        e.preventDefault();
        var t = nifty.randomInt(0, 8);
        $.niftyNoty({
            type: k[t].type,
            icon: k[t].icon,
            title: k[t].title,
            message: "提醒内容",
            container: ".floating-container",
            timer: 3500
        })
    });


    $('#demo-page-alert').on("click", function (e) {
        e.preventDefault();
        var t = nifty.randomInt(0, 8), n = function () {
            return nifty.randomInt(0, 5) < 4 ? 3e3 : 0
        }();
        $.niftyNoty({
            type: k[t].type,
            icon: k[t].icon,
            title: (n > 0 ? "这是一个会自动关闭的提醒" : "这是一个一直固定的提醒.."),
            message: "这里是一段很长很长的提醒内容。。。。。。",
            timer: n,
            container: '#page-alert'
        });
    });


    $("#feedback-cancel-modal").on('click',function(e){
        e.preventDefault();
        notify.cancel(function(result){
            if(result){
                notify.success('点击了确定.');
            }else{
                notify.warning('点击了取消.');
            }
        });
    });

    $("#feedback-decline-modal").on('click',function(e){
        e.preventDefault();
        notify.decline(function(result){
            if(result){
                notify.warning('不要拒绝我好吗....');
            }else{
                notify.success('没有拒绝我真的是太好了~~');
            }
        });
    });


    $("#approval-modal").on('click',function(e){
        e.preventDefault();
        notify.approval('弹框标题','<form class="form-horizontal"><textarea class="form-control">审批内容，可以放表单什么的，自己看着办.</textarea></form>',{
            'success':function() {
                notify.success('确定通过什么的了.');
            },
            'cancel':function() {
                notify.success('取消了。。这样子好吗.');
            }
        });
    });

});