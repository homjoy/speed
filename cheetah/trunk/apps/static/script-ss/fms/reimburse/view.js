fml.define('fms/reimburse/view', ['jquery','component/notify','plugin/bootbox','plugin/tokeninput','fms/common/utils'], function (require, exports) {
    "use strict";
    var $ = require('jquery'),notify = require('component/notify'), utils = require('fms/common/utils');
    var bootbox = require('plugin/bootbox');

    $('.panel-body').delegate('.activation','click',function(){
        $('.needfill').find('span').addClass('hide').next().removeClass('hide');
        $(this).hide();
        $('.save').removeClass('hide');
        $('.list-agree').attr('disabled','disabled');
    });
    $('.panel-body').delegate('.save','click',function(){
        $('.list-agree').removeAttr('disabled');
        var _this = this;
        var url = $(this).data().url;
        var data=[];
        $.each($('.needforech'),function(a,b){
            $.each($(b).find('.needfillDetails'),function(k,v){
                $(v).find('input').each(function(key,val){
                    data.push({
                        name:$(val).attr('name1')+'['+k+'].'+$(val).attr('name2'),
                        value:$(val).val()
                    });
                });
            });
        });

        if($('.attribution').length!=0){
            data.push({
                name:$('.attribution').attr('name')+'.attribution',
                value:$('.attribution').val()
            });
        }
        data.push({
            name:$('#billid').attr('name')+'.id',
            value:$('#billid').val()
        });
        data.push({
            name:'flag',
            value:'upd'
        })

        $.post(url,data,function(ret){
            var ret = JSON.parse(ret);
            console.log(ret);
            if(ret.rcode=='200'){
                $(_this).addClass('hide');
                $('.activation').show();
                $('.list-reject,.list-agree').removeClass('hide');
                $('.needfill').find('span').removeClass('hide').next().addClass('hide');
                $.each($('.needfill'),function(k,v){
                    var a = $(v).find('span').next().val();
                    $(v).find('span').html(a);
                });
                notify.success('操作成功');
            }else{
                notify.error(ret.rmessage || '操作失败');
            }
        });
    });

    //修改金额时，同步表格中的报销总金额
    $('body').delegate('[name2=checkAmount]','blur',function(){
        var checkAmount = 0,totalAmount = 0;
        var $tbody = $(this).closest('.needforech'),$amountInput = $tbody.find('[name2=checkAmount]'),
            $amountSpanValues = $('.panel-body').find('.span-highlight').next();

        $amountInput.each(function(index,item){
            checkAmount += utils.getCurrencyValue(item.value);
        });

        //同步表格总金额
        $tbody.find('.span-highlight').next().text(utils.formatCurrency(checkAmount));

        $amountSpanValues.each(function(){
            totalAmount += utils.getCurrencyValue($(this).text());
        });

        $('.totalReimburse').text(utils.formatCurrency(totalAmount));
    });

    //催票
    $('body').delegate('.btn-prompt','click',function(){
        var that = $(this);
        var id = that.data('id'),type = that.data('type');

        that.prop('disabled',true);

        $.ajax({
            url:'/aj/reimburse/prompt',
            type:'post',
            data:{'id':id,'type':type},
            dataType:'json',
            success:function(resp){
                if(resp.rcode != 200)
                    return notify.error(resp.rmessage || '出错了');

                notify.success('催票成功啦！');
                that.prop('disabled',false);
            }
        });
    });

    //加签
    $('body').delegate('.list-claim','click',function(){
        var that = $(this);
        var url = that.data('url');

        showModal('增加审批人', '加签', function () {
            submit(that, url , 'claim');
        });
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

    //转移
    $('body').delegate('.list-shift','click',function(){
        var that = $(this);
        var url = that.data('url');

        showModal('审批意见', '转移', function () {
            submit(that,url , 'shift');
        });
    });
    //同意
    $('body').delegate('.list-agree', 'click', function () {
        var _this = this;
        var url = $(_this).data().url;

        showModal('审批意见', '同意', function () {
            submit(_this,url , 'agree');
        });
    });
    //驳回
    $('body').delegate('.list-reject', 'click', function () {
        var _this = this;
        var url = $(_this).data().url;

        showModal('审批意见', '驳回', function () {
            submit(_this,url, 'reject');
        });
    });


    var dataSingle;

    //提交方法 参数（事件源，提交接口，同意驳回）
    var submit = function (obje, url, dowhat) {
        //dowhat是表示同意，驳回，催审，撤销
        var parameters = {};
        //判断触发源是否有single class，如果有择单条提交，如果没有批量提交
        dataSingle = $(obje).data();
        $.each(dataSingle, function (name, value) {
            if (parameters[name]) {
                parameters[name].push(value);
            } else {
                parameters[name] = Array();
                parameters[name].push(value);
            }
        });

        var reason = $('.reason').val();
        if (dowhat == 'agree') {
            parameters.reason = reason;
            parameters.action_type = 1;
        } else if (dowhat == 'reject') {
            parameters.reason = reason;
            parameters.action_type = 2;
        } else if (dowhat == 'undo') {
            parameters.reason = reason;
        } else if (dowhat == 'shift'){
            parameters.reason = reason;
            parameters.action_type = 1;
        } else if(dowhat == 'claim'){
            parameters.taskid = parameters.taskid[0];
        }

        var arr = $('.form-agree').serializeArray();

        if (!!arr) {
            $.each(arr, function (k, v) {
                parameters[v.name] = v.value;
            });
        }

        //用返回值提交
        $.post(url, $.param(parameters), function (ret) {
            if (ret.code == 200 || ret.rcode == 200) {
                //审批
                notify.success('操作成功');
                window.location.reload();
            } else {
                notify.error(ret.error_msg || ret.rmessage || '操作失败');
            }
        }, 'json');
    }
    //弹出框，同意时，有几率重写
    function showModal(title, reason, success) {


        var message = '<form class="form-horizontal form-agree">' +
                '<textarea class="form-control reason" rows="3">' + reason + '</textarea>' +
                '</form>';

        if(reason == '转移')
            message = '<form class="form-horizontal form-agree">'
            +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">转移给</label><div class="col-lg-8 col-sm-8 col-xs-8"><select name="transfer" class="form-control" style="height:33px"><option value="YL">优璃</option><option value="SM">速美</option><option value="HD">花钿</option><option value="MLS">美丽说</option></select></div></div>'
            +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">理由</label><div class="col-lg-8 col-sm-8 col-xs-8"><textarea class="form-control reason" rows="3"></textarea> </div></div>'
            +'</form>';
        else if (reason == '加签'){
            message = '<form class="form-horizontal form-agree">'
                +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">审批人</label><div class="col-lg-8 col-sm-8 col-xs-8"><input name="userid" class="form-control"></div></div>'
                +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">理由</label><div class="col-lg-8 col-sm-8 col-xs-8"><textarea class="form-control" name="reason" rows="3"></textarea> </div></div>'
                +'</form>';
        }

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
});