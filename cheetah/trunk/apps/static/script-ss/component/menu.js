fml.define('component/menu', ['jquery', 'plugin/cookie', 'plugin/store'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var Cookies = require('plugin/cookie');
    var store = require('plugin/store');

    var NAV_TOGGLE_STATUS = 'nav_toggle_status';

    if (Cookies.get('thin')) {
        store.set(NAV_TOGGLE_STATUS, 'small');
        Cookies.remove('thin');
    }


    var Menu = {
        /**
         * 初始化主菜单
         */
        init: function () {
            var status = store.get(NAV_TOGGLE_STATUS);
            if (status == 'small') {
                $('.effect').addClass("mainnav-sm").removeClass('mainnav-lg');
                $('.mainnav-toggle').addClass('thin');
            } else {
                $('.effect').removeClass("mainnav-sm").addClass('mainnav-lg');
                $('.mainnav-toggle').removeClass('thin');
            }

            this.bindEvent();
            this.highlight();
        }
        /**
         * 高亮当前菜单项
         */
        , highlight: function () {
            var path = window.location.pathname;
            //console.log(path);
            path = $.inArray(path,['/','/home']) > 0 ? '/' : path;
            var currentUrl = window.location.pathname + window.location.search;
            //console.log('path',path,'currentUrl',currentUrl);
            /**
             * 处理菜单高亮.
             */
            $("#mainnav-menu").find('a').each(function(){
                var href = $(this).attr('href') || '';
                href = $.inArray(href,['/','/home']) > 0 ? '/' : href;
                //console.log(href);
                var match = false;
                //var level = href.replace(/^\//,'').split('/').length;

                if(href == '/' && path == href){
                    match = true;
                }else if(href != '/' && path.indexOf(href) >= 0){
                    match = true;
                }else if(href === currentUrl){
                    match = true;
                }else{
                    var ahref = href.split('/');
                    //console.warn(ahref);
                    var acurrentUrl = currentUrl.split('/');
                    //console.warn(acurrentUrl);
                    if(ahref[1]!='home'&&ahref[1]==acurrentUrl[1]&&ahref[2]==acurrentUrl[2]){
                        match = true;
                    }
                }



                if(match){
                    $(this).parent().addClass('active-link');
                    $(this).parents("ul.collapse").addClass('in');
                }
                $('.active-link').parents('li').addClass('active-link');
            });
        }
        ,bindEvent:function(){
            // 左侧导航展开隐藏
            $('.mainnav-toggle').on('click', function () {
                if ($(this).hasClass('thin')) {
                    store.set(NAV_TOGGLE_STATUS,'large');
                    $(this).removeClass('thin');
                } else {
                    $(this).addClass('thin');
                    store.set(NAV_TOGGLE_STATUS,'small');
                }
            });
        }
    };

    Menu.init();

    return Menu;
});