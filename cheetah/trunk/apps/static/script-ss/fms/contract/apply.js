fml.define('fms/contract/apply', ['jquery','plugin/tokeninput','plugin/moment','plugin/bootstrap/datepicker','plugin/dropzone','component/notify','fms/contract/validate','fms/common/utils'], function(require, exports) {
    var $ = require('jquery');
    var validate = require('fms/contract/validate');
    var dropzone = require('plugin/dropzone');
    var notify = require('component/notify');
    var moment = require('plugin/moment');
    var utils = require('fms/common/utils');

    /**
     * 合同申请页面相关路径
     */
    var conUrl = {
        drafts: '/aj/contract/drafts',
        submit: '/aj/contract/submit',
        qPrimaryCon: '/aj/contract/search_primary_contract',
        qCon: '/aj/contract/search_contract'
    };

    utils.UEditor('condition',true);
    utils.UEditor('content');
    utils.UEditor('mark');
    /**
     * 主合同信息
     */
    var inpPriInfo = {
        cabCustomerCompany: '#customer_company', //对方单位
        cabContractPerson: '#customer_contractor', //对方联系人
        cabTelephone: '#customer_phone', //联系电话
        cabContractCode: '#contract_company', //合同签订公司
        //departmentName: '#pass_depart', // 申请部门
        //cabDepartmentId:'#pass_did',
        //userName: '#pass_person', // 申请人
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

    }).on('blur', function() {
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
     * 补充协议————主合同信息显示控制
     */

    $('.supply').on('click', function() {
        var inpVal = $(this).val() == 'Y';
        var $curDom = $('.dis-ctrl'),
            $tokenUl = $('.token-input-list');
        if (inpVal) {
            $curDom.removeClass('supply-hide');
        } else {
            $('#pricon_id').tokenInput("clear");
            $('#pricon_name,#pricon_code').val('');
            $curDom.addClass('supply-hide');
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
     * 主合同查询
     */
    $('#pricon_id').tokenInput(conUrl.qPrimaryCon, {
        noResultsText: '没有结果',
        searchingText: '搜索中',
        queryParam: 'cContractCode',
        tokenLimit:1,
        onResult: function (results) {
            return results;
        },
        onAdd:function(item){
            var priId = $(this).val();
            $.post(conUrl.qCon,{'priId': item.id}, function(data) {
                for (var key in inpPriInfo) {
                    // 要求key的值与data中key值相同
                    $(inpPriInfo[key]).val(data[key]);
                }
            },'json');
        }
    });
    /**
     * 处理表单元素name值
     * @param form
     * @returns {*}
     */
    function dealAttrName(form) {
        var a = form.find('input,select,textarea');
        $.each(a, function(i, v) {
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

    /**
     * 提交合同申请单
     */
    $('.btn-submit').on('click', function(e) {
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
        $('.radio').each(function(){
            //没选中的.
            if(!$(this).find('.form-radio input[type=radio]:checked').length ){
                isAllChecked = false;
            }
        });

        //必须全选.
        if(!isAllChecked){
            notify.warning('请选择收支类型、合同类型、协议类型~');
            return;
        }

        var isPass = validate.checkAll();
        validate.money($('.money'));
        if (isPass.length == 0) {
            $(_this).button('loading');
            var info = form.serialize();
            $.ajax({
                type:"POST",
                url:conUrl.submit,
                data:info,
                //timeout:5000,
                dataType:'json',
                success: function(data){
                    if(data.rcode == 200){
                        window.location.href='/my/apply/contract';
                    }else{
                        notify.error(data.rmessage);
                    }
                    $(_this).button('reset');
                },
                error:function(){

                    notify.error('提交失败');
                    $(_this).button('reset');
                }
            });
        }else{
            notify.error('信息填写不完整,请检查一下~');
            $(_this).button('reset');
        }
    });



    var fileArr = [];
    var fileMap = {};
    var zoneOptions = {
        url:'/upload/file',
        paramName:'upload',
        autoDiscover :false,
        addRemoveLinks: true,
        dictRemoveLinks: 'x',
        dictCancelUpload: 'x',
        dictDefaultMessage:'文件拖到或点击此处实现上传，支持pdf/doc/docx/xlsx/xls/ppt/pptx/rar/zip格式',
        dictRemoveFile:'删除文件',
        previewTemplate: utils.dzPreviewTemplate,
        maxFiles:10,
        dictMaxFilesExceeded:'不能超过10个',
        //关闭自动上传功能，默认会true会自动上传
        //也就是添加一张图片向服务器发送一次请求
        autoProcessQueue: true,
        //acceptedFiles: 'application/pdf,.doc,.docx,.xlsx,.xls,.ppt,.pptx,.zip,.rar',
        init: function() {
            var zone = this;
            this.on('success', function(file) {
                var data = JSON.parse(file.xhr.responseText);
                notify.success(data.rmessage);
                fileArr.push(data.filePath);
                fileMap[file.name] = data.filePath;
                //fileArr.join(',');
                var str = fileArr.toString();
                $('.cabFilePath').val(str);
            });
            this.on("removedfile", function(file) {
                //var data = JSON.parse(file.xhr.responseText);
                for(var i = 0;i<fileArr.length;i++){
                    if(fileArr[i] == fileMap[file.name]){
                        fileArr.splice(i,1);
                    }
                }
                var str = fileArr.toString();
                $('.cabFilePath').val(str);
                notify.success("已删除");
            });
        }
    };
    // new Dropzone('#fileZone',zoneOptions)
    // Dropzone.autoDiscover = false;
    $('#fileZone').dropzone(zoneOptions);


    /**
     * 保存草稿 无需控制字段
     */
    $('.btn-save-drafts').on('click', function() {

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
            type:"POST",
            url:conUrl.drafts,
            data:info,
            //timeout:5000,
            dataType:'json',
            success: function(data){
                if(data.rcode == 200){
                    window.location.href='/my/apply/contract';
                }else{
                    notify.error(data.rmessage);
                    //$.niftyNoty({
                    //    type: 'info',
                    //    container: 'floating',
                    //    message: data.rmessage,
                    //    timer: 5000
                    //});
                }
                $(_this).button('reset');
            },
            error:function(){
                notify.error('提交失败');
                //$.niftyNoty({
                //    type: 'warning',
                //    container: 'floating',
                //    message: '提交失败，请重试.',
                //    timer: 5000
                //});
                $(_this).button('reset');
            }
        });

    });


    $("#contractNumber").on("keyup blur",function(){
        utils.matchSignCompany($(this).val(),function(companyName){
            $('#signCompany').val(companyName);
        });
    });
});