fml.define('speed/user/wizard',['jquery','plugin/store'],function(require,exports){
    "use strict";
    var $ = require('jquery');
    var store = require('plugin/store');
    var USER_INDEX_WIZARD_KEY = 'pass-user-index-wizard';
    ////无需进行引导
    if(store.get(USER_INDEX_WIZARD_KEY)){
        return ;
    }

    var userleadHTML = '<div class="user-wizard">' +
        '<div class="wizard wizard-nav"></div>'+
        '<div class="wizard wizard-often"></div>'+
        '<div class="wizard wizard-schedule"></div>'+
        '<div class="wizard wizard-user"></div>'+
        '<a href="javascript:void(0);" class="wizard wizard-close"></a>'+
        '</div>';

    var wizard = $(userleadHTML);

    function relocateTips()
    {
        wizard.css({
            height:$('#container').height()
        });

        var width = $(window).width(),
            height = $(window).height();
        wizard.find('.wizard-close').css({
            top:height - 138,
            left:width - 300
        });
    }
    wizard.appendTo("body");

    wizard.find('.wizard-close').on('click',function(e){
        e.preventDefault();

        wizard.fadeOut(function(){
            $(this).remove();
        });
        $('body').css('overflow','auto');
        store.set(USER_INDEX_WIZARD_KEY,1);
        return false;
    });
    relocateTips();

    $(window).on('resize',function(e){
        relocateTips();
    });

    $('body').css('overflow','hidden');
});