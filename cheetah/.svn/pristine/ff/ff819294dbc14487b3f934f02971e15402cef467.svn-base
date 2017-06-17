fml.define('fms/reimburse/traffic/form', ['jquery', 'component/notify', 'fms/reimburse/modules/Base','fms/reimburse/modules/BasicInfo','fms/reimburse/modules/ReceiptInfo','fms/reimburse/modules/TransportFee','fms/reimburse/modules/MealsFee'], function (require, exports) {
    var $ = require('jquery'), notify = require('component/notify');;

    var BaseModule = require('fms/reimburse/modules/Base');


    BaseModule.bindForm([
        'BasicInfo',
        'ReceiptInfo',
        'TransportFee',
        'MealsFee'
    ], function (name) {
        switch (name) {
            case 'BasicInfo':
            case 'ReceiptInfo':
                return 'expenseTrafficDining.';
            case 'TransportFee':
                return 'expenseTrafficDetails';
            case 'MealsFee':
                return 'expenseDiningDetails';
            default:
                return '';
        }
    });

});