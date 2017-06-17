fml.define('fms/query/pay', ['jquery', 'plugin/bootstrap/datepicker', 'component/notify', 'component/TreeSelect','fms/common/utils','fms/common/SearchTable'], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
    var utils = require('fms/common/utils');
    var SearchTable = require('fms/common/SearchTable');


    /**
     * 加载部门树.
     */
    $.post('/aj/query/depart', function (data) {
        $("input[name=departmentId]").TreeSelect({
            multiple: true,
            data: data
        });
    }, 'json');
    /**
     * 日期控件.
     */
    $("#sTime,#eTime").datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true,
        'todayHighlight': true
    });

    /**
     * 数据导出.
     */
    $('.btn-export').on('click', function () {
        var queryParams = $('form').serialize();
        $(this).attr('href', '/export/pay?' + queryParams);
    });


    var table = new SearchTable({
        container: ".table-container",
        searchForm: ".query-form",
        ajax: {
            url: '/aj/query/search_pay'
        },
        headers: [
            '单据编号',
            '合同编号',
            '合同名称',
            '签订日期',
            '预算项目ID',
            '合同付款公司',
            '合同收款公司',
            '申请时间',
            '申请人姓名',
            '部门名称',
            '付款金额(￥)',
            '收款方式',
            '收款银行',
            '收款支行',
            '银行账号',
            '付款对应服务周期',
            '预计付款时间',
            '收款单位代码',
            '审批状态'
        ],
        columns: [
            'cpbPayRequestCode',
            'cpbContractCode',
            'cpbContractName',
            'cpbContractSignTime',
            'cpbBugetItemId',
            'cpbContractCompany',
            'cpbCustomerCompany',
            'cpbApplyTime',
            'cpbPersonName',
            'cpbDepartmentName',
            'cpbMoney',
            'cpbCollectType',
            'cpbBank',
            'cpbBranchBank',
            'cpbAccountNumber',
            'cpbServerTerm',
            'cpbPayTime',
            'cpbGroupSimplename',
            'cpbApprovalState'
        ],
        columnFormatter: function (column, value, row) {
            if(column == 'cpbMoney'){
                return utils.formatCurrency(value);
            }
            return value;
        },
        rowFormatter: function (columns, obj) {
            var link = '/pay/view/?cpbId=' + (obj.cpbId || 0);
            return '<tr dbl-open="'+link+'">' + columns + '</tr>';
        }
    });
});