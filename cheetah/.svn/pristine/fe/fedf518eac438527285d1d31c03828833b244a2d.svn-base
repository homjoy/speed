
fml.define("fms/my/apply/IDCinfo",['jquery','component/approval'],function(require,exports){
    var $ = require('jquery');
    var approval = require('component/approval');

    var option={
        showLoading:false, //不显示loading
        isStruss2:true,
        timeSearch:true,
        departSearch:false,
        usernameSearch:false,
        approveStatusSearch:true,
        identifierSearch:true,
        headercheckOthers:'',
        leftwidth:'366px',
        urlLeft: '/aj/my_apply/search_IDCinfos',
        urlRight :'/aj/IDCinfo/detail',
        url2:'/aj/IDCinfo/revoke',
        nav:[{title:'待审批',val:'0,2'},{title:'已审批',val:'3,4,5',addclass:'btn-remove'}],
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


