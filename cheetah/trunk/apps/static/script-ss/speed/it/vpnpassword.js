fml.define('speed/it/vpnpassword', ['jquery', 'plugin/bootstrap/validator','component/notify','speed/common/sendmessage'], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');


    var url = "/aj/it/save_vpn_password";


    $('.new_password').focus(function () {
        $('.password_notice').removeClass('hide');
    }).blur(function () {
        $('.password_notice').addClass('hide');
    });

    $('#form_password_fix').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {

            //$('.save_basic_info').button('loading').delay(3000);
            var myForm = $('#form_password_fix').serializeArray();
            console.log(myForm);
            $.post(url, myForm, function (ret) {
                console.warn(ret);
                if (ret.code == 200) {
                    notify.success('操作成功');
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg);
                }
                $('.save_basic_info').button('reset');
            }, 'json');
            return false;
        }

    });
    $('.new_password_again').on('blur',function(){
        if(!expand()){
            $('.new_password').on('blur', function () {
                expand();
            })
        }
    })
    function expand() {
        var new_password = $('.new_password').val(),
            new_password_again = $('.new_password_again').val();
        if (new_password == new_password_again) {
            console.log('1',new_password.length);
            return true;
        } else {
            notify.error('两次密码输入不一致，请检查后提交');
            return false;
        }
    }
    $('.new_password,.new_password_again').on('blur',function(){
        if($(this).val().length<6){
            notify.error('密码长度不足6位哦');
        }
    });
    $('.send_message').sendmessage();

});