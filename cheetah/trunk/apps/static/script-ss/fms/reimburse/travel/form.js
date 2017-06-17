fml.define('fms/reimburse/travel/form', ['jquery', 'component/notify', 'fms/reimburse/modules/Base', 'fms/reimburse/modules/BasicInfo', 'fms/reimburse/modules/ReceiptInfo', 'fms/reimburse/modules/BusinessTrip', 'fms/reimburse/modules/TransportFee', 'fms/reimburse/modules/HotelFee', 'fms/reimburse/modules/AdditionalFee', 'fms/reimburse/modules/BusinessFee', 'fms/reimburse/modules/OtherFee'], function (require, exports) {
    var $ = require('jquery'),
        notify = require('component/notify');
    var BaseModule = require('fms/reimburse/modules/Base');

    BaseModule.bindForm([
        'BasicInfo',
        'ReceiptInfo',
        'BusinessTrip',
        'TransportFee',
        'HotelFee',
        'AdditionalFee',
        'BusinessFee',
        'OtherFee'
    ], function (name) {
        switch (name) {
            case 'BasicInfo':
            case 'ReceiptInfo':
                return 'expenseCtrip.';

            case 'BusinessTrip':
                return 'expenseCtrip.';

            case 'TransportFee':
                return 'expenseCtripTraffics';

            case 'HotelFee':
                return 'expenseCtripHotels';

            case 'AdditionalFee':
                return 'expenseCtripAllowances';

            case 'BusinessFee':
                return 'expenseCtripEntertains';

            case 'OtherFee':
                return 'expenseCtripOthers';
            default:
                return '';
        }
    });

});