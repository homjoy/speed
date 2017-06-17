fml.define("speed/administration/businesscard/apply", ['jquery','plugin/artTemplate', 'plugin/moment','component/select', 'plugin/bootstrap/datetimepicker','component/notify','plugin/bootstrap/validator','component/upload','plugin/bootbox' ], function (require, exports) {
    var $ = require('jquery');
    var moment = require('plugin/moment');
    var notify = require('component/notify');
    var bootbox = require('plugin/bootbox');

    $(".select").select({
        'placeholder': "请选择"
    }).on('change',function(){
        $('.save_password').removeAttr('disabled');
    });

    $('.blue-word').click(function(){
        console.log(1);
        $('.wizard-container').fadeIn(function(){
                $(this).removeClass('hide');
            }
        );
    });
    $('.wizard-container').click(function(){
        $('.wizard-container').fadeOut(function(){
                $(this).addClass('hide');
            }
        );
    });

    //表单提交
    $('#form_leave').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {
            var myForm = $('#form_leave').serializeArray();
            $.post('/aj/administration/card_submit', myForm, function (ret) {
                //console.log(1);
                if (ret.code == 200) {
                    notify.success('操作成功');
                    setTimeout(function(){
                        window.location.href='/administration/businesscard/my?autuloadlast';
                    },2000);
                } else if (ret.code == 400 || ret.code == 500) {

                }
                $('.save_basic_info').button('reset');
            }, 'json');
            return false;
        }
    });
    //提交前校验
    function expand(){
        if(!$('.select').val()){
            notify.error('请选择工作地点');
        }else if(!$('.job').val()){
            console.log($('.job').val());
            notify.error('请填写职位信息');
        }else{
            return true;
        }
    }
    $('.speedim').click(function () {
        window.location.href = "speedim://open/";
    });
});