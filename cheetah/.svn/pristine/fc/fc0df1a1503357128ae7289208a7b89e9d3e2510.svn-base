fml.define("fms/my/approval/budget",['jquery','component/approval'],function(require,exports){
    var $ = require('jquery');
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
        contractdealTaskneedapprove:'<form class="form-horizontal form-agree">' +
        '<textarea class="form-control reason" rows="3">同意</textarea>' +
        '<label class="checkbox-inline control-label">' +
        '<input type="checkbox" name="is_check" class="checkbox ischecked" value="1">进一步审批</label>' +
        '</form>',
        isStruss2:true,
        timeSearch:true,
        departSearch:false,
        usernameSearch:true,
        approveStatusSearch:true,
        approveStatusTitle:false,
        identifierSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_approval/budget_list',
        urlRight :'/aj/budget/detail',
        url1:'/aj/budget/approval',
        url2:'/aj/budget/approval',
        tokeninputurl:'/aj/user/search',
        nav:[{title:'待审批',val:'1'},
            {title:'已审批',addclass:'btn-remove',val:'2,3,4,6',data:"[{title:'全部',v:'2,3,4,5'},{title:'审批中',v:'2'},{title:'审批通过',v:'3'},{title:'驳回',v:'4'}]"}
        ],
        approvaStusTypeSearch:true,
        approveStatusTitle:'审批状态',
        statusOption:[
            {title:'请选择',val:'2,3,4,5'},
            {title:'审批中',val:'2'}, {title:'审批通过',val:'3'},{title:'驳回',val:'4'}],
        searchbtn:true,
        statusOptionVal:"1",
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


    $(document).on('click',".data-preview .btn",function(e){
        e.preventDefault();
        var btn = $(this);
        var div = btn.parent().next();
        if(div && div.length){
            div.toggle();
        }
        return false;
    });
});
