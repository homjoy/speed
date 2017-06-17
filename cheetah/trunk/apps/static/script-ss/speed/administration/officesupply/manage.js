fml.define("speed/administration/officesupply/manage",['jquery','component/approval','plugin/bootbox','plugin/store','component/notify'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');
    var bootbox = require('plugin/bootbox');
    var notify = require('component/notify');

    var option={
        btnfunction:[{tar:'.list-reject2',boxremove:false,url:'/aj/administration/supply_status_change',dowhat:'reject2',notice:'审批意见',placehold:'驳回'}],
        btns:[
            {
                title:'通知领取',
                class:'btn btn-agree list-pushon btn-xs'
            },
            {
                title:'发放',
                class:'btn btn-warning list-undo btn-xs'
            }
        ],
        pushsubmit:[{alter:'reject2',str:'reject_reason'},{alter:'reject2',str:'output',v:'3'},{alter:'undo',str:'output',v:'1'}],
        leftauto:true,
        searchbtn:true,
        isStruss2:false,
        timeSearch:true,
        daterangepickertitle:'申请日期',
        status_name:'output',
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        headercheckOthers:'',
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_approve_get?type=4',
        urlRight :'/aj/administration/supply_approve_request',
        url1:'/aj/administration/supply_output',
        url2:'/aj/administration/supply_output',
        urlApproval:'/aj/administration/supply_approve_list',
        nav:[{title:'未发放',val:'2'},{title:'已处理',val:'1,3',addclass:'btn-remove'}],
        othersubmit:[{n:'type',v:'4'}],
        undoboxremove:true,
        controltablestyle:'style="min-height:416px;height:416px"',
        page_size:7,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);

    $('.tab-pane').delegate('.more','click',function(){
        $(this).remove();
        $('.timeline-entry').removeClass('hide');
    });
    $('.btn-export').click(function(){
        if(!$('.search-start').val()||!$('.search-end').val()){
            notify.error('填写下日期再导出哈，O(∩_∩)O~');
        }else{
            window.open('/export/express?start_date='+$('.search-start').val()+'&end_date='+$('.search-end').val());
        }
    });

});
