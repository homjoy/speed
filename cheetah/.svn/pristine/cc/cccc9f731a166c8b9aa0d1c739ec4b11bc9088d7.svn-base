fml.define('fms/my/manage/loan', ['jquery','component/notify','component/approval','plugin/bootbox'], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
    var approval = require('component/approval');
    var bootbox = require('plugin/bootbox');

    var option={
       btns:[
            {
                title:'同意',
                class:'btn btn-agree list-agree btn-xs'
            },
            {
                title:'驳回',
                class:'btn btn-danger list-reject btn-xs'
            }
        ],
        showLoading:false, //不显示loading
        isStruss2:true,
        timeSearch:true,
        departSearch:false,
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_manage/loan',
        urlRight :'/aj/loan/detail',
        url1:'/aj/loan/loanrequestpayconfirm',
        url2:'/aj/loan/loanrequestreject',
        tokeninputurl:'/aj/user/search',

        nav:[{title:'未付款',val:'3'},{title:'已付款',val:'5,8'},{title:'已还款',val:'7'}],
        searchbtn:true,
        clearbtn:true
    };
    approval.approval(option);

    $(document).on('keyup','[name=identifier]',function(e){
        var input = $(this);
        if (e.which === 13 /*&& input.val()*/) {
            input.next('.search-component').click();
        }
    });


    //还款触发事件
    $('.right-info').delegate('.list-reverse', 'click', function () {

            var _this = this;
            var billid = $(this).data().id;
        var options = {
            className: "time-modal",
            title: '还款',
            message: '<form class="form-horizontal form-reverse"><input type="hidden" name="billId" value="'+billid+'"/>'
            +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">冲账金额</label><div class="col-lg-8 col-sm-8 col-xs-8"><input type="text" class="form-control" name="money"/></div></div>'
            +'<div class="form-group"><label class="col-lg-3 col-sm-3 col-xs-3 control-label">说明</label><div class="col-lg-8 col-sm-8 col-xs-8"><input type="text" class="form-control" placeholder="非必填" name="remark"/></div></div>'
            +'</form>',
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
                    callback: function(){
                        var formdata = $('.form-reverse').serialize();
                        $.post('/aj/loan/repayloanrequest',formdata, function (ret) {
                            if (ret.code == 200 || ret.rcode == 200) {
                                //审批
                                notify.success('操作成功');
                                //window.location.reload();
                            } else {
                                notify.error(ret.error_msg || ret.rmessage || '操作失败');
                            }
                        }, 'json');
                    }
                }
            }
        };
        bootbox.dialog(options);
    });

    $('.right-info').delegate('.history-icon','click',function(){
        $('.history-icon').removeClass('active');
        $(this).addClass('active');
        if($(this).hasClass('reverse')){
            $('.timeline-wraper').addClass('hide');
            $('.timeline-wraper-reverse').removeClass('hide');
        }else{
            $('.timeline-wraper').removeClass('hide');
            $('.timeline-wraper-reverse').addClass('hide');
        }
    });
});