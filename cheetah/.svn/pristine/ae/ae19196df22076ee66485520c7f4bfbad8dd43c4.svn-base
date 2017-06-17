fml.define('fms/budget/view', ['jquery', 'component/notify', 'fms/common/utils'], function (require, exports) {
    var $ = require('jquery');

    var notify = require('component/notify');
    var utils = require('fms/common/utils');

    //@see http://fex-team.github.io/ueditor/#start-uparse
    //处理页面没有边框的BUG，
    //http://redmine.meilishuo.com/issues/40583
    var basePath = fml.getOption('modulebase');
    uParse('.ueditor-content', {
        rootPath: basePath + '/s/ueditor/'
    });


    $(document).on('click',".data-preview .btn",function(e){
        e.preventDefault();
        var btn = $(this);
        var div = btn.parent().next();
        if(div && div.length){
            div.toggle();
        }
        return false;
    });

    /**
     * 同意操作
     */
    $(".btn-agree").on("click", function (e) {
        e.preventDefault();
        var self = $(this);
        var budgetId = self.attr('data-id');
        var taskId = self.attr('data-taskId');
        var formKey = self.attr("data-formkey");
        var budgetType = self.attr("data-budget-type");
        approval('agree', formKey, budgetId, taskId, budgetType);
    });

    /**
     * 驳回操作
     */
    $(".btn-reject").on("click", function (e) {
        e.preventDefault();
        var self = $(this);
        var budgetId = self.attr('data-id');
        var taskId = self.attr('data-taskId');
        var formKey = self.attr("data-formkey");
        var budgetType = self.attr("data-budget-type");
        approval('reject', formKey, budgetId, taskId, budgetType);
    });

    /**
     *
     * @param type
     * @param formKey
     * @param budgetId
     * @param taskId
     * @param budgetType
     * @returns {*}
     */
    function approval(type, formKey, budgetId, taskId, budgetType) {
        var form = utils.formTemplate[formKey || '/contract/dealTask/defaultapprove'];
        if (!form) {
            return notify.error('不存在的审批模板!');
        }


        notify.formDialog('审批意见', form, function () {
            var form = $(".approval-form");
            var info = $('.approval-info').val(),
                cbox = $('#isChecked');
            var params = {
                'budgetId[]': budgetId,
                'task_id[]': taskId,
                'reason': info || '',
                'action_type': type == 'agree' ? 1 : 2, //1同意2驳回.
                'budgetType': budgetType //预算类型.
            };

            if (cbox.length > 0) {
                params.is_check = cbox.prop('checked') ? 1 : 0;
            }

            $.post('/aj/budget/approval', params, function (resp) {
                if (resp.rcode != 200) {
                    notify.error(resp.rmessage || '审批错误.');
                    return;
                }

                notify.success(resp.rmessage || '操作成功.', function () {
                    window.location.reload();
                });
            }, 'json');
        });
        $('.approval-info').val(type == 'agree' ? '同意' : '驳回');
    }
});