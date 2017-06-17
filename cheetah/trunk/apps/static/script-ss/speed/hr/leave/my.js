fml.define("speed/hr/leave/my",['jquery','component/approval','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');
    store.set('leave-approval',window.location.pathname);

    var option={
        onAfterLoadLeft:function(){
            var $ = require('jquery');
            var store = require('plugin/store');
            var WIZARD_CACHE_KEY = 'pass-leave-my-wizard1';

            ////无需进行引导
            if (store.get(WIZARD_CACHE_KEY)) {
                return;
            }
            setTimeout(function(){
                $('.effect').addClass('mainnav-sm');
            },1000);
            var wizardHTML = '<div class="wizard-container">' +
                '</div>' +
                '<div class="wizard wizard-leave-list"></div>';

            var wizard = $(wizardHTML);

            wizard.appendTo("body");
            wizard.on('click', function (e) {
                e.preventDefault();
                $('.wizard-leave-list').addClass('hide');
                wizard.fadeOut(function () {
                    $(this).remove();
                });
                store.set(WIZARD_CACHE_KEY, true);
                return false;
            });
        },
        panelinfostyle:'height:500px',
        leftauto:true,
        searchbtn:true,
        leftwidth:'326px',
        page_size:7,
        urlLeft: '/aj/hr/leave_list_get',
        urlRight :'/aj/hr/get_approve_request',
        url1:'/aj/hr/leave_reminder',
        url2:'/aj/hr/order_revoke',
        othersubmit:[{n:'type',v:'1'}],
        urlApproval:'/aj/hr/approve_leave_info',
        urlApplicationHistory:'/aj/hr/history_leave_list',
        tokeninputurl:'/aj/address/ajax_search_name',
        timeSearch:true,
        departSearch:false,
        usernameSearch:false,
        approveStatusSearch:true,
        identifierSearch:false,
        nav:[{title:'审批中',val:'3'},{title:'已审批',val:'4,5,6',addclass:'btn-remove'}],
        clearbtn:true,
        controltablestyle:'style="min-height:416px;height:416px;"',
        //rightinfostyle:'min-height:416px;height:416px',
        other:'<a href="/home/window/?f=document&s=all" target="_blank" class="old-info">查看过往数据</a>',
        headercheckOthers:''

    }
    approval.approval(option);
});