fml.define('fms/my/approval/contract', ['jquery','component/notify','component/approval'], function (require, exports) {

    var $ = require('jquery');
    var notify = require('component/notify');
    var approval = require('component/approval');

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
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:true,
        contractnoSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_approval/contract_list',
        urlRight :'/aj/contract/detail',
        url1:'/aj/contract/approval_all',
        url2:'/aj/contract/approval_all',
        tokeninputurl:'/aj/user/search',
        "contractdealTaskneedapprove":'<form class="form-horizontal form-agree">'
        +'<textarea placeholder="不超过200字" class="form-control approval-info" style="min-height:80px" name="reason">同意</textarea>'
        + '<div class="checkbox"><label><input type="checkbox" id="isChecked" name="is_check" value="1"/>需要进一步审批</label>' +
        '</form>',
        nav:[{title:'待审批',val:'1'},
            {title:'已审批',addclass:'btn-remove',val:'2,3,4,5',data:"[{title:'全部',v:'2,3,4,5'},{title:'审批中',v:'2'},{title:'审批通过',v:'3'},{title:'驳回',v:'4'},{title:'已归档',v:'5'}]"}
        ],
        approvaStusTypeSearch:true,
        approveStatusTitle:'审批状态',
        statusOption:[
            {title:'请选择',val:'2,3,4,5'},
            {title:'审批中',val:'2'}, {title:'审批通过',val:'3'},{title:'驳回',val:'4'},{title:'已归档',val:'5'}],
        searchbtn:true,
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
});