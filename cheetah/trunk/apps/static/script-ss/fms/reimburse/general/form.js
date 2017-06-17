fml.define('fms/reimburse/general/form', ['jquery', 'component/notify', 'fms/reimburse/modules/Base', 'fms/reimburse/modules/BasicInfo', 'fms/reimburse/modules/ReceiptInfo', 'fms/reimburse/modules/GeneralFee'], function (require, exports) {
    var $ = require('jquery'), notify = require('component/notify');

    var BaseModule = require('fms/reimburse/modules/Base');


    BaseModule.bindForm([
        'BasicInfo',
        'ReceiptInfo',
        'GeneralFee'
    ], function (name) {
        switch (name) {
            case 'BasicInfo':
            case 'ReceiptInfo':
                return 'expenseCommon.';

            case 'GeneralFee':
                return 'expenseCommonDetails';
            default:
                return '';
        }
    });
});