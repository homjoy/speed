fml.define('speed/it/wifi', ['jquery', 'plugin/bootstrap/validator', 'plugin/store', 'component/notify','component/select'], function (require, exports) {
    var $ = require('jquery');
    var store = require('plugin/store');
    var notify = require('component/notify');

    var LAST_VISIT_TAB = 'last-visit-tab';
    var tabId = store.get(LAST_VISIT_TAB);
    tabId && $('a[href='+tabId+']').click();

    $(".nav-tabs  > li > a").on("click",function(){
        store.set(LAST_VISIT_TAB,$(this).attr('href'));
    });

    //自写下拉框
    $("#expire_time").select({
        'placeholder': "请选择"
    }).on('change',function(){

    });


    $('#form_password_fix').bootstrapValidator({
        container: '#cc'
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        if (expand()) {
            //$('.save_basic_info').button('loading').delay(3000);
            var myForm = $('#form_password_fix').serializeArray();
            $.post('/aj/it/create_visitor_wifi', myForm, function (ret) {
                if (ret.code == 200) {
                    notify.success('操作成功');
                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg || '操作失败');
                }
                $('.save_basic_info').button('reset');
            }, 'json');
            return false;
        }
    });

    function expand() {
        return true;
    }

    //禁用
    var data;
    $('.btn-warning').click(function (e) {
        e.stopPropagation();
        data = $(this).data();
        $('#notice_bear').modal('show');
    });

    $('.bear').click(function () {
        $.post('/aj/it/disable_visitor_wifi',data, function (ret) {
            if (ret.code == 200) {
                notify.success('操作成功');
                window.location.reload();
            } else if (ret.code == 400 || ret.code == 500) {
                notify.error( ret.error_msg || '操作失败');
            }
            $('.save_basic_info').button('reset');
        }, 'json');
    });
    $('.agree').click(function (e) {
        e.stopPropagation();
        data = $(this).data();
        $('#agree-modal').modal('show');
    });
    $('.agree-modal-btn,.reject').click(function () {
        var _this=this;
        if($(_this).hasClass('reject')){
            data = $(_this).data();
        }else{
            if(!$('#expire_time').val()){
                notify.error('操作成功');
            }
            data.expire_time = $('#expire_time').val();
        }
        $.post('/aj/it/visitor_wifi_approve', data, function (ret) {
            if (ret.code == 200) {
                notify.success('操作成功');
                window.location.reload();
            } else if (ret.code == 400 || ret.code == 500) {
                notify.error( ret.error_msg || '操作失败');
            }
            $('.save_basic_info').button('reset');
        }, 'json');
    });
});