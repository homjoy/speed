fml.define('speed/home/wizard', ['jquery', 'plugin/store'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var Cookies = require('plugin/cookie');
    var store =require('plugin/store');

    Cookies.remove('passWizard');
    Cookies.remove('passCheckInWizard');

    ////无需进行引导
    //store.remove('wizard-1.4-bus');
    if (store.get('wizard-1.4-bus')) {
        return;
    }

    //var wizardHTML = '<div class="wizard-container">' +
    //    '<div class="wizard wizard-check-in"></div>' +
    //    '<a href="javascript:void(0);" class="wizard wizard-close-check-in"></a>' +
    //    '</div>';
    //控制布局
    alert('234');
    $('.mainnav-toggle').addClass('thin');
    $('.mainnav-lg').addClass('mainnav-sm').removeClass('mainnav-lg');
    var wizardHTML = '<div class="wizard-container">' +
        '<div class="wizard new-home"></div>' +
        '<a href="javascript:void(0);" class="wizard wizard-close-check-in"></a>' +
        '</div>';
    var wizard = $(wizardHTML);
    var wizardHight =$('#container').height()+140;
    function relocateTips() {
        wizard.css({
            height: wizardHight
        });
    }

    wizard.appendTo("body");
    wizard.find('.wizard-close-check-in').on('click', function (e) {
        e.preventDefault();

        wizard.fadeOut(function () {
            $(this).remove();
        });
        store.set('wizard-1.4-bus','1');
        return false;
    });

    relocateTips();
    $(window).on('resize', relocateTips);
});