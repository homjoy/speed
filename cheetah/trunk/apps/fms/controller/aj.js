/*
 ajax请求转发
 */
function aj() {
    return this;
}
var controlFns = {
    user: function (params) {
        // 个人中心接口
        var php = {
            'login': 'fms::/login',
            'search': 'fms::/userinfo/queryUserInfo',
            'get': 'fms::/userinfo/getUserInfo', //获取
            'switch': 'fms::/switchuser'
        };
        this.ajaxTo(php[params]);
    }
    , contract: function (params) {
        // 合同申请单
        var php = {
            //新建草稿
            'drafts': 'fms::/contractapplication/saveContractApplicationFirstDraftJson'
            //提交
            , 'submit': 'fms::/contractapplication/saveContractApplicationCommitJson'
            //保存草稿
            , 'save_drafts': 'fms::/contractapplication/saveContractApplicationDraftNotFirstJson'
            //保存修改
            , 'save_edit': 'fms::/contractapplication/saveContractApplicationCommitOthersJson',
            //查询主合同相关信息
            'search_contract': 'fms::/contract/passContractInforByCid'
            //tokeninput 查询主合同编号
            , 'search_primary_contract': 'fms::/contract/queryAllContractByCContractCode'
            //查询合同编码对应的签订公司
            , 'mach_sign_company': 'fms::/dictionary/getCompanyNameJson'
            //删除合同
            , 'delete': 'fms::/userbillrequest/deleteBillRequestDraft'

            //合同详情
            , 'detail': 'fms::/userbillrequest/showContractApplicationInformationJson'
            //合同催审
            , 'hasten': 'fms::/contract/remindapprovebatch'
            //合同撤销
            , 'revoke': 'fms::/contract/revokecontractbill'

            //合同归档
            , 'filing': 'fms::/contract/contractFiling'
            //修改已归档合同数据
            , 'update_filing': 'fms::/contract/updateContract'
            //驳回合同归档
            , 'reject_filing': 'fms::/contract/rejectFiling'

            //合同审批
            , 'approval': 'fms::/contract/complete'
            //合同批量审批
            , 'approval_all': 'fms::/contract/batchcomplete'

            //发送通知领取邮件.
            , 'mail': 'fms::/contract/notifyGetContractEmail'
        };
        this.ajaxTo(php[params]);
    }
    , pay: function (params) {
        //付款相关接口
        var php = {
            //保存草稿
            'drafts': 'fms::/contractpayrequest/saveContractPayRequestAsDraft'
            //提交
            , 'submit': 'fms::/contractpayrequest/saveContractPayRequestBillCommit'
            //查询合同信息
            , 'search_contract': 'fms::/contractpayrequest/showContractInfoJson'

            //付款催审
            , 'hasten': 'fms::/contractpayrequest/remindapprove'
            //付款撤销申请
            , 'revoke': 'fms::/contractpayrequest/revokeContractPay'
            //付款详情
            , 'detail': 'fms::/contractpayrequest/showContractPayAllJson'

            //确认付款
            , 'confirm': 'fms::/contractpayrequest/confirmContractPay'
            //付款驳回
            , 'reject': 'fms::/contractpayrequest/rejectContractPay'

            //批量审批
            , 'approval': 'fms::/contractpayrequest/batchcomplete'

            //催单
            , 'prompt': 'fms::/contractpayrequest/promptbill'
        };
        this.ajaxTo(php[params]);
    }
    , budget: function (params) {
        //预算相关接口
        var php = {
            //保存草稿
            'save_draft': 'fms::/budgetrequest/savebudgetapplicationasdraft',
            //提交申请
            'submit': 'fms::/budgetrequest/savebudgetapplicationcommit',

            //申请单详情.
            'detail': 'fms::/budgetrequest/showbudgetapplicationalljson',

            //撤销申请
            'revoke': 'fms::/budgetrequest/revokeapprove',

            //催审
            'hasten': 'fms::/budgetrequest/remindapprove',

            //审批 /批量
            'approval': 'fms::/budgetrequest/batchcomplete',

            //查询历史数据.
            'query_history_budget': "fms::/budgetrequest/querybudgetrequesthistoryjson",

            //预算项目选择
            'search_depart_budget': 'fms::/budgetrequest/showBudgetitemByDepartJson',

            //history
            'history': 'fms::/budgetrequest/querybudgetamountbeforejson',

            //修改/保存预算调整草稿
            'save_adjust_draft': 'fms::/budgetadjust/savebudgetadjustasdraft',

            //预算调整提交.
            'submit_adjust': 'fms::/budgetadjust/savebudgetadjustcommit',

            //查询调整额
            'query_balance': 'fms::/budgetadjust/querybudgetbalancejson',

            //查询项目的调整前数据
            'query_budget_before': "fms::/budgetadjust/querybudgetrequesthistoryjson",

            //预算项目列表
            'projects': "fms::/budgetrequest/showbudgetitembydepartjson",

            //报表项目.
            'report_projects':"fms::/budgetrequest/showreportitemjson"
        };
        this.ajaxTo(php[params]);
    }
    , query: function (params) {
        // 查询界面
        var php = {
            'depart': 'fms::/departmentbase/getDepartmentBaseByUserJson',//查询部门
            'search_contract': 'fms::/contract/queryContractInformationJson', //异步查询合同
            'search_pay': 'fms::/contractpayrequest/queryContractPayListJson'//查询付款单据

            //查询预算单据
            , 'search_budget': 'fms::/budgetrequest/querybudgetbilllistjson'

            , 'search_reimburse': 'fms::/expensecommon/queryexpensebilllistjson'

            , 'search_loan': 'fms::/loanrequest/queryloanbilllistjson'

            , 'search_IDCinfo': 'fms::/serverroom/queryserverroomlistjson'
            //test-导出
            ,'exports_IDCinfo': 'fms::/serverroom/exportserverroomlistjson'
        };
        this.ajaxTo(php[params]);
    }
    , "my_approval": function (params) {
        var php = {
            //所有待审批数量
            'getmyapprovegroupcount':'fms::/get_myapprovegroupcount',
            //合同列表
            'contract_list': 'fms::/contract/tasklistJson',
            'pay_list': 'fms::/contractpayrequest/tasklistJson',
            'budget_list': 'fms::/budgetrequest/querybudgetmyapprovejson'
            //报销列表
            , 'reimburse': 'fms::/expensecommon/queryexpensemyapprovejson'
            //借款
            , 'loan': 'fms::/loanrequest/queryloanmyapprovejson'
            //IDCinfo机房列表
            ,'IDCinfo_list':'fms::/serverroom/queryserverroommyapprovejson'

        };
        this.ajaxTo(php[params]);
    }
    , "my_apply": function (params) {
        var php = {
            //查询合同申请列表
            'search_contracts': 'fms::/contractapplication/queryContractApplicationRequestJson'
            //查询付款申请列表
            , 'search_pays': 'fms::/contractpayrequest/showContractPayRequestJson'

            , 'search_budgets': 'fms::/budgetrequest/querybudgetrequestjson'
            //报销列表
            , 'reimburse': 'fms::/expensecommon/queryexpenserequestjson'
            //借款
            , 'loan': 'fms::/loanrequest/queryloanrequestjson'
            //IDC机房列表
            ,'search_IDCinfos':'fms::/serverroom/queryserverroomrequestjson'

        };

        this.ajaxTo(php[params]);
    }
    , "my_manage": function (params) {
        var php = {
            //合同归档/待归档数据列表
            'contract_archives': 'fms::/contractapplication/showSuccAndFileContractJson'
            //付款列表
            , 'pay_list': 'fms::/contractpayrequest/showContractPaySuccessJson'
            //预算列表
            , 'budgets': 'fms::/budgetrequest/querycontractpaysuccessjson'
            //报销列表
            , 'reimburse': 'fms::/expensecommon/queryexpensesuccessjson'
            //借款
            , 'loan': 'fms::/loanrequest/queryloansuccessjson'

        };
        this.ajaxTo(php[params]);
    },

    "report": function (params) {
        var php = {
            //预算项目季度汇总报表数据及报表格式
            'budget_quarter_summary': 'fms::/report/showbudgetqsumbalancejson'
            //部门预算汇总报表数据
            , 'budget_department_summary': 'fms::/report/showdepartqsumbalancejson'
            //报表项目汇总报表数据
            , 'budget_project_summary': 'fms::/report/showreportqsumbalancejson'


            //报销报表查询
            , 'reimburse_summary': 'fms::/expensereport/showexpensereportasjson'

            , 'loan_summary': 'fms::/loanrequest/queryloanagingjson'
        };
        this.ajaxTo(php[params]);
    },


    "reimburse": function (params) {
        var php = {
            //查询用户当前欠款
            'get_user_debt':"fms::/expensecommon/getdebtamountjson",
            //通用费用报销保存草稿
            'general_save_draft': "fms::/expensecommon/saveexpensecommonasdraft",
            //通用费用报销提交
            'general_submit': "fms::/expensecommon/saveexpensecommonascommit",

            //保存/修改差旅报销申请单草稿
            'travel_save_draft':'fms::/expensectrip/saveexpensectripasdraft',
            //提交差旅报销申请单
            'travel_submit':'fms::/expensectrip/saveexpensectripascommit',

            //交通餐费报销保存草稿
            'traffic_save_draft':'fms::/expensetraffic/saveexpensetrafficasdraft',
            //提交交通餐费报销申请单
            'traffic_submit':'fms::/expensetraffic/saveexpensetrafficascommit',

            //借款单保存草稿
            'loan_save_draft':'fms::/loanrequest/temporarysaveapply',
            //借款单提交申请单
            'loan_submit':'fms::/loanrequest/submitapply',

            //查看详情.
            'detail':'fms::/expensecommon/showexpensealljson',

            //提交申请
            'submit': 'fms::/expensecommon/ saveexpensecommonascommit',

            //查询所属项目
            'belong_to_projects': 'fms::/dictionary/getprojectjson',

            //预算项目
            'budget_projects': 'fms::/dictionary/getexpenseitemjson',

            //归属地
            'addresses': 'fms::/dictionary/getattributionjson',

            //交通工具
            'vehicles': 'fms::/dictionary/getvechiclejson',

            //各种常量值.
            'constant': 'fms::/dictionary/getexpensedictionaryjson',

            //撤销申请
            'revoke': 'fms::/expensecommon/revokeapprove',

            //催审
            'hasten': 'fms::/expensecommon/remindapprove',

            //审批 /批量
            'approval': 'fms::/expensecommon/batchcomplete',

            //查询未报销出差列表
            'get_user_ctrip': 'fms::/expensectrip/getctripnonotexpensejson',
            //查看ctrip 详情.
            'ctrip_detail': 'fms::/expensectrip/getctripbynojson'
            //报销款确认/驳回
            ,'confirmexpense': 'fms::/expensecommon/confirmexpense'
            //转移审批
            ,'complete':'fms::/expensecommon/complete'
            //催票
            ,'prompt':'fms::/expensecommon/promptbill'
            //加签
            ,'claim':'fms::/contract/claim'
        };
        this.ajaxTo(php[params]);
    },
    "loan": function (params) {
        var php = {
            //撤销申请
            'revoke': 'fms::/loanrequest/revokeapprove',
            //催审
            'hasten': 'fms::/loanrequest/remindapprove',
            //查看详情.
            'detail':'fms::/loanrequest/showloanrequestdetailinfo',
            //审批 /批量
            'approval':'fms::/contract/batchcomplete'
            //增加归属地
            ,'addattribution':'fms::/loanrequest/addattribution'
            //管理付款确认
            ,'loanrequestpayconfirm':'fms::/loanrequest/loanrequestpayconfirm'
            //冲账
            ,'repayloanrequest':'fms::/loanrequest/repayloanrequest'
            //借款单出纳驳回
            ,'loanrequestreject':'fms::/loanrequest/loanrequestreject'

            ,'hasten_repayment' : 'fms::/loanrequest/remindreturn'

        };
        this.ajaxTo(php[params]);
    },
    "IDCinfo":function(params){
        var php = {
            //地域关联机房
            'areaRoomInfo':'fms::/dictionary/getdictionarybytypejson',
            //合同编号信息
            'contractNoInfo':'fms::/serverroom/queryallcontractnojson',
            //合同关联信息
            'contractInfo':'fms::/serverroom/showContractInfoJson',
            //提交申请
            'submit':'fms::/serverroom/saveserverroomascommit',
            //保存草稿
            'save_draft':'fms::/serverroom/saveserverroomasdraft',
            //获取单据详细信息
            'detail':'fms::/serverroom/showserverroomalljson',
            //审批／批量
            'approval':'fms::/serverroom/complete',
            //撤销
            'revoke':'fms::/serverroom/revoke',
            //作废
            'cancel':'fms::/serverroom/cancel'
        };
        this.ajaxTo(php[params]);
    }
};
exports.__create = controller.__create(aj, controlFns);
