fml.define("speed/administration/fastmail/my",['jquery','component/approval','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');
    store.set('leave-approval',window.location.pathname);

    var option={
        nav:[{title:'审批中',val:'3'},{title:'已审批',val:'4,5,6',addclass:'btn-remove'}],
        othersubmit:[{n:'type',v:'3'}],
        leftauto:true,
        page_size:7,
        searchbtn:true,
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_apply_get?type=3',
        urlRight :'/aj/administration/express_approve_request',
        url1:'/aj/administration/order_reminder',
        url2:'/aj/administration/order_revoke',
        urlApproval:'/aj/administration/express_approve_list',
        tokeninputurl:'/aj/address/ajax_search_name',
        timeSearch:true,
        departSearch:false,
        usernameSearch:false,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        controltablestyle:'style="min-height:416px;height:416px"',
        headercheckOthers:''

    }
    approval.approval(option);
});