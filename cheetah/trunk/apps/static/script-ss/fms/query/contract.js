fml.define('fms/query/contract', ['jquery', 'plugin/bootstrap/datepicker', 'component/notify', 'component/TreeSelect', 'fms/common/SearchTable','plugin/tokeninput'], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
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
     * 加载人员信息
     */
    $('input[name=handleId]').tokenInput("/aj/user/search", {
        tokenLimit: 1,
        onAdd: function (item) {
            $.ajax('/aj/user/get', {
                type: 'POST',
                data: {userId: item.id},
                dataType: 'json',
                success: function (resp) {
                    if (resp.code != 200) {
                        return notify.error('')
                    }
                    $('input[name=handleId]').val(item.id);
                }
            });
        },
        tokenFormatter:function(item){
            return "<li><p>" + item['name_cn'] + "</p></li>";
        },
    });

    /**
     * 数据导出.
     */
    $('.btn-export').on('click', function () {
        var queryParams = $('form').serialize();
        $(this).attr('href', '/export/file?' + queryParams);
    });


    var table = new SearchTable({
        container: ".table-container",
        searchForm: ".query-form",
        ajax: {
            url: '/aj/query/search_contract'
        },
        headers: [
            '单据编号',
            '合同编号',
            '合同类型',
            '合同名称',
            '签订日期',
            '合同金额(￥)',
            '合同期限',
            '签订合同公司',
            '对方名称',
            '对方联系人',
            '联系电话',
            '经办部门',
            '经办人',
            '单据状态',
            '文件状态'
        ],
        columns: [
            'contractApplicationCode',
            'contractCode',
            'contractTypeName',
            'contractName',
            'signDate',
            'amount',
            'contractTerm',
            'contractCompany',
            'customerCompany',
            'contractPerson',
            'telephone',
            'departmentName',
            'handingPersonName',
            'approveState',
            'fileDetail'
        ],
        rowFormatter: function (columns, obj) {
            var id = obj['contractApplicationId'] || 0;
            return '<tr dbl-open="/contract/view/?type=CA&id=' + id + '">' + columns + '</tr>';
        }
    });
});