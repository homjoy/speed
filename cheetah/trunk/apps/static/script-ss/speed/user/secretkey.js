fml.define('speed/user/secretkey', ['jquery', 'plugin/bootstrap/validator','component/notify','speed/common/sendmessage','plugin/store'], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');
    var store = require('plugin/store');


    $('.new_step').click(function(){
        if ($('.verificationcode').val()!='') {

            $('.new_step').button('loading').delay(300);
            var myForm = $('#form').serializeArray();
            console.log(myForm);
            $.post('/aj/user/verify_captcha', myForm, function (ret) {
                console.warn(ret);
                if (ret.code == 200) {
                    notify.success('验证成功');
                    $('.tap').hide();
                    $('.second-row').removeClass('hide');
                    $('.dynamicqrcodeimg').attr('src','/user/dynamic_qrcode?refresh=1');
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg);
                    $('.new_step').button('reset');
                }
            }, 'json');
            //$.when().then();
        }else{
            notify.error('嘿，写下验证码呗，么么哒(づ￣ 3￣)づ');

        }
    });

    $('.submitcode').click(function(){
        if ($('.code').val()!='') {

            $('.new_step').button('loading').delay(300);
            $.post('/aj/user/save_mfa_status',{ code:$('.code').val()}, function (ret) {
                console.warn(ret);
                if (ret.code == 200) {
                    notify.success('操作成功');
                    $('.codesubdiv').hide();
                    $('.successdiv').removeClass('hide');
                    store.set('dynamic_qrcode',1);

                    setTimeout(function(){
                        window.location.reload();
                    },5000);
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg);
                    $('.new_step').button('reset');
                }
            }, 'json');
        }else{
            notify.error('嘿，你肿么啥都没填呢');

        }
    });

    $('.send_message').sendmessage({send_type:'sms'});

});