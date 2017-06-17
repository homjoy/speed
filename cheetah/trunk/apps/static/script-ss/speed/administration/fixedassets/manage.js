fml.define("speed/administration/fixedassets/manage",['jquery','component/select','component/approval','plugin/bootbox','plugin/store','component/notify'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');
    var bootbox = require('plugin/bootbox');
    var notify = require('component/notify');

    var option={
        btnfunction:[{tar:'.list-reject2',boxremove:false,url:'/aj/administration/assets_status_change',dowhat:'reject2',notice:'审批意见',placehold:'驳回'}],
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
        leftauto:true,
        searchbtn:true,
        isStruss2:false,
        timeSearch:true,
        daterangepickertitle:'寄出日期',
        status_name:'output',
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        headercheckOthers:'',
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_approve_get?type=5',
        urlRight :'/aj/administration/assets_approve_request',
        url1:'/aj/administration/assets_output',
        url2:'/aj/administration/assets_status_change',
        urlApproval:'/aj/administration/approve_assets_info',
        nav:[{title:'待发放',val:'2'},{title:'已发放',val:'1,3',addclass:'btn-remove'}],
        othersubmit:[{n:'type',v:'5'}],
        undoboxremove:true,
        pushsubmit:[{alter:'reject2',str:'reject_reason'},{alter:'reject2',str:'output',v:'3'},{alter:'undo',str:'output',v:'1'}],

        //other:'<a href="javascript:void(0)" class="btn btn-export btn-agree btn-xs">导出</a>',
        controltablestyle:'style="min-height:416px;height:416px"',
        page_size:7,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);
    $('.right-info').delegate('.prapareresaultbtn','click',function(){
        var _this = this;
        var prevdiv = $(_this).prev();
        if(!$(prevdiv).find('.prapareresault').hasClass('hide')){
            $(prevdiv).find('hr,.prapareresault,.prapareresaultspan').addClass('hide');
        }else{
            $(prevdiv).find('hr,.prapareresault,.prapareresaultspan').removeClass('hide');
        }
    })

});
