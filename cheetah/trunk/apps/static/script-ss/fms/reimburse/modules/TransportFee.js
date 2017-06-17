fml.define('fms/reimburse/modules/TransportFee', ['jquery', 'plugin/artTemplate', 'component/TreeSelect','plugin/moment','fms/reimburse/modules/Base'], function (require, exports) {
    "use strict";
    var BaseModule = require('fms/reimburse/modules/Base');
    var $ = require('jquery'), Template = require('plugin/artTemplate'), moment = require('plugin/moment');
    var $form = $(".form-apply");

    /**
     * @param options
     * @constructor
     */
    function Module(options) {
        options = options || {};
        this.templateName = 'transport-fee-tpl';
        this.detail = options.values || {};
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
            templateData.summaryCount = this.detail['ExpenseCommon'] ?
                this.detail['ExpenseCommon'].SumCtripTraffic | this.detail['ExpenseCommon'].SumTrafficDetail : 0;

            $form.find('[data-module=TransportFee]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.transport-fee');
        },
        formatData: function () {
            //通用费用报销.
            if (this.detail['ExpenseCommon'] && this.detail['ExpenseCommon'].MlsExpenseTrafficDetail) {
                return this.detail['ExpenseCommon'].MlsExpenseTrafficDetail;
            }

            return [];
        },
        bindEvent: function () {
            var that = this;
            var id = this.$block.siblings('.basic-info').find('[name="id"]').val();
            BaseModule.bindDatePicker(this.$block.find('[name=billDate]'));
            BaseModule.bindBlockWantedSummary.call(this);

            /**
             * 增加删除
             */
            var paramsArray = ['transport-fee-row','bindDatePicker','delTrafficItem','ctripDayChange'];
            BaseModule.bindItemAction.apply(this,paramsArray);

            //出差日期发生变化时同步出差天数
            BaseModule.bindComputeCtripDay.apply(that);
        },
        validate: function () {

            var paramArray = [['billDate', 'departure', 'destination', 'tools', 'billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || 'expenseTrafficDetails';
            var fields = ['id', 'billheadId', 'billDate', 'departure', 'destination', 'tools', 'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delTrafficItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);
        }
    };

    return Module;
});