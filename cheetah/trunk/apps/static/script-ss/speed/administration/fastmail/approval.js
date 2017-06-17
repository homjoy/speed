fml.define("speed/administration/fastmail/approval",['jquery','component/approval','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');

    store.set('leave-approval',window.location.pathname);

    var option={
        onAfterSearchChange:function(val){
            if(val!=3){
                $('.addpage').val('7');
            }else{
                $('.addpage').val('500');
            }
        },
        onBeforeLoadleft:function(){
            if($('.addpage').length==0){
                $('.status-val').after('<input type="hidden" class="addpage" name="page_size" value="500">');
            }
        },
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
        leftauto:true,
        searchbtn:true,
        isStruss2:false,
        timeSearch:true,
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        headercheckOthers:'',
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_approve_get?type=3',
        urlRight :'/aj/administration/express_approve_request',
        url1:'/aj/administration/order_process',
        url2:'/aj/administration/order_process',
        urlApproval:'/aj/administration/express_approve_list',
        nav:[{title:'待审批',val:'3'},{title:'已审批',val:'4,5,6',addclass:'btn-remove'}],
        othersubmit:[{n:'type',v:'3'}],
        controltablestyle:'style="min-height:416px;height:416px"',
        rightauto:true,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);
});
