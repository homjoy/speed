fml.define('fms/contract/view', ['jquery', 'component/notify', 'fms/common/utils'], function (require, exports) {
    var $ = require('jquery');
    var utils = require('fms/common/utils');
    var notify = require('component/notify');

    //@see http://fex-team.github.io/ueditor/#start-uparse
    //处理页面没有边框的BUG，
    //http://redmine.meilishuo.com/issues/40583
    var basePath = fml.getOption('modulebase');
    uParse('.ueditor-content', {
        rootPath: basePath + '/s/ueditor/'
    });

    /**
     * 合同审批
     * @param taskId
     * @param formKey
     */
    function approval(taskId, formKey) {
        var form = utils.formTemplate[formKey || '/contract/dealTask/defaultapprove'];
        if (!form) {
            return notify.error('不存在的审批模板!');
        }
        process('agree', form, taskId);
        $('.approval-info').val('同意');
    }

    /**
     * 新的处理方式.
     * @param type
     * @param form
     * @param taskId
     * @param id
     */
    function process(type, form, taskId, id) {
        id = id || [];
        //select有变化删除提示信息
        $('body').on('change', '#cabContractCode', function () {
            $('.company-warn').html('');
        });
        notify.formDialog('审批意见', form, function () {
            var form = $(".approval-form");
            var cabContractCode = $('#cabContractCode');
            var info = $('.approval-info').val(),
                company = cabContractCode.val(),
                cbox = $('#isChecked');


            var params = {
                "id": $.isArray(id) ? id : [id],
                "task_id": $.isArray(taskId) ? taskId : [taskId],
                "reason": info,
                "action_type": type == 'agree' ? 1 : 2 //1同意 2驳回
            };


            //如果有对应的选项框.
            if (cabContractCode.length) {
                if (!company) {
                    form.after('<p class="company-warn" style="margin-top:10px;color:red;">请选择合同签订公司</p>');
                    return false;
                } else {
                    params['cabContractCode'] = company;
                }
            }

            if (cbox.length > 0) {
                params.is_check = cbox.prop('checked') ? 1 : 0;
            }

            $.post('/aj/contract/approval_all', params, function (data) {
                if (data.rcode != 200) {
                    return notify.error(data.rmessage);
                } else {
                    notify.success(data.rmessage)
                    window.location.reload();
                }
            }, 'json');
        });
    }


    /**
     *
     * @param taskId
     */
    function reject(taskId) {
        var form = '<textarea placeholder="不超过200字" class="form-control approval-info" style="min-height:80px">驳回</textarea>';
        process("reject", form, taskId);
    }

    // 同意
    $('body').on('click', '.btn-agree', function (e) {
        e.stopPropagation();
        var btn = $(this);
        var taskId = btn.attr('data-taskId');
        var formkey = btn.attr('data-formkey');
        approval(taskId, formkey);
    }).on('click', '.btn-reject', function (e) {
        //驳回
        e.stopPropagation();
        var taskId = $(this).attr('data-taskId');
        reject(taskId);
    });
});