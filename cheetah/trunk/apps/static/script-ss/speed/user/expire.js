fml.define('speed/user/expire', ['jquery', 'plugin/cookie','component/notify', 'component/GridInput'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), notify = require('component/notify'),GridInput = require('component/GridInput');

    $("form").on("submit", function (e) {
        e.preventDefault();
        return false;
    });

    /**
     * 切换卡片.
     */
    $("[data-switch]").on("click", function () {
        var corner = $(".card-corner"),
            target = $(this).attr('data-switch');
        $(".card.active").removeClass('active');
        $('.' + target).addClass('active');

        if (target == 'card-expire') {
            corner.removeClass('corner-pc');
            corner.attr("data-switch", 'card-download');
        } else {
            corner.addClass('corner-pc');
            corner.attr("data-switch", 'card-expire');
        }
    });


    var verifyInput = new GridInput("input[name=verifyCode]", {
        //onInput: checkVerifyCode
    });

    /**
     * 输入检查，并自动提交.
     * @param code
     * @returns {boolean}
     */
    function checkVerifyCode(code) {
        //未输入或者长度不够6位.
        if (!code || code.length !== 6) {
            verifyInput.focus();
            return false;
        }

        $.post('/aj/user/get_mfa_status', {code: code}, function (resp) {
            if (resp.code != 200) {
                notify.error(resp.error_msg || '验证出错.');
                return false;
            }

            //启用，继续校验..
            if (resp.data && resp.data.status == 2) {
                checkIsValid(code);
            } else {
                window.location.href = '/user/login/';
            }
        }, 'json');
    }

    //检查是否有效
    function checkIsValid(code) {
        $.post('/aj/user/expire_verify', {code: code}, function (resp) {
            if (resp.code != 200) {
                return false;
            }
            //会到之前的页面.
            window.location.href = '/';
        }, 'json');
    }

    /**
     * 点击验证
     */
    $(".btn-sign-in").on('click', function () {
        var value = verifyInput.val();
        if (!value || value.length != 6) {
            //抖动提示.
            $(".grid-input").addClass('bounceIn animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $(this).removeClass('bounceIn animated');
            });
        }

        checkVerifyCode(value);
    });

    /**
     * 如何获取.
     */
    $("#how-to-get").on('click',function(){
        $("#how-to-get-tips").removeClass('bounceOut').addClass('active bounceIn');
    });
    $("#how-to-get-tips").on("click",function(){
        $(this).addClass('bounceOut').removeClass('bounceIn active');
    });
});