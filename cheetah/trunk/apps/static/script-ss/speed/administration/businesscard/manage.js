fml.define("speed/administration/businesscard/manage",['jquery','component/approval','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');

    store.set('leave-approval',window.location.pathname);

    var option={
        onAfterSearchChange:function(val){
            if(val!=2){
                $('.approval-btn-div').hide();
                $('.check-all').hide();
                //$('.addpage').remove();
            }else{
                //if($('.addpage').length==0){
                //    $('.status-val').after('<input type="hidden" class="addpage" name="page_size" value="9999">');
                //}
                $('.approval-btn-div').show();
                $('.check-all').show();
            }
        },
        btnfunction:[{tar:'.list-reject2',boxremove:false,url:'/aj/administration/card_status_change',dowhat:'reject2',notice:'审批意见',placehold:'驳回'}],
        btns:[

            {
                title:'通知领取',
                class:'btn btn-agree list-pushon btn-xs'
            },
            {
                title:'已发放',
                class:'btn btn-warning list-undo btn-xs'
            },
            {
                title:'驳回',
                class:'btn btn-danger list-reject2 btn-xs'
            }
        ],
        pushsubmit:[{alter:'reject2',str:'reject_reason'},{alter:'reject2',str:'output',v:'3'},{alter:'undo',str:'output',v:'1'}],
        searchbtn:true,
        isStruss2:false,
        timeSearch:true,
        leftauto:true,
        status_name:'output',
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        headercheckOthers:'',
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_approve_get?type=2',
        urlRight :'/aj/administration/get_approve_request',
        url1:'/aj/administration/card_output',
        url2:'/aj/administration/card_status_change',
        urlApproval:'/aj/administration/card_approve_list',
        nav:[{title:'未发放',val:'2'},{title:'已审批',val:'1,3',addclass:'btn-remove'}],
        controltablestyle:'style="min-height:416px;height:416px;"',
        //rightinfostyle:'min-height:416px;height:416px',
        undoboxremove:true,
        page_size:7,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);

    $('.tab-pane').delegate('.more','click',function(){
        $(this).remove();
        $('.timeline-entry').removeClass('hide');
    });
});
