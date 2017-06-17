fml.define('speed/user/verification', ['jquery', 'plugin/bootstrap/validator','component/notify','speed/common/sendmessage'], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');

    $('.new_step').click(function(){
        if ($('.verificationcode').val()!='') {

            $('.new_step').button('loading').delay(300);
            var myForm = $('#form').serializeArray();
            console.log(myForm);
            $.post('/aj/user/verify_captcha', myForm, function (ret) {
                console.warn(ret);
                if (ret.code == 200) {
                    notify.success('操作成功');
                    $('.new_step,.oldmobile').hide();
                    $('.save_password,.newmobile').removeClass('hide');
                    $('.verificationcode').val('');
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg);
                    $('.new_step').button('reset');
                }
            }, 'json');
        }else{
            notify.error('嘿，写下验证码呗，么么哒(づ￣ 3￣)づ');

        }
    });

    var time;
    var quartz;
    $('.send_message2').click(function () {
        var _this = $(this);
        if($('.newmobileval').val()!=''){
            _this.attr('disabled', 'false');
            _this.html('正在发送...');
            time = 60;
            $.getJSON('/aj/user/send_sms_captcha', {send_type:'sms',mobile:$('.newmobileval').val()}, function (ret) {
                if (ret.code != 200) {
                    _this.removeAttr('disabled');
                    _this.html('获取验证码');
                    notify.error(ret.message);
                    return;
                } else {
                    quartz = setInterval(function () {
                        if (time > 0) {
                            _this.html('发送成功，' + time + '秒后可重试');
                            time--;
                        } else {
                            _this.removeAttr('disabled');
                            _this.html('获取验证码');
                            clearInterval(quartz);
                        }
                    }, 1000);
                } //else
            });
        }else{
            notify.error('没了主子的手机号，臣妾做不到啊');
        }
    });


    $('#form').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {

            $('.save_basic_info').button('loading').delay(3000);
            var myForm = $('#form').serializeArray();
            console.log(myForm);
            $.post('/aj/user/update_user_private_info', myForm, function (ret) {
                console.warn(ret);
                if (ret.code == 200) {
                    notify.success('操作成功');
                    setTimeout(function(){
                        window.location.reload();
                    },3000);
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg);
                }
                $('.save_basic_info').button('reset');
            }, 'json');
            return false;
        }

    });

    function expand() {
        var newmobileval = $('.newmobileval').val(),
            verificationcode = $('.verificationcode').val();
        if (newmobileval !=''&& verificationcode!='') {
            return true;
        } else {
            notify.error('不要漏填哦，请检查后提交');
            return false;
        }
    }

    $('.send_message').sendmessage({send_type:'sms'});

});