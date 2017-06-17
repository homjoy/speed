fml.define("fms/my/apply/budget",['jquery','component/approval'],function(require,exports){
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
        urlLeft: '/aj/my_apply/search_budgets',
        urlRight :'/aj/budget/detail',
        url1:'/aj/budget/hasten',
        url2:'/aj/budget/revoke',
        nav:[{title:'待审批',val:'0,2,6'},{title:'已审批',val:'3,4,5',addclass:'btn-remove'}],
        statusOption:[
            {title:"全部",v:""},
            {title:"草稿",v:"0"},
            {title:"审批中",v:"2"},
            {title:"审批通过",v:"3"},
            {title:"审批驳回",v:"4"},
            {title:"已付款",v:"5"},
            {title:"已撤销",v:"6"}
        ],
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


