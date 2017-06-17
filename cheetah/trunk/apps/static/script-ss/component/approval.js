fml.define("component/approval",['jquery','plugin/tokeninput','component/notify','plugin/moment','plugin/artTemplate','component/pagination','plugin/bootbox','plugin/bootstrap/daterangepicker','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var notify = require('component/notify');
    var moment = require('plugin/moment');
    var Template = require('plugin/artTemplate');
    var bootbox = require('plugin/bootbox');
    var store = require('plugin/store');

    //默认页，提交后刷新使用
    var pageMemory = 1;
    //分页是否已初始化
    var paginationFlag = false;
    //左侧内容是否为第一次加载
    var firstload = true;
    //数据条数;
    var leftlength;

    //head部分。搜索功能
    var approval = function(option) {
        var htmlheader = '<form class="search-component-form speed-search-head form-inline" >';

        if (!!option.btns) {
            htmlheader += '<input type="checkbox" class="check-all"/>' +
                    //'<label>已选中<span class="choose-num">0</span>条</label>'+
                '<div class="approval-btn-div">';
            $.each(option.btns, function (k, v) {
                htmlheader += '<a href="javascript:void(0);" class="' + v.class + '">' + v.title + '</a>';
            });
            htmlheader += '</div>';
        }

        //只要标记为false就不显示.
        if(option.showLoading !== false){
            $('body').append('<div class="background-ajax" style=""><i class="icon-spinner icon-spin"></i></div><div class="progressBar" style="">处理中，请稍等...</div>');
            $(document).ready(function() {
console.log(12);
                var ajaxbg = $(".background-ajax,.progressBar");
                var timeout ;
                ajaxbg.hide();
                $(document).ajaxStart(function () {
                    timeout=setTimeout(function(){
                        ajaxbg.show();
                    },2000)
                }).ajaxStop(function () {
                    window.clearTimeout(timeout);
                    ajaxbg.hide();
                });
            });
        }

        if (!!option.timeSearch) {
            var daterangepickertitle='申请日期';
            if(!!option.daterangepickertitle){
                daterangepickertitle = option.daterangepickertitle;
            }
            htmlheader += '<label>'+daterangepickertitle+'</label>' +
                '<input type="hidden" class="search-start" name="start_date" />' +
                '<input type="hidden" class="search-end " name="end_date" />';
            htmlheader += '<input class="time-start-end form-control search-fyc">';
        }
        if (!!option.departSearch) {
            htmlheader += '<label>申请部门</label>' +
                '<select name="depart_id" id="">' +
                '<option value="">请选择</option>' +
                '<option value="">技术部-用户研发</option>' +
                '</select>';
        }
        if (!!option.usernameSearch) {
            htmlheader += '<label>姓名</label>' +
                '<div class="usernameSearch-div" style="">' +
                '<input type="text" name="user_id" class="employees"/>' +
                '</div>';
        }
        if (!!option.identifierSearch) {
            htmlheader +=
                '<input type="text" name="identifier" placeholder="输入单据编号" class="identifier form-control"/>';
        }
        if (!!option.headercheckOthers) {
            htmlheader += option.headercheckOthers;
        }
        if (option.searchbtn) {
            htmlheader += '<a href="javascript:void(0);" class="btn btn-default btn-xs search-component">搜索</a>';
        }
        if (!!option.clearbtn) {
            htmlheader += '<a href="javascript:void(0);" class="btn btn-default btn-xs clear-component">清除</a>';
        }
        if (!!option.other) {
            htmlheader += option.other;
        }
        var str_name = 'status';
        if (!!option.status_name) {
            str_name = option.status_name;
        }
        htmlheader += '<input type="hidden" class="status-val" name="' + str_name + '"  ></form>';
        var controltablestyle = '';
        if(!!option.controltablestyle){
            controltablestyle = option.controltablestyle;
        }
        var rightinfostyle = '',autoclass='';
        if(!!option.rightauto){
            autoclass = 'overfloarhidden';
        }
        if(!!option.rightinfostyle){
            rightinfostyle= option.rightinfostyle;
        }
        var htmlbody =
            '<div class="row">' +
            '<div style="width:' + option.leftwidth + ';" class="left-list hide">' +
            '<div class="control-hide"></div>' +
            '<div class="control-table hide-td-narrow '+autoclass+'" '+option.controltablestyle+'>' +

            '</div>' +

            '<div class="pagination-left">' +

            '</div>' +
            '</div>' +
            '<div style="margin-left: ' + option.leftwidth + ';'+rightinfostyle+'" class="right-info hide panel panel-info '+autoclass+'">' +

            '</div>' +
            '</div>';
        //dom结构框架
        $('.search-head').html(htmlheader).after(htmlbody);
        if(!!option.leftauto){
            $('.control-table').on('mouseover',function(){
                $(this).addClass('overfloarauto');
                $(this).removeClass('overfloarhidden');
            }).on('mouseleave',function(){
                $(this).removeClass('overfloarauto');
                $(this).addClass('overfloarhidden');
            });
        }
        if(!!option.rightauto){
            $('.right-info').on('mouseover',function(){
                $(this).addClass('overfloarauto');
                $(this).removeClass('overfloarhidden');
            }).on('mouseleave',function(){
                $(this).removeClass('overfloarauto');
                $(this).addClass('overfloarhidden');
            });
        }

        if(!!option.approveStatusSearch){
            var apply = '<div class="row nav-row"><ul class="nav nav-tabs col-lg-12 col-sm-12 col-xs-12">';
            $.each(option.nav,function(k,v){
                var is_active;
                if(k==0){
                    is_active='active';
                    $('.status-val').val(v.val);
                }
                apply += '<li role="presentation" class="nav-search '+is_active+'" navval="'+ v.val+'"><a href="javascript:void(0)">'+ v.title+'</a></li>';
            });
            apply +='</ul></div>';
            $('.search-head').before(apply);
        }
        $('.nav-search').click(function(){
            var _this=this;
            $('.nav-search').removeClass('active');
            $(_this).addClass('active');
            $('.status-val').val($(_this).attr('navval'));
            if($.isFunction(option.onAfterSearchChange)){
                option.onAfterSearchChange($('.status-val').val());
            }
            //如果当前active.li是已审批，则不需要批量处理的按钮
            var activeLiIndex = $('.nav-row li').index($('.nav-row').find('li.active'));
            if(activeLiIndex != 0){
                $('.search-head').find('.check-all').addClass('hide')
                    .end().find('.approval-btn-div').addClass('hide');
                $('.approvalStatusRow').removeClass('hide')
                    .find('#statusSearch').children().eq(0).attr('selected',true);

            }else{
                $('.search-head').find('.check-all').removeClass('hide')
                    .end().find('.approval-btn-div').removeClass('hide');

                $('.approvalStatusRow').addClass('hide');
            }
            loadleft();
        });

        if(!!option.approvaStusTypeSearch){
            var apply = '<div class="approvalStatusRow">';
            var approveStatustitle="审批状态";
            if(option.approveStatusTitle==false){
                approveStatustitle='';
            }else if(!!option.approveStatusTitle){
                approveStatustitle =option.approveStatusTitle;
            }
            apply += '<label class="approvestatustitle">'+approveStatustitle+'</label>' +
                '<select  id="statusSearch" class="selectpicker">';
            if(!!option.statusOption){
                $.each(option.statusOption,function(index,option){
                    apply += '<option value="'+ option.val+'" >'+ option.title+'</option>';
                });
            }else{
                apply+=
                    '<option value="">全部</option>' +
                    '<option value="3">审批中</option>' +
                    '<option value="4">审批通过</option>' +
                    '<option value="5">审批驳回</option>' +
                    '<option value="6">已撤销</option>';
            }

            apply +='</select><label class="status-notice"></label></div>';

            $('.search-component').before(apply);
            $('#statusSearch').children().eq(0).attr('selected',true);
            console.log('val',$('#statusSearch').val());
        }


        var selectvaluechange = function(){
            $('.status-notice').html('');
            var val = $('#statusSearch').val();
            $('.status-val').val(val);
            if(!!option.statusOption){
                $.each(option.statusOption,function(a,statusdata){
                    if (statusdata.v==val){
                        $('.status-notice').html(statusdata.n);
                    }
                });
            }
        }

        /*if(!!option.statusOption){
            $('#statusSearch').val(option.statusOption);
            //$('#statusSearch').onChange();
        }*/
        if(!firstload) selectvaluechange();

        //根据select value值判断显示内容
        $('.left-list').delegate('input[type="checkbox"]','click',function () {
                var totalLength = $('.left-list input[type="checkbox"]').length;
                var checkedLength = $('.left-list input[type="checkbox"]:checked').length;

                $('.choose-num').html(checkedLength);

                if(totalLength && totalLength == checkedLength)
                    $('.check-all').prop('checked',true);
                else if (totalLength && totalLength != checkedLength)
                    $('.check-all').prop('checked',false);

            }
        );

        //名字
        if(!!option.tokeninputurl) {
            $('.employees').tokenInput(option.tokeninputurl, {
                hintText: '请输入姓名...',
                prePopulate: [],
                tokenLimit: 1
            });
        }
        //布局bug
        if(!!option.usernameSearch){
            $('.search-head').addClass('mt-20');
        }
        //全选，反选功能
        //选择器唯一性
        $('.check-all').click(function(){
            if($('.check-all').prop('checked')){
                $('.left-list input[type="checkbox"]').prop('checked',true);
                $('.choose-num').html($('.left-list input[type="checkbox"]').length);
            }else{
                $('.left-list input[type="checkbox"]').prop('checked',false);
                $('.choose-num').html('');
            }
        });

        //搜索
        var Form;
        var FormLength;
        //鼠标滑过展开
         /*  $('#statusSearch').parents('.row').delegate('.open','mouseleave',function(){
                $('.bootstrap-select').removeClass('open');
            });
            $('#statusSearch').parents('.row').delegate('button[data-id="statusSearch"]','mouseover',function(){
                $(this).parents('.bootstrap-select').addClass('open');
         });*/

        //换就加载，
        $('#statusSearch').on('change',function(){
            var _this = this;
            $('.status-val').val($(_this).val());
            selectvaluechange();
            if($.isFunction(option.onAfterSearchChange)){
                option.onAfterSearchChange($(_this).val())
            }
           loadleft();
        });


        $(document).on('keydown', function (e) {
            var e = e || event;
            var currKey = e.keyCode || e.which || e.charCode;
            if (currKey == 13) {
                loadleft();
            }
        });
        $('.search-component').click(function(){
            //左侧数据加载
            loadleft();
        });
        $('.clear-component').click(function () {
            window.location.reload();
        });
        //左侧数据加载方法
        var loadleft = function (pageOn) {
            if($.isFunction(option.onBeforeLoadleft)){
                option.onBeforeLoadleft();
            }
            $('.choose-num').html('');
            var myForm;
            if (!!pageOn) {
                //如果是点击分页标签的，添加page字段
                if (Form.length > FormLength) {
                    Form.pop();
                }
                myForm = Form;
                myForm.push({
                    name: 'page',
                    value: pageOn
                });
            } else {
                //如果是自动加载或者搜索的
                myForm = $('.search-component-form').serializeArray();
                Form = myForm;
                FormLength = Form.length;
            }
            if(!!option.page_size){
                myForm.push({
                    name:'page_size',
                    value: option.page_size
                });
            }

           // console.warn('Form',Form);
            $.getJSON(option.urlLeft, myForm, function (ret) {
                if (firstload) {
                    var nullword = '还没有过申请哦～</br></br>have a nice day';
                } else {
                    var nullword = '木有找到哇....</br></br>搜下别的试试？';
                }
                if (ret.code == 200 || ret.rcode == 200) {
                    //封装好的加载数据方法
                    firstload = false;
                    $('.approval_num').find('span').html(ret.data.approval_num);
                    $('.application_num').find('span').html(ret.data.application_num);
                    var data = ret.data;
                    leftlength = data.data.length;

                    if(!data.data||(!!data.data&&leftlength==0)){
                        if(firstload){
                            $('.search-head').hide();
                        }
                        $('.right-info').html('<div class="null-picture">' +
                            '<h4 class="null-word">' + nullword + '</h4>' +
                            '</div>');
                        $('.left-list').addClass('hide');
                        $('.right-info').addClass('full');
                        //暂时注掉
                    } else {
                        $('.right-info').html('<div class="null-picture">' +
                            '<h4 class="null-word">点击左侧栏，内容即现 </h4>' +
                            '</div>');
                        $('.left-list').removeClass('hide');
                        $('.right-info').removeClass('full');
                        //$('.null-word').html('点击左侧栏，内容即现');
                    }
                    $('.right-info').removeClass('hide');

                    var listTemplate = Template('left-table',data);
                    //console.warn(data);
                    $('.control-table').html(listTemplate);
                    //console.log(!!option.autoload);
                    if (!!option.autoload) {
                        $('.left-list').find('tr').eq(1).click();
                    }
                    if ($.isFunction(option.onAfterLoadLeft)) {
                        option.onAfterLoadLeft()
                    }
                    if(!!option.leftscroll){
                        var tdheight = 9*54;
                        $('.control-table tr:last').after('<tr style="height:'+tdheight+'px"></tr>');
                    }
                    //全选
                    if(leftlength) {
                        $('.left-list input[type="checkbox"]').prop('checked', true);
                        $('.check-all').prop('checked', true);
                    }
                    if(!leftlength || window.location.href.indexOf('/my/approval/')!=-1){
                        //无内容时不选中
                        $('.check-all').prop('checked',false);
                        $('.left-list input[type="checkbox"]').prop('checked', false);
                    }

                    //分页标签

                    if (!!data.page) {
                       // console.log(1,!!data.page);
                        if (!pageOn || data.page.maxPage < 1) {
                           // console.warn(1);
                            $(".pagination-left").after('<div class="pagination-left"></div>').remove();
                        }
                        //console.log(paginationFlag,!pageOn,data.page.maxPage > 1);
                        if ((paginationFlag || !pageOn) && data.page.maxPage > 1) {
                            //console.warn(2);
                            $(".pagination-left").after('<div class="pagination-left"></div>').remove();
                            var maxPage = parseInt(data.page.maxPage),
                                currentPage = parseInt(data.page.currentPage);
                            $(".pagination-left").pagination({
                                //总页数
                                totalPage: maxPage,
                                //初始选中页
                                currentPage: currentPage,
                                //最前面的展现页数
                                firstPagesCount: 0, //最前面的展现页数，默认值为2
                                preposePagesCount: 2,  //当前页的紧邻前置页数，默认值为2
                                postposePagesCount: 0, //当前页的紧邻后置页数，默认值为1
                                lastPagesCount: 2,//最后面的展现页数，默认值为0
                                href: false,    //不生成链接
                                first: '', //取消首页
                                prev: '<',
                                next: '>',
                                last: '', //取消尾页
                                go: '' //取消页面跳转
                            }).on("switch", function (e, page) {
                                pageMemory = page;
                                loadleft(page);
                            });
                            //以下是已处理事件
                            $('table tbody tr:nth-child(11)').addClass('noafter');
                            if(!$('.tr-approvaling:last').hasClass('noafter')){
                                $('.tr-approvaling:last').after('<tr><td colspan="8" class="afternotice">以下为已处理事件</td></tr>');
                            };
                            paginationFlag =true;
                        }
                    } else {
                        $(".pagination-left").after('<div class="pagination-left"></div>').remove();
                    }
                } else {
                    notify.error(ret.error_msg || ret.rmessage || '操作失败');
                }
            });
        };

        //右侧信息展示
        $('.control-table').delegate('.show-info', 'click', function (e) {
            if (e.target.type != 'checkbox') {
                $('.show-info').removeClass('flag');
                var data = $(this).data();
                $(this).addClass('flag');
                $.getJSON(option.urlRight, data, function (ret) {
                    //console.warn(ret);
                    if (ret.code == 200 || ret.rcode == 200) {
                        //封装好的加载数据方法
                        var info = Template('right-show', ret);
                        $('.right-info').html(info);
                        //当界面未我的管理时，不需要订单span标签
                        if(window.location.href.indexOf('/my/manage/')!= -1){
                            $('.right-info .info-head').find('span.billStatus').remove();
                        }
                        $('.approval-history').click();
                        //成功时候调用
                        if ($.isFunction(option.onAfterLoadRight)) {
                            option.onAfterLoadRight();
                        }
                    } else {
                        notify.error(ret.error_msg || ret.rmessage || '操作失败');
                    }
                });
            } else {
                //点击checkbox添加flagcheck类作为样式记号
                if ($(e.target).prop('checked')) {
                    $(this).addClass('flagcheck');
                } else {
                    $(this).removeClass('flagcheck');
                }
            }
        });

        //初始化加载
        loadleft();
        $('.approvalStatusRow').addClass('hide');
        //如果为请假提交后跳转的，自动打开最后一个申请的假
        var str = location.href; //取得整个地址栏
        var num = str.indexOf("?");
        str = str.substr(num + 1);
        if (str == 'autuloadlast') {
            setTimeout(function () {
                //console.log(str);
                $('.tr-approvaling:last').click();
                //console.log(1);
            }, 500);
        }

        //时间
        $('.time-start-end').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                separator: '至',
                applyLabel: '确定',
                cancelLabel: '取消'
            }

        }, function (start, end, label) {
            $('.search-start').val(start.format('YYYY-MM-DD'));
            $('.search-end').val(end.format('YYYY-MM-DD'));
        }).val('');

        if (!option.widthmark) {
            var widthmark = 'widthmark';
        }

        //左侧列表100%
        $('.control-hide').click(function () {
            if ($(this).hasClass('fat')) {
                $('.right-info').show();
                $('.left-list').css('width', option.leftwidth);
                $(this).removeClass('fat').next().addClass('hide-td-narrow').removeClass('hide-td-fat');
                store.set(option.widthmark, 1);
            } else {
                $('.right-info').hide();
                $('.left-list').css('width', '100%');
                $(this).addClass('fat').next().removeClass('hide-td-narrow').addClass('hide-td-fat');
                store.set(option.widthmark, 2);
            }
        });
        //记忆功能O(∩_∩)O~
        var widthmark = store.get(option.widthmark);
        if (widthmark == 2) {
            $('.control-hide').click();
        }


        //批量处理（同意/驳回，撤销/催审）
        function getSelectedProcessData(alter, panduan, parameters) {
            var foreachdata = function (dataList, index) {
                if (option.isStruss2) {
                    console.warn(dataList);
                    $.each(dataList, function (name, value) {
                        if (parameters[name]) {
                            parameters[name].push(value);
                        } else {
                            parameters[name] = Array();
                            parameters[name].push(value);
                        }
                    });
                } else {
                    //console.log(parameters);
                    $.each(dataList, function (name, value) {
                        parameters.push({
                            name: 'data[' + index + '][' + name + ']',
                            value: value
                        });
                    });

                    if(!!option.pushsubmit){
                        $.each(option.pushsubmit,function(a,b){
                            if(alter== b.alter){
                                parameters.push({
                                    name:'data['+index+']['+ b.str+']',
                                    value: b.v? b.v:$('.reason').val()
                                });
                            }
                        })
                    }

                }
            }

            //根据参数判断，遍历table，还是单条数据
            var parameters;
            if (panduan == 'single') {
                //单条数据
                foreachdata(dataSingle, 0);
            } else {
                //遍历table
                //if($('.left-list input[type="checkbox"]:checked').length==0){
                //    notify.error('没有选中任何选项哦');
                //    return false;
                //}
                $('.left-list input[type="checkbox"]:checked').each(function (index) {
                    var dataList = $(this).data();
                    foreachdata(dataList, index);
                });
            }
            return parameters;
        }
        //弹出框，同意时，有几率重写
        function showModal(title, reason, success, dowhat, formKey) {

            var isSpecial = false;
            if (!!formKey && formKey != '/contract/dealTask/defaultapprove') {
                var isSpecialArray = formKey.split('/');
                isSpecial = '';
                $.each(isSpecialArray, function (k, v) {
                    isSpecial += v;
                });
            }
            //}
            var message;
            if (isSpecial && dowhat == 'agree') {
                message = option[isSpecial];
            } else {
                message = '<form class="form-horizontal form-agree">' +
                    '<textarea class="form-control reason" rows="3">' + reason + '</textarea>' +
                    '</form>';
            }

            if(dowhat == 'claim')
                 message = option.reimburseAddClaim;

             var options = {
                className: "time-modal",
                title: title,
                message: message,
                backdrop: true,
                onEscape: function () {
                    //关闭对话框.
                    //this.modal('hide');
                },
                buttons: {
                    cancel: {
                        label: '取消',
                        className: 'btn-default btn-cancel',
                        callback: function () {
                            //暂时不管.
                        }
                    },
                    success: {
                        label: '确定',
                        className: 'btn-primary',
                        callback: success
                    }
                }
            };
            bootbox.dialog(options);
        }

        //声明单条数据字段用来存储
        var dataSingle;

        //提交方法 参数（事件源，提交接口，同意驳回）
        var submit = function (obje, url, dowhat) {
            //dowhat是表示同意，驳回，催审，撤销
            var parameters;
            if (option.isStruss2) {
                parameters = {};
            } else {
                parameters = [];
            }
            //判断触发源是否有single class，如果有择单条提交，如果没有批量提交
            if ($(obje).hasClass('single')) {
                dataSingle = $(obje).data();

                parameters = getSelectedProcessData(dowhat, 'single', parameters);
            } else {//将checkbox或者按钮上的数据按照对应各式序列化
                parameters = getSelectedProcessData(dowhat, 'table', parameters);
            }
            reason = $('.reason').val();

            if (option.isStruss2) {
                if (dowhat == 'agree') {
                    parameters.reason = reason;
                    parameters.action_type = 1;
                } else if (dowhat == 'reject') {
                    parameters.reason = reason;
                    parameters.action_type = 2;
                } else if (dowhat == 'undo') {
                    parameters.reason = reason;
                }else if(dowhat == 'claim'){
                    parameters.taskid = parameters.taskid[0];
                }

                var arr = $('.form-agree').serializeArray();
                if (!!arr) {
                    $.each(arr, function (k, v) {
                        parameters[v.name] = v.value;
                    });
                }
            }else{
                if (dowhat == 'agree') {
                    parameters.push({
                        name: 'reason',
                        value: reason
                    });
                    parameters.push({
                        name: 'action_type',
                        value: 1
                    });
                } else if (dowhat == 'reject') {
                    parameters.push({
                        name: 'reason',
                        value: reason
                    });
                    parameters.push({
                        name: 'action_type',
                        value: 2
                    });
                } else if (dowhat == 'undo') {
                    parameters.push({
                        name: 'reason',
                        value: reason
                    });
                }else if(dowhat == 'cancel'){
                    parameters.push({
                        name:'reason',
                        value:reason
                    });
                }else if(dowhat == 'claim'){
                    parameters.taskid = parameters.taskid[0];
                }

                if(!!option.othersubmit){
                    $.each(option.othersubmit,function(c,d){
                        parameters.push({
                            name: d.n,
                            value: d.v
                        });
                    })
                }
            }

            //console.log($.param(parameters));
            //if(!$.param(parameters)||parameters.length==0){
            //    notify.error('没有选中任何选项哦');
            //    return false;
            //}
            //用返回值提交
            $.post(url, $.param(parameters), function (ret) {
                //console.warn(ret);
                if (ret.code == 200 || ret.rcode == 200) {
                    //审批
                    notify.success('操作成功');
                    if (dowhat != 'pushon') {
                        loadleft(pageMemory);
                        $('.right-info').html('<div class="null-picture">' +
                            '<h4 class="null-word">点击左侧栏，内容即现 </h4>' +
                            '</div>');
                    }

                } else {
                    notify.error(ret.error_msg || ret.rmessage || '操作失败');

                    loadleft(pageMemory);
                }
            }, 'json');

            if (!option.isStruss2) {
                $.getJSON('/aj/check/check_all', function (ret) {
                    if (ret.code == 200) {
                        var data = ret.data;
                        var total = data.total;
                        if (total != 0) {
                            $('.badge').html(total);
                            var str = '';
                            for (var i = 0; i < data.data.length; i++) {
                                str += '<li>' +
                                    '<a href="' + data.data[i].href + '" class="media" target="_Blank' + i + '">' +
                                    '<span class="media-left left" >' +
                                    data.data[i].name +
                                    '</span>' +
                                    '<span class=" label-success message_to_do right">' + data.data[i].count + '</span>' +

                                    '</a>' +
                                    '</li>';

                            }
                            $('.head-list-append').html(str).parent();
                        } else {
                            $('.head-list-append').parent().hide();
                        }
                    } else {
                        $('.head-list-append').parent().hide();
                    }

                });
            }
        }

        var lengthcheck = function (obj) {
            //console.log($(obj));
            if (!$(obj).hasClass('single')) {
                if ($('.left-list input[type="checkbox"]:checked').length == 0) {
                    notify.error('没有选中任何选项哦');
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }

        //加签
        $('body').delegate('.list-claim','click',function(){
            var that = $(this);
            var url = that.data('url');

            showModal('增加审批人', '加签', function () {
                submit(that, option.urlClaim , 'claim');
            },'claim');
        });
        //加签——modal绑定tokenInput
        $('body').delegate('[name=userid]','focus',function(){
            var $form = $('fomr.form-agree');

            $('[name=userid]').tokenInput("/aj/user/search", {
                tokenLimit: 1,
                onAdd: function (item) {
                    $form.find('[name=userid]').val(item.id);
                },
                tokenFormatter: function (item) {
                    return "<li><p>" + item['name_cn'] + "</p></li>";
                },
                onDelete: function (item) {
                    //直接清空.
                    $form.find('[name=userid]').val('');
                }
            });
        });

        //作废
        $('body').delegate('.list-delete','click',function(){
            var _this = this;
            if (!lengthcheck(_this)) {
                return false;
            }
            var formkey;
            if ($(_this).hasClass('single')) {
                formkey = $(_this).data().formkey;
            }
            showModal('作废理由', '作废', function () {
                submit(_this, option.url3, 'cancel');
            }, 'cancel', formkey);
        });

        //同意
        $('body').delegate('.list-agree', 'click', function () {
            var _this = this;
            if (!lengthcheck(_this)) {
                return false;
            }
            var formkey;
            if ($(_this).hasClass('single')) {
                formkey = $(_this).data().formkey;
            }
            showModal('审批意见', '同意', function () {
                submit(_this, option.url1, 'agree');
            }, 'agree', formkey);
        });
        //批量驳回
        $('body').delegate('.list-reject', 'click', function () {
            var _this = this;
            if (!lengthcheck(_this)) {
                return false;
            }
            showModal('审批意见', '驳回', function () {
                submit(_this, option.url2, 'reject');
            }, 'reject');
        });

        //批量-催审
        $('body').delegate('.list-pushon', 'click', function () {
            var _this = this;
            if (!lengthcheck(_this)) {
                return false;
            }
            submit(_this, option.url1, 'pushon')
        });
        //批量撤销
        $('body').delegate('.list-undo', 'click', function () {
            var _this = this;
            //console.log(!lengthcheck(_this));
            if (!lengthcheck(_this)) {
                return false;
            }
            if(!!option.undoboxremove){
                submit(_this,option.url2,'undo');
            }else{
                showModal('撤销理由','取消申请',function(){
                    submit(_this,option.url2,'undo')
                },'undo');
            }
        });
        //自定义
        if(!!option.btnfunction){
            $.each(option.btnfunction,function(k,v){
                //console.log(111);
                $('body').delegate(v.tar,'click',function () {
                    var _this =this;
                    if(!lengthcheck(_this)){
                        return false;
                    }
                    if(!!v.boxremove){
                        submit(_this,v.url, v.dowhat);
                    }else{
                        showModal(v.notice, v.placehold,function(){
                            submit(_this,v.url,v.dowhat)
                        }, v.dowhat);
                    }
                });
            });
        }

        //审批记录，接口已通
        $('.right-info').delegate('.approval-history', 'click', function () {
            $('.history-icon').removeClass('active');
            $(this).addClass('active');
            var data = $(this).data();

            if ((!data.projectname ||data.projectname!='fms') && (!!data.task_id||data.task_id=='0')) {
                $.getJSON(option.urlApproval, data, function (ret) {
                    if (ret.code == 200 || ret.rcode == 200) {
                        //封装好的加载数据方法
                        var info = Template('approve-history', ret);
                        $('.timeline-wraper').html(info);
                    } else {
                        notify.error(ret.error_msg || ret.rmessage || '操作失败');
                    }
                });
            } else  {
                var timeline_length = $('.timeline').children().length;
                if(data.projectname == 'fms' && timeline_length > 0) return;

                $('.approval-history').hide();
            }

        });
        //历史冲帐记录
        $('.right-info').delegate('.application-history', 'click', function () {
            $('.history-icon').removeClass('active');
            $(this).addClass('active');
            var data = $(this).data();
            $.getJSON(option.urlApplicationHistory, data, function (ret) {
                if (ret.code == 200 || ret.rcode == 200) {
                    //封装好的加载数据方法
                    var info = Template('aplication-history', ret);
                    $('.timeline-wraper').html(info);
                    $('.timeline-entry:first').addClass('active');
                } else {
                    notify.error(ret.error_msg || ret.rmessage || '操作失败');
                }
            });
        });
        $('.right-info').delegate('.speedim', 'click', function () {
            window.location.href = "speedim://open/";
        });

        return this;
    }

    exports.approval = approval;
});