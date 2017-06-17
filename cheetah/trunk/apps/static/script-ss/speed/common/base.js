fml.define('speed/common/base', ['jquery', 'plugin/cookie', 'plugin/store', 'component/notify', 'component/menu', 'speed/meeting/checkin'], function (require, exports) {
    var $ = require('jquery');
    var Cookies = require('plugin/cookie');
    var store = require('plugin/store');
    var notify = require('component/notify');
    var checkin = require('speed/meeting/checkin');

    /**
     * 迁移各种 cookie设置到localStorage
     */
    (function moveCookie2store() {
        $.each(['passMyTimeWizard', 'passModalWizard', 'passOverviewWizard', 'userlead', 'meeting_only_workday', 'OA_MEILISHUO_TIME_TYPE'], function (index, key) {
            var value = Cookies.get(key);
            if (value !== undefined) {
                switch (key) {
                    case 'OA_MEILISHUO_TIME_TYPE':
                        store.set('time-index-calendar-view', value);
                        store.set('meeting-room-calendar-view', value);
                        break;
                    case 'userlead':
                        store.set('pass-user-index-wizard', value);
                        break;
                    default:
                        store.set(key, value);

                }
                Cookies.remove(key);
            }
        });
    })();


    $.post('/aj/meeting/get_check_in', {}, function (resp) {
        if (resp.code == 200) {
            $.each(resp.data || [], function (i, value) {
                checkin(value);
            })
        }
    }, 'json');

    $.getJSON('/aj/check/check_all', function (ret) {
        if (ret.code == 200) {
            var data = ret.data;
            var total = data.total;

            if (total != 0) {
                $('.badge').html(total);
                var str = '';
                for (var i = 0; i < data.data.length; i++) {
                    str += '<li>' +
                        '<a href="' + data.data[i].href + '" class="media" target="_Blank' + i + '">' +
                        '<span class="media-left left" >' +
                        data.data[i].name +
                        '</span>' +
                        '<span class=" label-success message_to_do right">' + data.data[i].count + '</span>' +
                            // '<div class="media-body">'+
                            //   '<div class="text-nowrap">Jackson sent you a message</div>'
                            //   <small class="text-muted">Yesterday</small>
                            // </div>
                        '</a>' +
                        '</li>';

                }
                $('.head-list-append').append(str);
                console.log('content',$('.head-list-append'));
            } else {
                $('.head-list-append').parent().hide();
            }
        } else {
            $('.head-list-append').parent().hide();
        }
    });

    //页面头部消息提示
   /* $.getJSON('/aj/user/get_user_notice',function (ret) {
        if (ret.code != 200) {
            return;
        }

        $.each(ret.data, function (k, v) {
            //增加提示.
            v.closeBtn = !!v.is_always;
            $.niftyNoty(v);
            //获取
            var alert = $(".alert-wrap .alert").last();
            //存入notice对象.
            alert.data('notice', v);
            //点击需打开链接
            if (v.link) {
                //alert.wrap('<a href="/page/go/?link=' + v.link + '"' + (v.new_window ? ' target="_blank" ' : '') + '></a>');
                alert.wrap('<a href="/page/go/?link=' + encodeURIComponent(v.link) + '"' + (v.new_window ? ' target="_blank" ' : '') + '></a>');
            }
        });
    });


    $('body').on("click", ".alert-wrap .alert button.close", function (e) {
        e.stopPropagation();
        //屏蔽链接跳转。
        //return false;
    }).on("click", ".alert-wrap .alert", function (e) {
        var alert = $(this);
        var notice = alert.data('notice');
        if (!notice) {
            return;
        }
        $.post('/aj/user/notice_mark', {notice_id: notice.notice_id}, function (ret) {}, 'json');
    });
*/

    //if(!store.get('position')){
    //    console.log(!store.get('position'));
    //    $.niftyNoty({
    //        type:"danger",
    //        icon:"laba",
    //        title: '亲，为了能让小伙伴快速定位到你的坐标，到个人中心填写下工位号吧，填完就会在通讯录中展示哦',
    //        message:"",
    //        timer:99999999
    //    });
    //}
    //
    //
    //$(function(){
    //    $.each($('.alert-title'),function(key,val){
    //        if($(val).html()== '亲，为了能让小伙伴快速定位到你的坐标，到个人中心填写下工位号吧，填完就会在通讯录中展示哦'){
    //            $(this).parents('.alert').on("click",function(){
    //                window.location.href='/user';
    //                store.set('position',1);
    //            });
    //        }
    //    });
    //});

    if (SPEED.monitor && SPEED.monitor.message) {
        notify.warning(SPEED.monitor.message);
    }

    $("#mainnav-menu li a[href$=time]").on('click', function () {
        //如果是第一次.
        if (!store.get('passMyTimeWizard')) {
            window.location.href = '/time/my/';
            return false;
        }
    });


    var currentToken = Cookies.get('speed_token');
    if (currentToken) {
        Cookies.remove('speed_token');
        Cookies.remove('speed_token', {domain: 'meilishuo.com'});
        Cookies.set('speed_token', currentToken, {expires: 7, domain: window.location.hostname});
    }


    /**
     * 处理需要跳转到最后一次访问的链接的按钮
     */
    $("body").on('click', '[data-previous]', function (e) {
        var storeKey = $(this).attr('data-previous');
        if (!storeKey) {
            return true;
        }

        var previousLink = store.get(storeKey);
        if (previousLink) {
            $(this).attr('href', previousLink);
        }
    });
    /**
     *header部分联系客服事件处理
     */
    $(function () {
        $('.speedim-nav').click(function () {
            window.location.href = "speedim://open/";
        });
    });

    /**
     * search contact搜索触发事件 icon/keydown
     */
    $('.search_pic').click(function () {
        var q = $('.input_select').val();
        if (!q) {
            return false;
        }
        // window.location.href="/contacts/search?q="+q+"&key="+$('.button_select_word').attr('name');
        window.location.href = "/contacts/?q=" + q;
    });

    //回车响应搜索通讯录.
    $('.input_select').on('keydown', function (e) {
        var e = e || event;
        var currKey = e.keyCode || e.which || e.charCode;
        if (currKey == 13) {
            $('.search_pic').trigger('click');
        }
    });


    /**
     * header added for dynamic
     */

/*
    $('.tgl-menu-btn').on('click',function(){
       btnToggleClass();
    });

    function btnToggleClass(){
        var $Btn =  $('.tgl-menu-btn').find('.mainnav-toggle'),
            $navBar = $('#navbar'),
            $container = $('#container');
        if($container.hasClass('mainnav-sm')){
            $navBar.removeClass('wideNav').addClass('thinNav');
        }else{
            $navBar.removeClass('thinNav').addClass('wideNav');
        }
    }

    (function(){
        btnToggleClass();
    })();*/
});