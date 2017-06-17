fml.define("fms/my/approval/common",['jquery'],function(require,exports){

    var path = window.location.pathname;
    path = $.inArray(path,['/','/home']) > 0 ? '/' : path;
    var currentUrl = window.location.pathname + window.location.search;
    console.log(currentUrl);
    //console.warn(window.location.pathname,'111',window.location.search);
    /**
     * 处理菜单高亮.
     */

    $.getJSON('/aj/my_approval/getmyapprovegroupcount',{},function(ret){
        var data = {};
        if(ret.rcode==200){
            data = ret.data;
        }
        $(".slope").find('a').each(function(){
            var href = $(this).attr('href') || '';
            var ahref=href.split('/');
            var item = ahref[3];
            if(!!data[item]){
                $(this).find('span').html(data[item]);
            }
        });
    });
    $(".slope").find('a').each(function(){
        var href = $(this).attr('href') || '';
        //href = $.inArray(href,['/','/home']) > 0 ? '/' : href;
        var match = false;
        //var level = href.replace(/^\//,'').split('/').length;
        if(href == '/' && path == href){
            match = true;
        }else if(href != '/' && path.indexOf(href) >= 0){
            match = true;
        }else if(href === currentUrl){
            match = true;
        }
        if(match){
            $(this).parent().addClass('active');
            //$(this).parents("ul.collapse").addClass('in');
        }
    });
});