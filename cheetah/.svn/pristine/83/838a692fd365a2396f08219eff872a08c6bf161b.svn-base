fml.define('fms/home/index', ['jquery','plugin/cookie','plugin/store','component/notify'], function (require, exports) {
    var $ = require('jquery');
    var Cookies = require('plugin/cookie');
    var store = require('plugin/store');

    /*
     遮罩层
     */
    var everWrap = Cookies.get('wrap') || store.get('pass-home-index-wizard');
    if (!everWrap) {
        $('.director-wrap').removeClass('wrap-hide');
        var isThin = $('.effect').hasClass('mainnav-sm');
        if (isThin) {
            $('.effect').removeClass('mainnav-sm').addClass('mainnav-lg');
        }
        $('.guanbi').on('click', function () {
            store.set('pass-home-index-wizard',true);
            $('.director-wrap').remove();
        });
    }else{
        Cookies.remove('wrap');
        store.set('pass-home-index-wizard',true);
    }
});