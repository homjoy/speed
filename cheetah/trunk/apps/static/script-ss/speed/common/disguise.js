fml.define('speed/common/disguise',['jquery','plugin/cookie'],function(require,exports){
    "use strict";
    var $ = require('jquery');
    var Cookies = require('plugin/cookie');

    var btn = '<li><button data-target="#pretend-modal" data-toggle="modal" class="btn btn-success btn-xs">伪装</button></li>';
    var modal = '<div id="pretend-modal" class="modal fade" tabindex="-1">' +
        '<div class="modal-dialog modal-md">'+
        '<div class="modal-content">'+
        '<div class="modal-header">' +
        '<button class="close" data-dismiss="modal"><span>&times;</span></button>' +
        '<h4 class="modal-title">温馨提示</h4>' +
        '</div>' +
        '<div class="modal-body">' +
        '<form class="form-horizontal" role="form" onsubmit="return false">' +
        '<div class="form-group">' +
        '<label class="control-label col-sm-3">邮件前缀</label>' +
        '<div class="col-sm-8">' +
        '<input type="text" name="username" id="disguise-username" class="form-control">' +
        '</div>' +
        '</div>' +
        '<label class="notice"></label>' +
        '</form>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<button data-dismiss="modal" class="btn btn-default" type="button">关闭</button>' +
        '<button class="btn btn-primary" id="disguise-user">确定</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    $("#footer .footer-list").prepend(btn);
    $("body").prepend(modal);


    $('#disguise-user').on('click', function(){
        var username = $("#disguise-username").val();
        if(!username){
            alert('邮箱前缀不能为空.');
            return false;
        }
        //登陆
        $.post('/aj/user/disguise',{username:username},function(resp){
            if(resp.code == 200 && resp.data){
                var expire = resp.data.token_expire ? resp.data.token_expire / 86400 : 7;
                Cookies.set('speed_token',resp.data.speed_token,{domain:window.location.hostname,expire:expire});
                window.location.reload();
            }else{
                alert('伪装失败.');
            }
        },'json');
    });
});