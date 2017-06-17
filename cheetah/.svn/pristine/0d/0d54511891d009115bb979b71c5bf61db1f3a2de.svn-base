fml.define('fms/my/manage/pay', ['jquery', 'plugin/artTemplate', 'plugin/bootstrap/datepicker', 'component/TreeSelect','plugin/tokeninput','component/notify', 'component/pagination'], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');
    var template = require('plugin/artTemplate');

    /**
     * 加载部门树.
     */
    $.post('/aj/query/depart', function (data) {
        $("input[name=departmentId]").TreeSelect({
            multiple: true,
            data: data
        });
    }, 'json');

    /**
     * 日期控件.
     */
    $("#sTime,#eTime").datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true,
        'todayHighlight': true
    });

    /**
     * 加载人员信息
     */
    $('input[name=approId]').tokenInput("/aj/user/search", {
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
                    $('input[name=approId]').val(item.id);
                }
            });
        },
        tokenFormatter:function(item){
            return "<li><p>" + item['name_cn'] + "</p></li>";
        },
    });

    /**
     * 付款按钮点击
     */
    $('.toolbar .btn-pay').on("click", function (e) {
        e.preventDefault();
        var self = $(this);

        var cpbIds = getCheckedIds();
        if (!cpbIds.length) {
            notify.warning('未选中任何行.');
            return;
        }


        // 防止二次点击
        self.prop('disabled', true);
        $.post('/aj/pay/confirm', {'cpbId': cpbIds.join(',')}, function (data) {
            if (data.rcode != 200) {
                return notify.error(data.rmessage || '付款出错.');
            }

            self.prop('disabled', false);
            notify.success("付款成功.");
            //刷新付款列表
            renderPayList();
        }, "json");
    });

    /**
     *
     */
    $('.toolbar .btn-rej').on("click", function (e) {
        var self = $(this);
        var cpbIds = getCheckedIds();
        if (!cpbIds.length) {
            notify.warning('未选中任何行.');
            return;
        }

        if (cpbIds.length !== 1) {
            notify.warning('驳回不能进行批量操作.');
            return;
        }

        var formHtml = template("rejectFormTpl", {
            cpbId: cpbIds[0] || '0'
        });

        self.prop('disabled', true);
        notify.formDialog('付款驳回', formHtml, function () {
            //$("#rejectForm").serialize()
            $.post('/aj/pay/reject', $("#rejectForm").serialize(), function (data) {
                if (data.rcode != 200) {
                    return notify.error(data.rmessage || '驳回出错.');
                }

                self.prop('disabled', false);
                notify.success("驳回成功.");
                //刷新付款列表
                renderPayList();
            }, "json");
            //return false;
        });
    });


    /*
     * 加载付款列表
     *
     */
    function loadPage(p,size) {
        p = p || 1;
        size = size || 10;

        var params = [{name: "page", value: p}, {name: "ps", value: size}];
        var url = '/aj/my_manage/pay_list';

        url = url + (url.indexOf('?') >= 0 ? '&' : '?') + $.param(params);

        //原有查询列表 /aj/my_manage/pay_list
        $.ajax({
            url: url,
            type: "POST",
            data: $('.query-form').serializeArray(),
            dataType: "json",
            success: function (resp) {
                if (resp.rcode != 200) {
                    notify.error(resp.rmessage || '查询有误');
                    return;
                }

                if (resp.data && resp.data.length > 0) {
                    var tBody = template('payList', {pays: resp.data});
                    $('#tb-pay tbody').html(tBody);
                } else {
                    $('#tb-pay tbody').html('');
                }
            }
        });
    }

    /**
     *
     */
  /* function renderPayList() {
        $.getJSON('/aj/my_manage/pay_list', function (resp) {
            if (resp.rcode != 200) {
                $('#tb-pay tbody').html('');
                return ;
            }

            var pagination = $("#pagination");
            var pageObj = pagination.data('pagination');
            if(pageObj){
                pageObj.destroy();
            }

            $("#pagination").pagination({
                totalPage: parseInt(resp.page.maxPage) || 1,
                currentPage: parseInt(resp.page.currentPage) || 1,
                lastPagesCount: 1
            }).on("switch", function (e, page) {
                loadPage(page);
            });

            if (resp.data && resp.data.length > 0) {
                var tBody = template('payList', {pays: resp.data});
                $('#tb-pay tbody').html(tBody);
            } else {
                $('#tb-pay tbody').html('');
            }
        });
    }*/

    /*
     * 加载付款列表
     * */
    //renderPayList();

    //查询按钮触发事件
   $('.btn-query').on('click',function(e){
       e.preventDefault();

       var page = 1,pageSize = 10;
       var params = [{name: "page", value: page}, {name: "ps", value: pageSize}];
       var url = '/aj/my_manage/pay_list';

       url = url + (url.indexOf('?') >= 0 ? '&' : '?') + $.param(params);


       $.ajax({
           url:url,
           type:"POST",
           data:$('.query-form').serializeArray(),
           dataType:"json",
           success:function(resp){
               if (resp.rcode != 200) {
                    notify.error(resp.rmessage || '查询有误');
                    return ;
               }

               if(!resp.data.length){
                   //查询结果为空
                   $('#tab-pay').css('visibility','hidden');

                   if($('.empty-content').length > 0) {
                       $('.empty-content').css({'display':'block'});

                       return ;
                   }


                   $('#tab-pay').before('<div class="empty-content"><p>很遗憾，没有找到任何数据.</p></div>');

                   return ;
               }

               $('#tab-pay').css('visibility','visible')
                   .prev('.empty-content').css({'display':'none'});
               var pagination = $("#pagination");
               var pageObj = pagination.data('pagination');
               if(pageObj){
                   pageObj.destroy();
               }

               $("#pagination").pagination({
                   totalPage: parseInt(resp.page.maxPage) || 1,
                   currentPage: parseInt(resp.page.currentPage) || 1,
                   lastPagesCount: 1
               }).on("switch", function (e, page) {
                   loadPage(page);
               });

               if (resp.data && resp.data.length > 0) {
                   var tBody = template('payList', {pays: resp.data});
                   $('#tb-pay tbody').html(tBody);
               } else {
                   $('#tb-pay tbody').html('');
               }
           }
       });
   });

    /*
     * 付款列表全选
     * */
    $(".check-all").on("change", function () {
        var checked = $(this).prop('checked');
        var checkbox = $("#tb-pay tbody input[type=checkbox]");
        checkbox.prop("checked", checked);
    });


    /**
     * 批量获取选中的id
     * @returns {*|jQuery}
     */
    function getCheckedIds() {
        return $('#tb-pay').find('tbody tr input[type=checkbox]:checked')
            .map(function () {
                return $(this).val();
            }).get();
    }
});
