fml.define('speed/user/index', ['jquery', 'plugin/bootstrap/validator', 'plugin/bootbox','component/select','component/notify'], function (require, exports) {
    var $ = require('jquery');
    var bootbox = require('plugin/bootbox');
    var notify = require('component/notify');

    //保存获取的美丽说用户信息.
    var lastUserInfo = {};
    var originMlsId = $("input[name=mls_id]").val() || 0;
    var lastMlsId = 0;

    //查看帮助
    $('.how-to-get').on("click", function (e) {
        //取消事件冒泡
        e.stopPropagation();
        $('.how-to-get-tips').fadeToggle();//toggle().toggleClass('in');
    });

    //点提示框跳转不关闭
    $(".how-to-get-tips a").on('click', function (e) {
        e.stopPropagation();
    });

    $(document).on("click", function (event) {
        "use strict";
        var target = $(event.target);
        if (!target.hasClass('how-to-get') || target.parents('.how-to-get-tips').size() == 0) {
            $('.how-to-get-tips').fadeOut();
        }
    });

    // 表单验证
    $('#form-userinfo').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        $('.position').val($('.floor-text').html()+$('.area-text').html()+$('.station').html());
        var mlsId = $input.val();
        if (!mlsId || !/\d+/.test(mlsId)) {
            notify.error('美丽说ID 填写不正确');
            return false;
        }
        if(!$('#coatcolor').val()||!$('#coatsize').val()){
            notify.error('请确认工服信息是否填写完整');
            return false;
        }
        //if(!$('#floor').val()||!$('#area').val()||!$('#station').val()){
        //    notify.error('请确认工位信息是否填写完整');
        //    return false;
        //}
        //至少获取一次.
        //初始ID 为空.
        if (!originMlsId && $.isEmptyObject(lastUserInfo)) {
            queryMlsUserInfo(confirmMllId);
        } else {
            //美丽说ID 没变化. 跳过确认，直接更新.
            if (originMlsId == mlsId) {
                updateUserInfo();
            } else {
                confirmMllId();
            }
        }
        return false;
    });

    /**
     * 确认美丽说ID.
     */
    function confirmMllId() {
        "use strict";
        var mls_id = $('input[name=mls_id]').val();
        var nickname = $('input[name=mls_nickname]').val();
        var avatar = $('.mls-avatar img').attr('src');
        bootbox.dialog({
            title: '提交确认',
            className: 'submit-confirm',
            closeButton: false,
            message: '<div class="media">' +
            '<div class="media-left">' +
            '<img class="media-object img-md bord-all" src="' + avatar + '" alt="' + nickname + '"></div>' +
            '<div class="media-body">' +
            '<h4 class="text-thin">重要的事情说三遍！</h4>' +
            '当前美丽说ID:&nbsp;' + mls_id +
            '<br/>对应的昵称为:&nbsp;' + nickname +
            '</div>' +
            '</div>',
            buttons: {
                cancel: {
                    label: "我不确定",
                    className: "btn-default",
                    callback: function () {
                        $('.save_basic_info').prop('disabled', false);
                    }
                },
                confirm: {
                    label: "我很确定",
                    callback: updateUserInfo
                }
            },
            animateIn: 'bounceIn',
            animateOut: 'bounceOut'
        });
    }


    $('#coat_size').focus(function () {
        $('.input-select').removeClass('hide');
    }).blur(function () {
        setTimeout(function () {
            $('.input-select').addClass('hide');
        }, 100);

    }).next().children().click(function (e) {
        var coat_size = $(this).html();
        $('#coat_size').val(coat_size);
    });


    /**
     * 更新用户信息.
     */
    function updateUserInfo() {
        "use strict";

        //修改时重新记录美丽说ID.
        originMlsId = $("input[name=mls_id]").val();
        $('.save_basic_info').button('loading').delay(3000);
        var myForm = $('#form-userinfo').serializeArray();
        $.post('/aj/user/save_info', myForm, function (ret) {
            if (ret.code == 200) {
                notify.success('操作成功');
            } else if (ret.code == 400 || ret.code == 500) {
                notify.error(ret.error_msg || '操作失败');
            }
            $('.save_basic_info').button('reset');
        }, 'json');
    }


    var typingTimer;
    var doneTypingInterval = 150;
    var $input = $("#form-userinfo input[name=mls_id]");

    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(queryMlsUserInfo, doneTypingInterval);
    });
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });

    $input.on('blur', function () {
        queryMlsUserInfo();
    });


    /**
     * 查询美丽说个人信息.
     * @returns {boolean}
     */
    function queryMlsUserInfo(callback) {
        "use strict";
        var mlsId = $input.val();
        if (!mlsId || !/\d+/.test(mlsId) || mlsId == lastMlsId) {
            return false;
        }
        lastMlsId = mlsId;
        $.getJSON('/aj/user/mls_user_info', {mls_id: mlsId}, function (resp) {
            if (resp.code != 200) {
                notify.error('获取美丽说账号信息失败，请检查输入是否有误.');
                return;
            }
            lastUserInfo = resp.data;

            $('input[name=mls_nickname]').val(resp.data.nickname || '');
            $('.mls-avatar img').attr('src', resp.data.avatar_o || '');
            if ($.isFunction(callback)) {
                callback(resp.data);
            }
        });
    }
    //工服尺码
    $('#coatsize,#coatcolor').select();
    //楼层，区域事件添加，插件绑定
    $('#floor').select().change(function(){
        var floor = $(this).val();
        console.log(floor);
        $('.floor-text').html('F'+floor);
    });
    $('#area').select().change(function(){
        var area = $(this).val();
        $('.area-text').html('-'+area);
    });
    $('#station').on('input',function(){
        var _this = this;
        setTimeout(function(){
            var value=$(_this).val();
            var length = 3-value.length;
            var addstr='';
            console.log(length);
            for(var i=0;i<length;i++){
                addstr +='0';
            }
            $('.station').html(addstr+value);
        },500)
    }).on('keyup',function(){
        $(this).val($(this).val().replace(/\D/g,''));
    }).on('afterpaste',function(){
        $(this).val($(this).val().replace(/\D/g, ''));
    });
});