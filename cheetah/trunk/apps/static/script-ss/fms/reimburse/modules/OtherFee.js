fml.define('fms/reimburse/modules/OtherFee', ['jquery', 'plugin/artTemplate', 'plugin/moment', 'fms/reimburse/modules/Base'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), Template = require('plugin/artTemplate'), moment = require('plugin/moment');
    var BaseModule = require('fms/reimburse/modules/Base');
    var $form = $(".form-apply");

    /**
     * @param options
     * @constructor
     */
    function Module(options) {
        options = options || {};
        this.templateName = 'other-fee-tpl';
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
    Module.prototype = {
        render: function () {
            var templateData = {};
            templateData.details = this.templateData;
            templateData.constant = this.detail.constant;
            templateData.summaryCount = this.detail['ExpenseCommon'] ? this.detail['ExpenseCommon'].SumCtripOther : 0;


            $form.find('[data-module=OtherFee]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.other-fee');
        },
        formatData: function () {
            //通用费用报销.
            if (this.detail['ExpenseCommon']
                && this.detail['ExpenseCommon'].MlsExpenseCtripOther) {
                return this.detail['ExpenseCommon'].MlsExpenseCtripOther;
            }

            return [];
        },
        bindDatePicker: function (input) {
            //各种事件处理
            input.datepicker({
                format: "yyyy-mm-dd",
                startDate: moment().subtract(2, 'month').format('YYYY-MM-DD'),
                language: "zh-CN",
                autoclose: true
            });
        },
        bindEvent: function () {
            //各种事件处理
            var that = this;

            that.bindDatePicker(this.$block.find('[name=billDate]'));
            BaseModule.bindBlockWantedSummary.call(this);

            var paramsArray = ['other-fee-row','bindDatePicker','delOtherItem'];
            BaseModule.bindItemAction.apply(this,paramsArray);

        },
        validate: function () {

            var paramArray =[['billDate', 'expenseItem', 'billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || '';
            var fields = ['id', 'billheadId', 'billDate', 'expenseItem', 'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delOtherItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);
        }
    };

    return Module;
});