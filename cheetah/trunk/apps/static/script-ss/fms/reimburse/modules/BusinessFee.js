fml.define('fms/reimburse/modules/BusinessFee', ['jquery', 'plugin/artTemplate', 'plugin/moment','fms/reimburse/modules/Base'], function (require, exports) {
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
            templateData.summaryCount = this.detail['ExpenseCommon'] ? this.detail['ExpenseCommon'].SumCtripEntertain : 0;
            $form.find('[data-module=BusinessFee]').replaceWith(Template('business-fee-tpl', templateData));

            this.$block = $form.find('.business-fee');
        },
        formatData: function () {
            //通用费用报销.
            if (this.detail['ExpenseCommon']
                && this.detail['ExpenseCommon'].MlsExpenseCtripEntertain) {
                return this.detail['ExpenseCommon'].MlsExpenseCtripEntertain;
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

            var paramsArray = ['business-fee-row','bindDatePicker','delEntertainItem'];
            BaseModule.bindItemAction.apply(this,paramsArray);

        },
        validate: function () {

            var paramArray =[['billDate', 'billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || '';
            var fields = ['id', 'billheadId', 'billDate', 'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delEntertainItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);
        }
    };

    return Module;
});