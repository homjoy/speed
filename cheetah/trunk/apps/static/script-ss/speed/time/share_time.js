fml.define('speed/time/share_time', ['jquery', 'plugin/tokeninput', 'component/notify'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var notify = require('component/notify');


    /**
     * 插件引用
     */
    var share_id = $('.share_id');
    $(share_id).tokenInput('/aj/address/ajax_search_name', {
        prePopulate: '',
        tokenLimit: 1
        //resultsFormatter:function(data){
        //    console.log(data);
        //    return "<li>"+data.user_id+"</li>"
        //},

    });
    // 添加
    $('.add').click(function () {
        if (!!$(share_id).val()) {
            var myForm = $('.add_form').serializeArray();
            $.post('/aj/share_time/user_share_add', myForm, function (ret) {
                //console.warn(ret);
                if (ret.code == 200) {
                    notify.success('操作成功');
                    setTimeout(function () {
                        location.reload();
                    }, 2000)

                } else if (ret.code == 400 || ret.code == 500) {
                    notify.error(ret.error_msg || '操作失败');
                }
            }, 'json');
        } else {
            notify.error('操作失败，请输入共享人');
        }
    });
    $('.update').click(function () {
        var data = $(this).data();
        $.post('/aj/share_time/user_share_update', data, function (ret) {
            console.warn(ret);
            if (ret.code == 200) {
                notify.success('操作成功');
            } else if (ret.code == 400 || ret.code == 500) {
                notify.error(ret.error_msg || '操作失败');
            }
        }, 'json');
    });
    // 点击查看修改
    $('.control-label').click(function () {
        var is_write = $(this).find('input').val();
        // 更改按钮上的数据
        $(this).parent().next().find('.update').data('is_write', is_write);
    });
    $('.delete').click(function () {
        $('#notice_time').modal('show');
        var id = $(this).data().id;

        $('.delete_sure').data('id', id);

    });
    $('.delete_sure').click(function () {
        var data = $(this).data();
        $.post('/aj/share_time/user_share_delete', data, function (ret) {
            console.warn(ret);
            if (ret.code == 200) {
                notify.success('删除成功');
                var dataId = data.id;
                $('[data-id=' + dataId + ']').parent().parent().remove();
            } else if (ret.code == 400 || ret.code == 500) {
                notify.error(ret.error_msg || '删除失败');
            }
        }, 'json');
    });
});