fml.define('fms/pay/apply', ['jquery', 'plugin/tokeninput', 'plugin/moment', 'plugin/bootstrap/datepicker', 'plugin/bootstrap/validator', 'plugin/dropzone', 'fms/contract/validate', 'component/notify', 'fms/common/utils'], function (require, exports) {
    var $ = require('jquery');
    var validate = require('fms/contract/validate');
    var dropzone = require('plugin/dropzone');
    var notify = require('component/notify');
    var moment = require('plugin/moment');
    var utils = require('fms/common/utils');
    var dropZoneInstance = null;


    //初始化申请日期
    $('.apply-date').val(moment().format('YYYY-MM-DD'));
    //绑定日期选择插件
    $('#payDay,#signDate').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true,
        'todayHighlight': true
    });
    //
    var payCondition = utils.UEditor('payTips', true);
    var remarkEditor = utils.UEditor('remark', true);


    // 通过合同编号 查询相关信息
    var lastAttachList = [];
    $('input[name=cpbContractCode]').on('keyup change blur', function () {
        var conId = $(this).val();
        $.post('/aj/pay/search_contract', {'contractCode': conId}, function (resp) {
            var data = null;
            if (resp.rcode == 200) {
                if (!$.isEmptyObject(resp.contractInfo)) {
                    data = resp.contractInfo;
                    //合同名称
                    $('#nameContent').val(data.cpbContractName || '');
                    //合同签订日期
                    $("#signDate").val(data.cpbContractSignTime || "");
                    payCondition.setContent(data.cpbContractPayTerm || '');
                    //预算项目
                    $("#preProject").val(data.cpbBugetitemId || "");

                    //付款单位
                    $("#payCompany").val(data.cpbContractCompany || "");
                    //收款公司
                    $("#takeCompany").val(data.cpbCustomerCompany || "");
                } else {
                    //清空.
                    $('#nameContent').val('');
                    $('#signDate').val('');
                    payCondition.setContent('');
                    $('#preProject').val('');
                    $('#payCompany').val('');
                    $('#takeCompany').val('');
                }

                //resp.archiveAttach = [
                //    {fileName:"测试文件",'id':1234},
                //    {fileName:"测试文件11",'id':1235},
                //]

                //删除之前的
                $.each(lastAttachList, function (index, file) {
                    //删除对应的文件.
                    dropZoneInstance.options.removedfile.call(dropZoneInstance, file);
                });
                //重置数组
                lastAttachList = [];
                $("input[name=archiveAttachId]").val('');
                //重新添加。
                if (resp.archiveAttach && resp.archiveAttach.length) {
                    var contractIds = [];
                    $.each(resp.archiveAttach, function (index, file) {
                        //转换大小写.
                        file.name = file.fileName;
                        file.fromContract = true;
                        lastAttachList.push(file);
                        dropZoneInstance.options.addedfile.call(dropZoneInstance, file);
                    });
                    $("input[name=archiveAttachId]").val(contractIds.join(','));
                }
            }
        }, 'json');
    });

    /**
     * 格式化金额.
     */
    $('input[name=cpbMoney]').on('blur', function () {
        validate.money($(this));
    });


    /**
     * 签合同显示，无合同隐藏合同信息
     */
    $('input[name=sign]').on('change', function () {
        var radio = $(this);
        var isYes = radio.val() == 'yes';
        $('.sign-yes').toggle(radio.val() == 'yes');

        if (!isYes) {
            $('.sign-yes .form-control').val('').removeAttr('data-required');
            payCondition.setContent('');
        } else {
            $('.sign-yes .form-control').attr('data-required', '');
        }
    });


    /**
     * 上传文件
     * @type {Array}
     */
    var fileArr = []
        , fileMap = {},
        fileOverArr = [],//获取已保存到数据库的文件的数组
        delIdArr = [];//删除文件时候的id数组。
    var zoneOptions = {
        url: '/upload/file',
        paramName: 'upload',
        autoDiscover: false,
        dictRemoveLinks: 'x',
        dictCancelUpload: 'x',
        dictDefaultMessage: '文件拖到或点击此处实现上传，支持pdf/doc/docx/xlsx/xls/ppt/pptx/rar/zip格式',
        addRemoveLinks: true,
        dictRemoveFile: '删除文件',
        previewTemplate: utils.dzPreviewTemplate,
        maxFiles: 10,
        dictMaxFilesExceeded: '不能超过2个',
        autoProcessQueue: true,
        //acceptedFiles: 'application/pdf,.doc,.docx,.xlsx,.xls,.ppt,.pptx,.zip,.rar',
        init: function () {
            var zone = dropZoneInstance = this;
            var initFileList = $.parseJSON($('#fileZone').attr('data-init') || '[]');
            if (initFileList && initFileList.length) {
                $.each(initFileList, function (index, file) {
                    //记录到
                    fileMap[file.id] = file.id;
                    //转换大小写.
                    file.name = file.fileName;
                    fileOverArr.push(file);
                    zone.options.addedfile.call(zone, file);
                });
            }

            this.on('success', function (file) {
                var data = $.parseJSON(file.xhr.responseText);
                notify.success(data.rmessage);
                fileArr.push(data.filePath);
                fileMap[file.name] = data.filePath;
                //fileArr.join(',');
                var str = fileArr.toString();
                $('input[name=cpbFilePath]').val(str);
            }).on("removedfile", function (file) {
                //不是从合同带出的.
                if (file.id && !file.fromContract) {
                    delIdArr.push(file.id);
                    //更新删除的附件
                    $('input[name=delAttachId]').val(delIdArr.toString());
                }
                for (var i = 0; i < fileArr.length; i++) {
                    if (fileArr[i] == fileMap[file.name]) {
                        fileArr.splice(i, 1);
                    }
                }
                var str = fileArr.toString();
                $('input[name=cpbFilePath]').val(str);
                notify.success("已删除");
            });
        }
    };
    $('#fileZone').dropzone(zoneOptions);

    /**
     * 表单检查
     * @param form
     * @returns {boolean}
     */
    function checkForm(form) {
        var failed = [];
        //现金收款的时候，
        if ($("#takeWay").val() == 2) {
            $('input[name=cpbBranchBank],input[name=cpbBank],input[name=cpbAccountNumber]')
                .removeAttr('data-required').each(function () {
                    validate.passBorder($(this));
                });
        } else {
            $('input[name=cpbBranchBank],input[name=cpbBank],input[name=cpbAccountNumber]')
                .attr('data-required', true);
        }
        $(form).find('[data-required]:not(textarea)').each(function () {
            var self = $(this);
            if (!self.val()) {
                validate.redBorder(self);
                failed.push(self);
            } else {
                validate.passBorder(self);
            }
        });

        var payMoneyInput = $('input[name=cpbMoney]');
        var payMoney = (payMoneyInput.val() || '').replace(/,/g, '') || 0;
        var groupCodeName = $("input[name=cpbGroupSimplename]", form);
        if (payMoney > 50000 && !groupCodeName.val()) {
            validate.redBorder(groupCodeName);
        } else {
            validate.passBorder(groupCodeName);
        }


        if (failed.length > 0) {
            notify.error('信息填写不完整,请检查一下!');
            return false;
        }

        if ($('input[name=sign]:checked').val() == 'yes' && !payCondition.getContent().trim()) {
            notify.error('请填写付款条件.');
            return false;
        }

        //if (!remarkEditor.getContent().trim()) {
        //    notify.warning('请填写备注信息.');
        //    return false;
        //}

        var filePathInput = $('input[name=cpbFilePath]', form);
        var initJson = $('#fileZone').attr('data-init');
        //没有上传过附件、并且没有新上传
        if (!initJson && !filePathInput.val()) {
            notify.error('请上传附件.');
            return false;
        }

        return true;
    }

    /**
     * 获取表单数据
     * @param form
     * @returns {*}
     */
    function getFormData(form) {
        return utils.wrapFormFilter({
            form: form,
            prefix: 'contractPay.',
            exclude: ['cpbFilePath', 'delAttachId', 'archiveAttachId'],
            filter: function (field) {//去掉金额的千分符，向后台传数字
                if (field.name == 'contractPay.cpbMoney') {
                    field.value = field.value.replace(/,/g, '');
                }
                return field;
            }
        });
    }

    /**
     * 提交合同申请单
     */
    $('.btn-submit').on('click', function (e) {
        e.preventDefault();
        var form = $('#formPay'),
            _this = this;

        if (!checkForm(form)) {
            $(_this).button('reset');
            return;
        }


        $(_this).button('loading');
        var formData = getFormData(form);
        $.ajax({
            type: "POST",
            url: '/aj/pay/submit',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.rcode == 200) {
                    window.location.href = '/my/apply/pay';
                } else {
                    notify.error(data.rmessage);
                }
                $(_this).button('reset');
            },
            error: function () {
                notify.error('提交失败');
                $(_this).button('reset');
            }
        });
    });


    /**
     * 保存草稿 无需控制字段
     */
    $('.btn-save-drafts').on('click', function () {

        var form = $('#formPay'),
            _this = this;
        var formData = getFormData(form);
        $(_this).button('loading');
        $.ajax({
            type: "POST",
            url: '/aj/pay/drafts',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.rcode == 200) {
                    notify.success(data.rmessage);
                    setTimeout(function () {
                        window.location.href = '/my/apply/pay';
                    }, 1500);
                } else {
                    notify.error(data.rmessage);
                }
                $(_this).button('reset');
            },
            error: function () {
                notify.error('提交失败');
                $(_this).button('reset');
            }
        });
    });
});
