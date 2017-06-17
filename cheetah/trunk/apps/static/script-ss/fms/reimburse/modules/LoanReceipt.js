fml.define('fms/reimburse/modules/LoanReceipt', ['jquery', 'plugin/artTemplate','plugin/moment', 'component/TreeSelect', 'fms/reimburse/modules/Base','fms/common/utils','component/notify'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), Template = require('plugin/artTemplate'), moment = require('plugin/moment'),utils = require('fms/common/utils'),notify = require('component/notify');

    var BaseModule = require('fms/reimburse/modules/Base');
    var $form = $(".form-apply");

    /**
     * @param options
     * @constructor
     */
    function Module(options) {
        options = options || {};
        this.templateName = 'loan-receipt-tpl';
        this.detail = options.values || {};
        this.options = options;
        this.$block = null;
        this.templateData = this.formatData();

        this.render();
        this.bindEvent();
    }


    Module.prototype = {
        render: function () {
            var templateData = this.templateData;

            templateData.listAttach = this.detail.listAttach || [];
            templateData.constant = this.detail.constant;
            templateData.expectedRepayDate = templateData.expectedRepayDate || moment().add(5,'day').format('YYYY-MM-DD');

            $form.find('[data-module=LoanReceipt]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.loan-receipt');

        },
        formatData: function () {
            //通用费用报销.
            if (this.detail['LoanRequestBill']){
                return this.detail['LoanRequestBill'];
            }

            return {};
        },
        bindEvent: function () {
            var that = this;


            $form.find('.basic-info .form-group:first .control-label').text('借款人');

            this.$block.find('[name=budgetItemCode]').TreeSelect({
                valueField: 'fullCode', //值为该字段的数据.
                data: this.detail.constant.budgetProjects
            }).on("change", function (event, node) {
                that.$block.find('[name=budgetItemCode]').val(node.fullCode);
            });


            BaseModule.bindDatePicker(this.$block.find('.datepicker'),'today');

            BaseModule.bindUpload(this.$block.find('.upload-btn'), {
                uploadInput: this.$block.find('[name=filekeys]'),
                deleteInput: this.$block.find('[name=deleteattachkeys]')
            });

            //借款金额不能超过5万
            this.$block.find('[name=loanMoneyNum]').blur(function(){
                var loanMoney = utils.getCurrencyValue($(this).val());
                var parentDiv = $(this).closest('.form-group');
                if(loanMoney > 50000){
                     notify.error('借款金额不能超过五万,请您重新填写!');

                     $(this).val(utils.formatCurrency(0));
                     parentDiv.addClass('has-error');
                }else parentDiv.removeClass('has-error');
            });

        },
        validate: function () {

            var paramArray =[[/*'budgetItemCode',*/'loanMoneyNum','expectedRepayDate'],'singleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || '';
            var that = this;
            var fields = ['budgetItemCode','forProject','loanMoneyNum','expectedRepayDate','loanReason','filekeys','deleteattachkeys'];

            return $.map(fields, function (name) {
                var input = that.$block.find('[name=' + name + ']'), value = input.val();
                return {name: prefix + name, value: value};
            });
        }
    };

    return Module;
});
