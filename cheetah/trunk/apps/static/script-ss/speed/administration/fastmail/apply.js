fml.define("speed/administration/fastmail/apply", ['jquery','plugin/artTemplate','component/select', 'plugin/bootstrap/datetimepicker','component/notify','plugin/bootstrap/validator','plugin/bootbox' ], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
    var bootbox = require('plugin/bootbox');

    $(".select").select({
        'placeholder': "请选择"
    }).on('change',function(){
        var _this = this;
        if($(this).hasClass('post_place')){
            setTimeout(function(){
                $.getJSON('/aj/administration/express_request', {place:$(_this).prev().find('span').html()}, function (ret) {
                    if (ret.code == 200) {
                        var option = '<option value="">请选择</option>';
                        $.each(ret.data.express,function(k,v){
                            option += '<option value="'+v+'">'+v+'</option>';
                        });
                        $('.express_company').html(option).data('queen-select').sync();
                        $('p.pink-word').html(ret.data.process);
                        $('.pink-word-row').removeClass('hide');
                    } else if (ret.code == 400 || ret.code == 500) {
                        notify.success('操作失败');
                    }
                    $('.save_password').removeAttr('disabled');
                });
            },500);
        }else{

        }
    });
    $('form').delegate('.minusdiv','mouseover',function(){
        $(this).find('a').removeClass('hide');
    }).delegate('.minusdiv','mouseleave',function(){
        $(this).find('a').addClass('hide');
    });
    var addrow = '<div class="row form-group minusdiv">'+$('.add-btn-row').prev().html()+'</div>';
    $('.add-btn').click(function(){
        $('.save_password').removeAttr('disabled');
        $('.add-btn-row').before(addrow);
    });

    $('.speed').delegate('.btn-plus','click',function(){
        $(this).parent().parent().remove();
        $('.save_password').removeAttr('disabled');
    });

    //表单提交
    $('#form_leave').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {
            console.log(444);
            var myForm = $('#form_leave').serializeArray();
            $.post('/aj/administration/express_submit', myForm, function (ret) {
                if (ret.code == 200) {
                    notify.success('操作成功');
                    setTimeout(function(){
                        window.location.href='/administration/fastmail/my?autuloadlast';
                    },2000);
                } else if (ret.code == 400 || ret.code == 500) {

                }
                $('.save_password').removeAttr('disabled');
            }, 'json');
            return false;
        }
    });
    //提交前校验
    function expand(){
        var flag = false;
        if(!$('.post_place').val()){
            notify.error('请填写寄出地');
        }else if(!$('.express_company').val()){
            notify.error('请填写快递公司');
        }else if($('.detail-num').length==0){
            notify.error('请添加一个快递单号');
        }else{
            $.each($('.detail-num'),function(k,v){
                console.log($(v).val());
               if(!$(v).val()){
                   notify.error('请填写快递单号');
                   flag=false;
                   return false;
               }else{
                   flag=true;
               }
            });
        }
        return flag;
    }
    $('.speedim').click(function () {
        window.location.href = "speedim://open/";
    });
});