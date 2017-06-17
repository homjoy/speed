fml.define('fms/reimburse/loan/form', ['jquery', 'component/notify', 'fms/reimburse/modules/Base', 'fms/reimburse/modules/BasicInfo', 'fms/reimburse/modules/LoanReceipt'], function (require, exports) {

    var $ = require('jquery'), notify = require('component/notify');

    var BaseModule = require('fms/reimburse/modules/Base');


    BaseModule.bindForm([
        'BasicInfo',
        'LoanReceipt'
    ], function (name) {
        switch (name) {
            case 'BasicInfo':
                return 'mlsLoanRequestBill.';

            case 'LoanReceipt':
                return 'mlsLoanRequestBill.';
            default:
                return '';
        }
    });
});