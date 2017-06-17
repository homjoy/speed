fml.define('fms/contract/edit', ['jquery', 'plugin/tokeninput', 'plugin/moment', 'plugin/dropzone', 'plugin/bootstrap/datepicker', 'component/notify', 'fms/contract/validate', 'fms/common/utils'], function (require, exports) {
    var $ = require('jquery');
    var moment = require('plugin/moment');
    var validate = require('fms/contract/validate');
    var notify = require('component/notify');
    var utils = require('fms/common/utils');
    var dropzone = require('plugin/dropzone');

    /**
     * select公司值
     */
    var companyVal = $('#conCompany').val();
    $('#contract_company').val(companyVal);


    /**
     * 主合同信息
     */
    var inpPriInfo = {
        cabCustomerCompany: '#customer_company', //对方单位
        cabContractPerson: '#customer_contractor', //对方联系人
        cabTelephone: '#customer_phone', //联系电话
        cabContractCode: '#contract_company', //合同签订公司
        //departmentName: '#pass_depart', // 经办部门
        //cabDepartmentId:'#pass_did',
        //userName: '#pass_person', // 经办人
        //cabHandingPersonId:'#pass_pid',
        cabMasterContractName: '#pricon_name' // 主合同名称
    };
    /**
     * 日期插件设置
     */
    $('#startTime,#endTime').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true,
        'todayHighlight': true

    }).on('blur', function () {
        if (!$(this).val()) {
            setLoaclDate();
        }
    });
    /**
     * 如果时间为空,设置为默认的本地时间
     */
    function setLoaclDate() {
        var curDate = moment().format('YYYY-MM-DD');
        $('#startTime,#endTime,#applyTime').val(curDate);
    }

    var isDate = $('#startTime').val().length > 0 && $('#endTime').val().length > 0;
    if (!isDate) {
        setLoaclDate();
    }
    /**
     * 控制文件上传隐藏
     */
    $('.btn-del-file').on('click', function () {
        var hstr = ''
            + '<input type="hidden" class="cabFilePath" name="cabFilePath" value="">'
            + '<input type="file" class="form-control" name="upload" id="input" data-required>'
            + '文件拖拽或点击此处实现上传，支持pdf/doc/dpcx/xlsx/xls/ppt/pptx/rar/zip格式'
        $('.file-show').html(hstr);

    });

    /**
     * 补充协议————主合同信息显示控制
     */

    $('.supply').on('click', function () {
        var inpVal = $(this).val() == 'Y';
        var $curDom = $('.dis-ctrl'),
            $tokenUl = $('.token-input-list');
        if (inpVal) {
            $curDom.removeClass('supply-hide');
            //清空输入框
        } else {
            $('#pricon_id').tokenInput("clear");
            $('#pricon_name,#pricon_code').val('');
            $curDom.addClass('supply-hide');
            //$('#pricon_id').tokenInput("remove",{id:tokenId});
            validate.passBorder($curDom.find('input'));
            validate.passBorder($tokenUl);
        }
        var isVal = $(this).prop('checked');
        // checkbox有值 select变成必填。
        if (isVal) {
            $('.pri-con').attr('data-required', '');
        } else {
            $('.pri-con').removeAttr('data-required')
        }
    });

    /**
     * 主合同搜索
     */
    var id = $('#pricon_id').val();
    var name = $('#pricon_code').val();
    //cabMasterContractName
    $('#pricon_id').tokenInput('/aj/contract/search_primary_contract', {
        noResultsText: '没有结果',
        searchingText: '搜索中',
        queryParam: 'cContractCode',
        tokenLimit: 1,
        prePopulate: [
            {id: id, name: name}
        ],
        onResult: function (results) {
            return results;
        },
        onAdd: function (item) {
            $.post('/aj/contract/search_contract', {'priId': item.id}, function (data) {
                for (var key in inpPriInfo) {
                    // 要求key的值与data中key值相同
                    $(inpPriInfo[key]).val(data[key]);
                }
            }, 'json');
        }
    });

    //if($('.pri-switch').prop('checked')){
    //    $('.supply').trigger('click');
    //}

    /**
     * 表单元素 name属性处理
     * @param form
     * @returns {*}
     */
    function dealAttrName(form) {
        var a = form.find('input,select,textarea');
        $.each(a, function (i, v) {
            var oName = $(v).attr('name'),
                newName,
                oVal = $(v).val();
            if (oName) {
                newName = oName.replace('_', '.');
                $(v).attr('name', newName);
            }
        });
        return form;
    }

    utils.UEditor('condition', true);
    utils.UEditor('content');
    utils.UEditor('mark');
    /**
     * 文件上传
     */
    //$('body').on('change','#input', function () {
    //    var xhr = new XMLHttpRequest();
    //    xhr.onreadystatechange = function (e) {
    //        if (xhr.readyState == 4) {
    //            if (xhr.status == 200) {
    //                var ret = $.parseJSON(xhr.responseText);
    //                if(ret.rcode=="200"){
    //                    $('.cabFilePath').val(ret.filePath);
    //                }
    //                notify.info(ret.rmessage);
    //            }
    //        }
    //    };
    //    xhr.open("post", "/upload/file", true);
    //    var formData = new FormData(document.getElementById('form-con-apply'));
    //    xhr.send(formData); //提交表单
    //});
    var fileArr = [],//增添时候的文件数组
        fileMap = {},//增添文件时候的映射
        fileOverArr = [],//获取已保存到数据库的文件的数组
        delIdArr = [];//删除文件时候的id数组。
    var zoneOptions = {
        url: '/upload/file',
        paramName: 'upload',
        autoDiscover: false,
        addRemoveLinks: true,
        dictRemoveLinks: 'x',
        dictCancelUpload: 'x',
        dictDefaultMessage: '文件拖到或点击此处实现上传，支持pdf/doc/docx/xlsx/xls/ppt/pptx/rar/zip格式',
        dictRemoveFile: '删除文件',
        maxFiles: 10,
        dictMaxFilesExceeded: '不能超过10个',
        previewTemplate: utils.dzPreviewTemplate,
        //关闭自动上传功能，默认会true会自动上传
        //也就是添加一张图片向服务器发送一次请求
        autoProcessQueue: true,
        //acceptedFiles: 'application/pdf,.doc,.docx,.xlsx,.xls,.ppt,.pptx,.zip,.rar',
        init: function () {
            var zone = this;
            var fileLen = $('.fileLen').val();//文件个数
            var aFileId = $('.fileId');//隐藏域文件id数组
            var aFileName = $('.fileName');//隐藏域文件名字数组
            var mockFiles;
            //变成数组
            for (var i = 0; i < fileLen; i++) {
                var temp = {
                    id: $(aFileId[i]).val(),
                    name: $(aFileName[i]).val()
                };
                fileOverArr.push(temp);
            }
            for (var i = 0; i < fileLen; i++) {
                var fileId = fileOverArr[i].id;
                var fileName = fileOverArr[i].name;
                mockFiles = {name: fileName, id: fileId};
                console.log(fileId, mockFiles);
                zone.options.addedfile.call(zone, mockFiles);
            }
            zone.on('success', function (file) {
                var data = JSON.parse(file.xhr.responseText);
                notify.success(data.rmessage);
                fileArr.push(data.filePath);
                fileMap[file.name] = data.filePath;
                //fileArr.join(',');
                var str = fileArr.toString();
                console.log(str, "----");
                $('.cabFilePath').val(str);
            }).on("removedfile", function (file) {
                if (file.id) {
                    delIdArr.push(file.id);
                }
                console.log(delIdArr.toString(), '=======')
                $('.delAttachId').val(delIdArr.toString());
                //获取上传文件的随机编码。cabfilepath
                for (var i = 0; i < fileArr.length; i++) {
                    if (fileArr[i] == fileMap[file.name]) {
                        fileArr.splice(i, 1);
                    }
                }
                var str = fileArr.toString();
                $('.cabFilePath').val(str);
                notify.success("已删除");
            });
        }
    };
    $('#fileZone').dropzone(zoneOptions);


    /**
     * 提交合同申请单
     */
    $('.btn-submit').on('click', function (e) {
        e.preventDefault();
        var form = $('#form-con-apply'),
            newform = dealAttrName(form),
            stime = $('#startTime'),
            etime = $('#endTime'),
            _this = this;
        if (stime.val() >= etime.val()) {
            notify.error('请检查一下合同期限~');
            validate.redBorder(stime);
            validate.redBorder(etime);
            return;
        }else{
            validate.passBorder(stime);
            validate.passBorder(etime);
        }

        var isAllChecked = true;
        $('.radio').each(function () {
            //没选中的.
            if (!$(this).find('.form-radio input[type=radio]:checked').length) {
                isAllChecked = false;
            }
        });

        //必须全选.
        if (!isAllChecked) {
            notify.warning('请选择收支类型、合同类型、协议类型~');
            return;
        }

        var isPass = validate.checkAll();
        validate.money($('.money'));
        //console.log(isPass.length, isFlag)

        if (isPass.length == 0) {
            $(_this).button('loading');
            var info = form.serialize();
            $.ajax({
                type: "POST",
                url: '/aj/contract/save_edit',
                data: info,
                //timeout:5000,
                dataType: 'json',
                success: function (data) {
                    if (data.rcode == 200) {
                        window.location.href = '/my/apply/';
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
        } else {
            notify.error('信息填写不完整,请检查一下~');
        }

    });
    /**
     *     保存草稿 无需控制字段
     */
    $('.btn-save-drafts').on('click', function () {


        var form = $('#form-con-apply'),
            newform = dealAttrName(form),
            stime = $('#startTime').val(),
            etime = $('#endTime').val(),
            _this = this;
        if (stime >= etime) {
            notify.error('请检查一下合同期限~');
            return;
        }
        var info = form.serialize();
        $(_this).button('loading');
        $.ajax({
            type: "POST",
            url: '/aj/contract/save_drafts',
            data: info,
            //timeout:5000,
            dataType: 'json',
            success: function (data) {
                if (data.rcode == 200) {
                    window.location.href = '/my/apply/';
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


    $("#contractNumber").on("keyup blur", function () {
        utils.matchSignCompany($(this).val(),
            function (companyName) {
                $('#signCompany').val(companyName);
            });
    });
});

