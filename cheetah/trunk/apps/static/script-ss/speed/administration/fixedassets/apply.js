fml.define("speed/administration/fastmail/apply", ['jquery','plugin/artTemplate','component/select', 'plugin/bootstrap/datetimepicker','component/notify','plugin/bootstrap/validator','plugin/bootbox' ], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
    var bootbox = require('plugin/bootbox');
    var Template = require('plugin/artTemplate');

    var goods;
    $.getJSON('/aj/administration/assets_request',{}, function (ret) {

        if (ret.code == 200) {
            var option='';
            $.each(ret.data,function(k,v){
                option +='<option value="'+ v.good+'"'+ v.child_goods+'>'+ v.good+'</option>';
            })
            $('.addoption').after(option);
            var selectfun = function (_that) {
                $('.save_password').removeAttr('disabled');
                if($(_that).hasClass('first-level')){
                    var num = $(_that).data().order;
                    var option2='<select name="assets['+num+'][assets_brand]" class="second-level need-check">';
                    if(!$(_that).val()){
                        $(_that).parent().next().addClass('hide');
                        $(_that).parents('.form-group').find('.second-level-div').html('');
                    }
                    $.each(goods,function(key,val){
                        if(val.good==$(_that).val()){
                            if(val.child_goods!=undefined){
                                $.each(val.child_goods,function(a,b){
                                    option2+='<option value="'+b+'" class="option2">'+b+'</option>';
                                })
                                option2+='</select>';
                                $(_that).parents('.form-group').find('.second-level-div').html(option2).find('select').select({
                                    'placeholder': "请选择"
                                });
                                $(_that).parent().next().removeClass('hide');
                            }else{
                                $(_that).parent().next().addClass('hide');
                                $(_that).parents('.form-group').find('.second-level-div').html('');
                            }
                        }
                    })
                }else{

                }
            }

            //添加
            $('.add-btn').click(function(){
                $('.save_password').removeAttr('disabled');
                var a =$('.minusdiv').length+1
                console.log(a);
                var Html = Template('add', {num:a});
                $('.add-btn-row').before(Html);
                $('.minusdiv:last').find('.addoption').after(option);
                $('.minusdiv:last').find('.select').select({
                    'placeholder': "请选择"
                }).on('change',function(){
                    var _this = this;
                    selectfun(this)
                });
            });

            goods = ret.data;
            //选择联动
            $(".select").select({
                'placeholder': "请选择"
            }).on('change',function(){
                var _this = this;
                selectfun(this)

            });
            console.log(ret);
        } else if (ret.code == 400 || ret.code == 500) {

        }
    });
    $('.standard').popover({
        'placement': 'left',
        'trigger': 'focus',
        'delay': {show: 300, hide: 0},
        html: true,
        template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
        content: function () {
            var Html = Template('user_own_leave', {});
            return Html;
        }
    }).on("shown.bs.popover", function (e) {

    });

//单选框切换
    $('[name="apply_type"]').click(function(){
        if($(this).val()=='1'){
            $('#other').attr('placeholder','如设备故障，需要先找IT同学检修哦，如检测确实需要换新，请说明');
        }else{
            $('#other').attr('placeholder','先了解下配置标准吧，减少重复劳动O(∩_∩)O，超出标准的申请，详细说明原因哦！');
        }
    });

    //鼠标滑过删除按钮显示
    $('form').delegate('.minusdiv','mouseover',function(){
        $(this).find('a').removeClass('hide');
    }).delegate('.minusdiv','mouseleave',function(){
        $(this).find('a').addClass('hide');
    });

    //删除
    $('.speed').delegate('.btn-plus','click',function(){
        $(this).parent().parent().parent().remove();
        $('.save_password').removeAttr('disabled');
    });
    $('.speed').delegate('.minus','click',function(){
        var a = parseInt($(this).prev().val())-1;
        if (a !=0){
            $(this).prev().val(a);
        }else{
            notify.error('数量最少是一个哦，如需删除，请点右面删除按钮');
        }
    });

    $('.speed').delegate('.plus','click',function(){
        var a = parseInt($(this).next().val())+1;
        $(this).next().val(a);
    });
    //文字判断
    $('.speed').delegate('textarea','input',function(){
        $('.save_password').removeAttr('disabled');
    });
    //表单提交

    $('#form_sub').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {
            var myForm = $('#form_sub').serializeArray();
            console.log('ahaha')
            $.post('/aj/administration/assets_submit', myForm, function (ret) {
                if (ret.code == 200) {
                    notify.success('操作成功');
                    setTimeout(function(){
                        window.location.href='/administration/fixedassets/my?autuloadlast';
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
        var flag = true;
        if(!$('#other').val()){
            notify.error('其他说明木有填写呐');
            flag=false;
        }else{
            console.log(111)
            $.each($('.need-check'),function(k,v){
               if(!$(v).val()){
                   console.log(222)
                   notify.error('还有没选择的申请物品哟');
                   flag=false;
                   return false;
               }else{
                   console.log(333)
               }
            });
        }
        return flag;
    }
});