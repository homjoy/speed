fml.define('speed/user/login', ['jquery', 'plugin/cookie', 'component/CountDown','plugin/artTemplate'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var Cookies = require('plugin/cookie'), CountDown = require('component/CountDown');
    var Template = require('plugin/artTemplate');

    $("form").on("submit", function (e) {
        e.preventDefault();
        return false;
    });
    //发短信
    var sendVerifyBtn = new CountDown('#send-verify-code', {
        storeKey: "login_count_down_time",
        ajax: {
            url: '/aj/user/send_login_verify',
            type: 'post',
            dataType: 'json'
        },
        onComplete: function (ret) {
            if (ret.code != 200) {
                $('.error-message').text(ret.error_msg || '发送验证码失败.');
                return false;
            }
            $('.error-message').text('');
        },
        getData: function () {
            var username = $("#username").val();
            if (!username) {
                $('.error-message').text('请填写用户名.');
                return false;
            }

            return {
                send_type: "sms",
                type: 6,
                username: $("#username").val(),
                password: $("#password").val()
            };
        }
    });
    //切换
    $("[data-switch]").on("click", function () {
        var corner = $(".card-corner"),
            target = $(this).attr('data-switch');
        $(".card.active").removeClass('active');
        $('.' + target).addClass('active');

        if (target == 'card-login') {
            corner.removeClass('corner-pc');
            corner.attr("data-switch", 'card-download');
        } else {
            corner.addClass('corner-pc');
            corner.attr("data-switch", 'card-login');
        }
    });
    //没有动态秘钥
    $('body').delegate('.link-no-miyao','click',function(){
        $('.first-show').remove();
        $('.second-show').addClass('active');
    });
//回到秘钥
    $('#returnmiyao').click(function(){
        $('input[name=mfa_code]').val('')
        $('.second-show').removeClass('active');
        var Html = Template('add');
        $('.addon-lock').after(Html)
    });
    //登陆方法
    function login() {
        var username = $('#username').val().replace(/(^\s*)|(\s*$)/g, "");
        var password = $('#password').val().replace(/(^\s*)|(\s*$)/g, "");
        var captcha = $('input[name=captcha]').val()?$('input[name=captcha]').val().replace(/(^\s*)|(\s*$)/g, ""):false;
        var mfa_code = $('input[name=mfa_code]').val()?$('input[name=mfa_code]').val().replace(/(^\s*)|(\s*$)/g, ""):false;

        if (!username || !password) {
            $('.error-message').text('用户名和密码不能为空');
            return false;
        }
        if (!captcha&&!mfa_code) {
            if($('input[name=mfa_code]').length){
                $('.error-message').text('请输入动态秘钥.');
                $('input[name=mfa_code]').focus();
                return false;
            }else{
                $('.error-message').text('请输入验证码.');
                $('input[name=captcha]').focus();
                return false;
            }
        }

        if(captcha.length != 6||mfa_code.length != 6){
            if($('input[name=mfa_code]').length&&mfa_code.length != 6){
                $('.error-message').text('秘钥格式为6位数字.');
                $('input[name=mfa_code]').focus();
                return false;
            }else if(!$('input[name=mfa_code]').length&&captcha.length != 6){
                $('.error-message').text('验证码格式为6位数字.');
                $('input[name=captcha]').focus();
                return false;
            }
        }

        $('.error-message').text('');

        var logindata = captcha?{
            "username": username,
            "password": password,
            "captcha": captcha
        }:{
            "username": username,
            "password": password,
            "mfa_code":mfa_code
        }
        $.post('/aj/user/login',logindata, function (res) {
            if (res.code != 200) {
                return $('.error-message').text(res.error_msg);
            } else {
                $('.error-message').text(''); //清空提示.
            }

            Cookies.remove('speed_token', {domain: 'meilishuo.com'});
            Cookies.remove('speed_token');
            Cookies.set('speed_token', res.data.speed_token, {expires: 365, domain: window.location.hostname});
            if(captcha){
                window.location.href = '/user/safe/secretkey';
            }else{
                window.location.href = '/home';
            }
        }, 'json');
    }

    /**
     * 登陆
     */
    $('.btn-sign-in').on('click', function () {
        login();
        return false;
    });


    //响应回车
    $(document).on('keydown', function (e) {
        var currKey = e.keyCode || e.which || e.charCode;
        if (currKey == 13) {
            login();
        }
    });

    //指引
    $('body').delegate('.howtoget-miyao','click',function(){

    //$('#howtoget-miyao').click(function(){
        var wizardHTML = '<div class="wizard-container">' +
            '<div class="wizard new-home"></div>' +
            '<a href="javascript:void(0);" class="wizard wizard-close-check-in"></a>' +
            '</div>';
        var wizard = $(wizardHTML);

        wizard.appendTo("body");
        wizard.on('click', function (e) {
            e.preventDefault();

            wizard.fadeOut(function () {
                $(this).remove();
            });
            return false;
        });
    });

});