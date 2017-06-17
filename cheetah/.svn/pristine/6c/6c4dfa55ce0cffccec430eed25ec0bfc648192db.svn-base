fml.define('fms/reimburse/modules/MealsFee', ['jquery','plugin/artTemplate','plugin/bootstrap/datepicker', 'plugin/moment','fms/reimburse/modules/Base'], function (require, exports) {
    "use strict";
    var BaseModule = require('fms/reimburse/modules/Base');
    var $ = require('jquery'),Template = require('plugin/artTemplate'),moment= require('plugin/moment');
    var $form = $(".form-apply");
    /**
     * @param options
     * @constructor
     */
    function Module(options) {
        options = options || {};
        this.templateName = 'meals-fee-tpl';
        this.detail = options.values || {};
        this.detail.constant = window.Constant;
        this.options = options;
        this.$block = null;
        this.templateData = this.formatData();

        this.render();
        this.bindEvent();
    }



    Module.prototype = {
        render: function () {
            var templateData = {};
            templateData.details = this.templateData;
            templateData.constant = this.detail.constant;

            templateData.summaryCount = this.detail['ExpenseCommon'] ? this.detail['ExpenseCommon'].SumDiningDetail : 0;

            $form.find('[data-module=MealsFee]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.meals-fee');
        },
        formatData:function(){
            //交通费用报销.
            if (this.detail['ExpenseCommon'] && this.detail['ExpenseCommon'].MlsExpenseDiningDetail) {
                return this.detail['ExpenseCommon'].MlsExpenseDiningDetail;
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
        bindEvent:function(){
            //各种事件处理
            var that = this;
            var id = this.$block.siblings('.basic-info').find('[name="id"]').val();
            BaseModule.bindDatePicker(this.$block.find('[name=billDate]'));
            BaseModule.bindBlockWantedSummary.call(this);

            var paramsArray = ['meals-fee-row','bindDatePicker','delDiningItem'];
            BaseModule.bindItemAction.apply(this,paramsArray);

        },
        validate:function(){
        //验证有效性
            var paramArray = [['billDate','billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || 'expenseDiningDetails';
            var fields = ['id', 'billDate',  'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delDiningItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);

        },
    };

    return Module;
});