fml.define('speed/time/wizard', ['jquery', 'plugin/store'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var store = require('plugin/store');
    //
    //Cookies.remove('passMyTimeWizard');

    ////无需进行引导
    if (store.get('passMyTimeWizard')) {
        return;
    }

    var wizardHTML = '<div class="wizard-container">' +
        '<div class="wizard wizard-time-my"></div>' +
        '</div>';

    var wizard = $(wizardHTML);

    function relocateTips() {
        wizard.css({
            height: $(document).height()
        });
        wizard.find('.wizard-time-my').css({
            top: $('.switch-group').offset().top - 50
        });
    }

    wizard.appendTo("body");
    wizard.on('click', function (e) {
        e.preventDefault();

        wizard.fadeOut(function () {
            $(this).remove();
        });
        store.set('passMyTimeWizard', 1);
        return false;
    });

    $(function () {
        var count = 10;

        function update() {
            relocateTips();
            if (count-- > 0) {
                setTimeout(update, 300);
            }
        }

        setTimeout(update, 300);
        $(window).on('resize', relocateTips);
    });
});