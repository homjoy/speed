fml.define('speed/contacts/index', ['jquery'], function (require,exports) {
    "use strict";

    var $ = require('jquery');


// $('.button_select').click(function(){
//   // 搜索框左边点击展示ul
//   var _this = $(this).children('ul');
//   if($(_this).hasClass('hide')){
//     $(_this).removeClass('hide');
//   }else{
//     $(_this).addClass('hide');
//   }
// });
// $('.choose_select li').click(function(){
//   // 更改，赋值
//   $('.button_select_word').html($(this).html()).attr('name',$(this).attr('name'));
// });

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg); //匹配目标参数

        if (r != null) return decodeURI(r[2]);
        return null; //返回参数值
    }

    //立即函数，加载后判断当前是查询结果页面／通常界面
    (function(){
        var $poneInfoDescribe = $('.phone-info-describe'),
            href = window.location.href,
            index = href.indexOf("/?q=");

        var paramValue = getUrlParam('q');

        if(paramValue){
            $poneInfoDescribe.text('搜索结果');
            $('.input_select').val(paramValue);

            return false;
        }

        $poneInfoDescribe.text('我所在部门人员');

    })();

    $('.select_pic').on('click', function () {
        var q = $('.input_select').val();
        if (q) {
            // window.location.href="/contacts/search?q="+q+"&key="+$('.button_select_word').attr('name');
            window.location.href = "/contacts/?q=" + q;
        }
    });

//启动speedim客户端
    $(function () {
        $('.speedim').click(function () {
            window.location.href = "speedim://open/";
        });
    });
/*// 回车响应
    $(document).on('keydown', function (e) {
        var e = e || event;
        var currKey = e.keyCode || e.which || e.charCode;
        if (currKey == 13) {
            $('.select_pic').click();
        }
    });*/

// 电话号
    $('.telephone').click(function () {
        $('.show_tel').html($('.show_tel').attr('tel')).removeClass('show_tel');
        var speed_im = $(this).attr('code');
        var _this = $(this);
        $(_this).addClass('show_tel');
        $.getJSON('/aj/address/ajax_search_mobile', {code: speed_im}, function (ret) {
            if (ret.code == 200) {
                $(_this).html(ret.data);
            }
        });
//   $(_this).on('blur',function(){
//   $(_this).html($(_this).attr('tel'));
// });
    });
// $('.telephone').on('blur',function(){
//   $(this).html($(this).attr('tel'));
// });
    $('.qrcode').popover({
        'placement': 'left',
        'html': true,
        'trigger': 'focus',
        'delay': {show: 300, hide: 0},
        'title': '微信一扫，添加至通讯录',
        'content': function () {
            var t = $(this);
            var data = t.data();
            if (data.qrcode != '') {
                return '<img class="head_img" src="' + $(this).data().qrcode + '"><span class="QQ_weixin"><b>QQ:' + $(this).data().qq + '</b></span>';
            } else {
                return '';
            }
        }
    });
// 高亮
    $('.contact_head').popover({
        'placement': 'right',
        'html': true,
        'trigger': 'hover',
        'delay': {show: 300, hide: 0},
        'title': '照片',
        'content': function () {
            var t = $(this);
            var data = t.data();
            if (data.avatar_big != '') {
                return '<img class="avatar" src="' + $(this).data().avatar + '">';
            } else {
                return '';
            }
        }
    });
//parent.document.all("external-frame").style.height=document.body.scrollHeight;

    //$('#mainnav-menu-wrap a[href="/contacts"]').parent().addClass('active-link');
// $('.select_div').on('click',function(e){
//   e.preventDefault();
//   var isCon = $('#floating-top-right').html();
//   if(!isCon){
//       $.niftyNoty({
//       type: 'info',
//       icon: 'fa fa-info fa-lg',
//       title: 'title',
//       container: 'floating',
//       message: '测试测试',
//       timer: 20000
//     })
//   }
// });
// $('.select_div').on('click',function(e){
//   e.preventDefault();
//   // #page-alert 有内容，就不生成tip，没有内容或者无该dom节点，生成tip
//   var isCon = $('#page-alert').html();
//   if(!isCon){
//       $.niftyNoty({
//       type: 'pink',
//       icon: 'fa fa-heart fa-lg',
//       title: 'page长条',
//       message: 'testtesttest',
//       timer: 'Sticky Alert Box'
//     })
//   }
// });
});