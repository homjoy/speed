fml.define('fms/IDCinfo/view', ['jquery','component/notify','plugin/bootbox','fms/common/utils'], function (require, exports) {
    "use strict";
    var $ = require('jquery'),notify = require('component/notify'), utils = require('fms/common/utils');
    var bootbox = require('plugin/bootbox');

   /* $('.panel-body').delegate('.activation','click',function(){
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
    });*/

    console.log("123");

    //同意
    $('body').delegate('.btn-agree', 'click', function () {
        var _this = this;
        var url = $(_this).data().url;
        console.log(url);
        showModal('审批意见', '同意', function () {
            submit(_this,url , 'agree');
        });
    });
    //驳回
    $('body').delegate('.btn-reject', 'click', function () {
        var _this = this;
        var url = $(_this).data().url;
        console.log(url);
        showModal('审批意见', '驳回', function () {
            submit(_this,url, 'reject');
        });
    });


    var dataSingle;

    //提交方法 参数（事件源，提交接口，同意驳回）
    var submit = function (obj, url, dowhat) {
        //dowhat是表示同意，驳回，催审，撤销
        var parameters = {};

        dataSingle = $(obj).data();
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
        }
        var arr = $('.form-agree').serializeArray();
        console.log(arr);
        if (!!arr) {
            $.each(arr, function (k, v) {
                parameters[v.name] = v.value;
            });
        }


        //用返回值提交
        console.log('url',url);
        console.log('');
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

    function showModal(title, reason, success) {

        var message = '<form class="form-horizontal form-agree">' +
            '<textarea class="form-control reason" rows="3">' + reason + '</textarea>' +
            '</form>';

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