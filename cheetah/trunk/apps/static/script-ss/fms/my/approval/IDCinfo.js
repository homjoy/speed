fml.define("fms/my/approval/IDCinfo",['jquery','component/notify','component/approval'],function(require,exports){
    var $ = require('jquery');
    var approval = require('component/approval');
    var notify = require('component/notify');

    var option={
        btns:[
            {
                title:'批量同意',
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
        usernameSearch:false,
        approveStatusSearch:true,
        approveStatusTitle:false,
        identifierSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_approval/IDCinfo_list',
        urlRight :'/aj/IDCinfo/detail',
        url1:'/aj/IDCinfo/approval',
        url2:'/aj/IDCinfo/approval',
        url3:'/aj/IDCinfo/cancel',
        tokeninputurl:'/aj/user/search',
        nav:[{title:'待审批',val:'2'},{title:'已审批',val:'3,4',addclass:'btn-remove',data:"[{title:'审批通过',v:'3'},{title:'驳回',v:'4'}]"}],
        statusOptionVal:"1",
        searchbtn:true,
        clearbtn:true
    };
    approval.approval(option);
    console.log('options',option);

    $(document).on('keyup','[name=identifier]',function(e){
        var input = $(this);
        if (e.which === 13 /*&& input.val()*/) {
            input.next('.search-component').click();
        }
    });
    $('.right-info').delegate('.activation','click',function(){
        $('.needfill').find('span').addClass('hide').next().removeClass('hide');
        $(this).hide();
        $('.save').removeClass('hide');
        $('.list-agree').attr('disabled','disabled');
    });
    $('.right-info').delegate('.save','click',function(){
        $('.list-agree').removeAttr('disabled');
        var _this = this;
        var url = $(this).data().url;
        var data=[];

        data.push({
            name:'attribution',
            value:$('.attribution').val()
        });
        data.push({
            name:'billId',
            value:$('#billid').val()
        });

        $.post(url,data,function(ret){
            if(ret.rcode=='200'){
                $(_this).addClass('hide');
                $('.activation').show();
                $('.list-reject,.list-agree').removeClass('hide');
                $('.needfill').find('span').removeClass('hide').next().addClass('hide');
                $.each($('.needfill'),function(k,v){
                    var a = $(v).find('span').next().val();
                    $(v).find('span').html(a);
                });
                var a = $('.attribution').val();
                var b = $('option[value="'+a+'"]').html();
                $('.attribution').prev('span').html(b);
                notify.success(ret.rmessage ||'操作成功');
            }else{
                notify.error(ret.rmessage || '操作失败');
            }
        },'json');
    });

});
