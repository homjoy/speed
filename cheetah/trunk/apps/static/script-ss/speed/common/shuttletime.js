fml.define('speed/common/shuttletime',['jquery'],function(requires,exports){
    "use strict"
    var $ = requires('jquery');

    /**
     * 切换班车时刻表
     */
    $(".line-toggle li").on("click",function(){
        var self = $(this);
        var target = $(this).attr('data-target');
        var nav = self.closest('.line-toggle');
        nav.find('.active').removeClass('active');
        self.addClass('active');
        nav.next().find('.active').removeClass('active');
        $('#'+target).addClass('active');
    });
});
