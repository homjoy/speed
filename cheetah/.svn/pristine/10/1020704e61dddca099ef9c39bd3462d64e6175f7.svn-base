fml.define('fms/reimburse/modules/HotelFee', ['jquery', 'plugin/artTemplate', 'plugin/moment', 'plugin/bootstrap/datepicker', 'fms/reimburse/modules/Base'], function (require, exports) {
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
        this.templateName = 'hotel-fee-tpl';
        this.detail = options.values || {};
        this.detail.constant = window.Constant;
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
            templateData.summaryCount = this.detail['ExpenseCommon'] ? this.detail['ExpenseCommon'].SumCtripHotel : 0;

            $form.find('[data-module=HotelFee]').replaceWith(Template(this.templateName, templateData));
            this.$block = $form.find('.hotel-fee');
        },
        formatData: function () {

            if (this.detail['ExpenseCommon'] && this.detail['ExpenseCommon'].MlsExpenseCtripHotel) {
                return this.detail['ExpenseCommon'].MlsExpenseCtripHotel;
            }

            return [];
        },
        /*bindDateRange: function (input) {
            input.datepicker({
                format: "yyyy-mm-dd",
                startDate: moment().subtract(2, 'month').format('YYYY-MM-DD'),
                language: "zh-CN",
                autoclose: true
            }).on('changeDate', function (e) {
                var input = $(e.target);
                var row = input.closest('.detail-control');

                var start = row.find('[name=startDate]').val(),
                    end = row.find('[name=endDate]').val();

                if (!start || !end) {
                    row.find('.column-days span').text('0天');
                    return;
                }

                var startTime = moment(start);
                var endTime = moment(end);

                var diff = endTime.diff(startTime, 'days');
                row.find('.column-days span').text(diff + '天');
            });
        },*/
        bindEvent: function () {
            var that = this;
            var $block = this.$block;
            //各种事件处理

            BaseModule.bindDateRange(this.$block.find(".input-daterange"));
            BaseModule.bindBlockWantedSummary.call(this);

            var paramsArray = ['hotel-fee-row','bindDateRange','delHotelItem'];
            BaseModule.bindItemAction.apply(this,paramsArray);

        },
        validate: function () {

            var paramArray =[['startDate', 'endDate', 'billAmount','remark'],'mutipleLine'];
            return BaseModule.bindCheckValidateResult.apply(this,paramArray);
        },
        //序列化为数组.
        serializeArray: function (prefix) {
            prefix = prefix || '';
            var fields = ['id', 'billheadId', 'startDate', 'endDate', 'billAmount', 'remark'];

            var paramArray = [prefix,fields,'delHotelItem'];
            return BaseModule.bindSerializeArray.apply(this,paramArray);
        }
    };

    return Module;
});