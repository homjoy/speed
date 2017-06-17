fml.define("fms/my/apply/loan",['jquery','component/approval'],function(require,exports){
    var $ = require('jquery');
    var approval = require('component/approval');

    var option={
        btns:[
            {
                title:'催审',
                class:'btn btn-warning list-pushon btn-xs'
            }
        ],
        showLoading:false, //不显示loading
        isStruss2:true,
        timeSearch:true,
        departSearch:false,
        usernameSearch:false,
        approveStatusSearch:true,
        identifierSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_apply/loan',
        urlRight :'/aj/loan/detail',
        url1:'/aj/loan/hasten',
        url2:'/aj/loan/revoke',
        nav:[{title:'待审批',val:'0,2,6'},{title:'已审批',val:'3,4,5,7',addclass:'btn-remove'}],
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


