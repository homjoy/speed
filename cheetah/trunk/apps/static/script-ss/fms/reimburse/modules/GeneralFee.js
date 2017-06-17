fml.define('fms/reimburse/modules/GeneralFee', ['jquery', 'plugin/artTemplate', 'plugin/moment', 'fms/reimburse/modules/Base'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), Template = require('plugin/artTemplate'), moment = require('plugin/moment');

    var BaseModule = require('fms/reimburse/modules/Base');
    var $form = $(".form-apply");

    /**
     * @param options
     * @constructor
     */
    function GeneralFee(options) {
        options = options || {};
        this.templateName = 'general-fee-tpl';
        this.detail = options.values || {};
        this.options = options;
        this.$block = null;
        this.templateData = this.formatData();

        this.render();
        this.bindEvent();
    }

    /**
     *
     */
    GeneralFee.prototype = {
        render: function () {
            var templateData = {};
            templateData.details = this.templateData;
            templateData.constant = this.detail.constant;
            templateData.summaryCount = this.detail['ExpenseCommon'] ? this.detail['ExpenseCommon'].SumCommonDetail : 0;

            $form.find('[data-module=GeneralFee]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.general-fee');
        },
        formatData: function () {
            //通用费用报销.
            if (this.detail['ExpenseCommon']) {
                return this.detail['ExpenseCommon'].MlsExpenseCommonDetail;
            }

            return [];
        },
        bindEvent: function () {
            //各种事件处理
            var that = this;

            BaseModule.bindDatePicker(this.$block.find('[name=billDate]'));
            BaseModule.bindBlockWantedSummary.call(this);

            var paramsArray = ['general-fee-row','bindDatePicker','delExpenseItem'];
            BaseModule.bindItemAction.apply(this,paramsArray);
        },
        validate: function () {

            var paramArray =[['billDate', 'expenseItem', 'billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || 'expenseCommonDetails';
            var fields = ['id', 'billheadId', 'billDate', 'expenseItem', 'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delExpenseItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);
        }
    };

    return GeneralFee;
});