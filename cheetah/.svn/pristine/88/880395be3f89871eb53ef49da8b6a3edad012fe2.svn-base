fml.define('fms/my/manage/contract', ['jquery',
    'plugin/bootstrap/datepicker',
    'plugin/bootstrap/validator',
    'plugin/dropzone',
    'plugin/artTemplate',
    'component/notify',
    'component/pagination',
    'component/upload',
    'fms/common/utils',
    'component/TreeSelect',
    'plugin/tokeninput'
], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');
    var dropzone = require('plugin/dropzone');
    var template = require('plugin/artTemplate');
    var utils = require('fms/common/utils');

    var archiveList = $('#contract-table');
    var archiveBtn = $('.toolbar .btn-filing');
    var rejectFilingBtn = $('.toolbar .btn-rejectFiling');
    var currentPage = 1;


    /**
     * 加载部门
     */
    $.post('/aj/query/depart', function (data) {
        $("input[name=departmentId]").TreeSelect({
            multiple: true,
            data: data
        });
    }, 'json');

    /**
     * 日期控件
     */
    $("#sTime,#eTime").datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true,
        'todayHighlight': true
    });

    /**
     * 加载人员信息
     */
    $('input[name=handleId]').tokenInput("/aj/user/search", {
        tokenLimit: 1,
        onAdd: function (item) {
            $.ajax('/aj/user/get', {
                type: 'POST',
                data: {userId: item.id},
                dataType: 'json',
                success: function (resp) {
                    if (resp.code != 200) {
                        return notify.error('')
                    }
                    $('input[name=handleId]').val(item.id);
                }
            });
        },
        tokenFormatter:function(item){
            return "<li><p>" + item['name_cn'] + "</p></li>";
        },
    });

    /**
     * 驳回按钮操作.
     */
    rejectFilingBtn.on('click',function(e){
        e.preventDefault();

        var selectedRow = archiveList.find('tr.selected');
        if (!selectedRow.length) {
            notify.warning('未选中任何行.');
            return;
        }
        rejectFilingBtn.text('驳回中..').prop('disabled', true);
        console.log('disabled status',rejectFilingBtn.prop('disabled'));

        var data = {
            "requestId": selectedRow.find('[name=requestId]').val() || '',
        };

        $.post('/aj/contract/reject_filing',data,function(resp){
            if(resp.rcode !=200){
                notify.error(resp.rmessage);
                return false;
            }
            console.log('disabled status',rejectFilingBtn.prop('disabled'));

            rejectFilingBtn.text('驳回').prop('disabled', false);
            notify.success("驳回成功.");
            //刷新归档列表
            loadArchives();

        },'json');

    });

    /**
     * 绑定点击归档操作.
     */
    archiveList.on('click', 'tbody tr', function (e) {
        var currRow = $(e.target).closest('tr');
        var prevRow = archiveList.find('tr.selected');
        if (currRow.hasClass('selected')) {
            return;
        }


        if (prevRow.length) {
            //禁用编辑
            toggleRow(prevRow, false);
            prevRow.removeClass('selected');
            prevRow.find('.attach-list').show();
        }

        //按钮恢复为可归档状态.
        archiveBtn.text('归档').removeClass('archiving').prop('disabled', false);
        //高亮当前正在归档的行
        currRow.addClass('selected');
        //切换当前行为可归档.
    });


    /**
     * 归档按钮点击
     */
    archiveBtn.on("click", function (e) {
        e.preventDefault();
        var self = $(this);


        var isArchiving = self.hasClass('archiving');
        var selectedRow = archiveList.find('tr.selected');
        if (!selectedRow.length) {
            notify.warning('未选中任何行.');
            return;
        }


        if (!isArchiving) {
            //按钮可进行保存操作.
            self.text('保存').addClass('archiving').prop('disable', false);
            selectedRow.find('.attach-list').hide();
            //切换为可编辑状态
            return toggleRow(selectedRow, true);
        }

        // 防止二次点击
        self.prop('disabled', true);
        //如果是已归档，则是修改
        if (selectedRow.hasClass('archive')) {
            var requestId = selectedRow.find('[name=requestId]').val(),
                conId = selectedRow.find('[name=conId]').val(),
                signDate = selectedRow.find('[name=signDate]').val(),
                state = selectedRow.find('[name=remark]').val();

            var info = {
                'requestId': requestId,
                'signDate': signDate,
                'conId': conId,
                "cFilePath": selectedRow.find('[name=cFilePath]').val() || '',
                "delAttachId": selectedRow.find('[name=delAttachId]').val() || '',
                'state': state
            };
            $.post('/aj/contract/update_filing', info, function (data) {
                if (data.rcode == 200) {

                    self.text('归档').removeClass('archiving').prop('disabled', false);
                    notify.success("保存成功.");
                    //刷新归档列表
                    loadArchives();
                } else {
                    notify.error(data.rmessage || '保存出错.');
                }
            }, "json");
        } else {
            var data = {
                "requestId": selectedRow.find('[name=requestId]').val() || '',
                "signDate": selectedRow.find('[name=signDate]').val() || '',
                "cFilePath": selectedRow.find('[name=cFilePath]').val() || '',
                "remark": selectedRow.find('[name=remark]').val() || ''
            };
            $.post('/aj/contract/filing', data, function (data) {
                if (data.rcode == 200) {
                    self.text('归档').removeClass('archiving').prop('disabled', false);
                    notify.success('归档成功');
                    loadArchives();
                } else {
                    notify.error(data.rmessage);
                }
            }, 'json');
        }
    });


    /**
     * 切换行的编辑状态.
     * @param row
     * @param editable
     */
    function toggleRow(row, editable) {
        if (editable) {
            editableRow(row);
        } else {
            row.removeClass('editable').find('[disabled]').prop('disabled', true);
            //销毁上传插件
            var upload = row.find('.btn-upload-attach').data('QueenUpload');
            upload &&  upload.destroy();
        }
    }

    /**
     * 让指定行切换为可编辑状态
     * @param selectedRow
     */
    function editableRow(selectedRow) {
        selectedRow.addClass('editable').find('[disabled]').removeAttr('disabled');
        var exists = [];
        var uploadBtn = selectedRow.find('.btn-upload-attach');
        var filePathInput = selectedRow.find('[name=cFilePath]');
        var delFileInput = selectedRow.find('[name=delAttachId]');
        var filePaths = [],delPath = [];
        try {
            var files = $.parseJSON(uploadBtn.attr('data-exists') || '[]') || [];
            $.each(files, function (index, value) {
                exists.push({
                    "identify": value.id,
                    "name": value.fileName,
                    "filePath": value.filePath,
                    "fileId": value.id, //删除的时候提交ID 即可
                    "type": "jpg"
                });
            });//r'nagn
        } catch (e) {
            exists = [];
        }
        //绑定上传插件及处理.
        uploadBtn.QueenUpload({
            name: "upload",
            action: "/upload/file",
            multiple: false,
            draggable: true,
            showProgressBar: false,
            message: {
                allowTypeTips: '仅支持文档、压缩文件'
            },
            //初始化文件列表（用于增加已经上传的文件列表，数据修改的时候会用得到）
            existFiles: exists,
            onSuccess: function (response, file) {
                if (response.rcode != 200) {
                    notify.error(response.rmessage || '上传失败.');
                    return false;
                }
                notify.success(response.rmessage || '上传成功.');
                filePaths.push(response.filePath);
                file.filePath = response.filePath;
                filePathInput.val(filePaths.join(','));
            },
            onError: function (message, file) {
                notify.error(message);
            },
            onFileRemove: function (file) {
                for (var i = 0; i < filePaths.length; i++) {
                    //删除的新上传的文件.
                    if (filePaths[i] == file.filePath) {
                        filePaths.splice(i, 1);
                    }
                }
                //提交删除已上传文件。
                if(file.fileId){
                    delPath.push(file.fileId);
                    delFileInput.val(delPath.join(','));
                }
            }
        });
    }


    /**
     * 加载归档列表.
     */
    function loadArchives(page) {
        var p = page || currentPage , size = 10;
        //原有查询接口/aj/my_manage/contract_archives
        var params = [{name:"page",value:p},{name:"ps",value:size}];
        var url = '/aj/my_manage/contract_archives';

        url = url + (url.indexOf('?') >= 0 ? '&' : '?') + $.param(params);

        $.ajax({
            url: url,
            type: 'POST',
            data: $('.query-form').serializeArray(),
            dataType: 'json',
            success: function (resp) {
                if (resp.rcode != 200) {
                    archiveList.find('tbody').html('');
                    return notify.error(resp.rmessage || '暂无数据.');
                }

                if (resp.data && resp.data.length > 0) {
                    var tBody = template('fillingList', {archives: resp.data});
                    archiveList.find('tbody').html(tBody);
                    bindHandle();
                } else {
                    /* archiveList.find('tbody').html('');*/
                    //对于归档或驳回后当前页面数据只有一行的情况下，返回前一页或者显示empty页面
                    if(page > 1) {
                        loadArchives(page-1);
                        return ;
                    }
                    $('#tab-filing').css({'visibility':'hidden'})
                        .prev('.empty-content').css({'display':'block'});

                }
            }
        });

    }

    /**
     * 加载归档列表
     */
    // loadArchives();

    //单击查询按钮触发事件
    $('.btn-query').on('click',function(e){
        e.preventDefault();

        var page = 1,pageSize = 10;
        var params = [{name:"page",value:page},{name:"ps",value:pageSize}];
        var url = '/aj/my_manage/contract_archives';

        url = url + (url.indexOf('?') >= 0 ? '&' : '?') + $.param(params);

        $.ajax({
            url:url,
            type:'POST',
            data:$('.query-form').serializeArray(),
            dataType:'json',
            success:function(resp){
                if(resp.rcode!=200){
                    notify.error(resp.rmessage || '查询有误');
                    return ;
                }

                if(!resp.data.length){
                    //查询结果数据为空
                    $('#tab-filing').css({'visibility':'hidden'})
                        .prev('.empty-content').css({'display':'block'});

                    return ;
                }


                $('#tab-filing').css({'visibility':'visible'})
                    .prev('.empty-content').css({'display':'none'});

                var pageObj = $("#pagination").data('pagination');
                if(pageObj){
                    pageObj.destroy();
                }

                $("#pagination").pagination({
                    totalPage: parseInt(resp.page.maxPage) || 1,
                    currentPage: parseInt(resp.page.currentPage) || 1,
                    lastPagesCount: 1
                }).on("switch", function (e, page) {
                    //记录当前页，一会刷新用.
                    currentPage = page;
                    loadArchives(page);
                });

                if (resp.data && resp.data.length > 0) {
                    var tBody = template('fillingList', {archives: resp.data});
                    archiveList.find('tbody').html(tBody);
                    bindHandle();
                } else {
                    archiveList.find('tbody').html('');
                }

            }

        });
    });
    /**
     * 绑定事件处理
     */
    function bindHandle() {
        /* 待归档*/
        // 单击上传文件转移点击
        $('.click-input').click(function () {
            $(this).parent().next().click();
        });

        // 解决验证事件的bug
        $('.signDate,.overSignDate').datepicker({
            'format': 'yyyy-mm-dd',
            'autoclose': true,
            'todayHighlight': true
        }).on('changeDate', function () {
            $(this).trigger('input')
        });
    }


    /**
     * 通知领取
     */
    $('body').on('click', '.btn-mail', function () {
        var ids = getCheckedIds();
        var params = [];
        for (var i = 0, len = ids.length; i < len; i++) {
            params.push({
                name: "requestId",
                value: ids[i]
            });
        }
        if (ids.length > 0) {
            $.post('/aj/contract/mail', $.param(params), function (data) {
                if (data.rcode == 200) {
                    notify.success(data.rmessage);
                } else {
                    notify.error(data.rmessage);
                }
            }, 'json');
        } else {
            notify.error('您没有选中任何记录');
        }
    });

    /**
     *
     */
    $(".check-all,.getall").on("change", function () {
        var checked = $(this).prop('checked');
        var checkbox = $("#contract-table tbody input[type=checkbox]");
        checkbox.prop("checked", checked);
    });

    /**
     * 批量获取选中的id
     * @returns {*|jQuery}
     */
    function getCheckedIds() {
        return $('#contract-table').find('tbody input[type=checkbox]:checked').map(function () {
            return $(this).closest("tr").find('[name=requestId]').val() || '';
        }).get();
    }
});