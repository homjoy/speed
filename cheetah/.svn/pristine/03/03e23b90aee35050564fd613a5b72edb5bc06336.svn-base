fml.define('fms/report/reimburse/base', ['jquery', 'plugin/bootstrap/datepicker', 'plugin/tokeninput', 'component/trees', 'component/notify', 'component/TreeSelect'], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');

    if ($("input[name=departId]").length) {
        /**
         * 加载部门树.
         */
        $.post('/aj/query/depart', function (data) {
            $("input[name=departId]").TreeSelect({
                multiple: true,
                cascading: true,
                data: data
            });
        }, 'json');
    }

    if ($("input[name=userId]").length) {
        /**
         * 加载部门树.
         */
        $("input[name=userId]").tokenInput("/aj/user/search", {
            tokenLimit: 1
        });
    }

    $('.input-daterange').datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose: true
    });

    $(".search-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var url = $form.attr("action");
        var btn = $form.find('.btn-search');
        btn.button('loading');

        $.ajax(url, {
            type: 'post',
            data: $form.serialize(),
            dataType: 'json',
            success: function (response) {
                btn.button('reset');
                if (response.rcode != 200) {
                    return notify.error(response.rmessage);
                }
                $(".report-content").html(response.data);
            }
        });

        return false;
    });


    /**
     *
     */
    $(".report-content").on("click", 'tr > td:last-child > a', function (e) {
        var btn = $(this);
        var userId = btn.attr('data-user-id');
        if (btn.hasClass('hasten-repayment')) {
            return notify.error('已催还.');
        }

        if (userId) {
            $.post('/aj/loan/hasten_repayment', {userid: userId}, function (resp) {
                if (resp.rcode != 200) {
                    return notify.error(resp.rmessage || '出错了.');
                }

                btn.addClass('hasten-repayment');
                notify.success(resp.rmessage);
            }, 'json');
        }
    });
});