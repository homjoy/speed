fml.define('speed/common/iframe', ['jquery'], function (require, exports) {
    "use strict";
    window.document.domain = 'meilishuo.com';
    var $ = require('jquery');
    var outerWindow = window;
    var iFrame = null;
    var innerWindow = null;
    var SPEED = {
        init: function (frameId) {
            if (!frameId || $(frameId).size() !== 1) {
                return false;
            }
            //iframe加载完之后绑定宽高适应
            iFrame = $(frameId).on('load', function () {
                innerWindow = iFrame.get(0).contentWindow;
                innerWindow.$(".container", innerWindow.document).on("resize", adjustFrameSize);
                (function checkChange() {
                    adjustFrameSize();
                    scrollTo();
                    setTimeout(checkChange, 100);
                })();
            });
        },
        logout: function () {
            $('#logout').click();
        },
        redirect: function (url) {
            outerWindow.location.href = url;
            return true;
        },
        reload: function () {
            return outerWindow.location.reload();
        },
        routeTo: function (module, action, query) {
            if (!module || !action) {
                return false;
            }
            var url = '/home/window/?f=' + module + '&s=' + action;

            if (query) {
                url += '&q=' + encodeURIComponent(query);
            }
            this.redirect(url);
        }
    };


    var oldWidth = 0;
    var oldHeight = 0;
    /**
     * 适配IFRAME 的高度.
     */
    function adjustFrameSize() {

        var hashWidth = $('body', innerWindow.document).width();
        //var hashHeight = $("body").height();
        //不用body 的高度计算，有异常空白出现导致bug。
        var container = $("body > .container", innerWindow.document);
        if(container.size() == 0){
            return ;
        }
        var hashHeight = container.offset().top + container.height();
        var modalHeight = $('.modal.in .modal-dialog', innerWindow.document).height() || 0;
        hashHeight = Math.max(hashHeight, modalHeight);
        if (hashWidth && hashHeight && (oldWidth != hashWidth || oldHeight != hashHeight)) {
            iFrame.css({
                //width:hashWidth + "px",
                height: hashHeight + "px"
            });
            oldWidth = hashWidth;
            oldHeight = hashHeight;
        }
    }

    var oldTop = 0;
    var needRevertScroll = false; //scroll 高度是否被还原了.
    /**
     * 跳转到对话框进行查看
     * 或者可以处理成让对话框早当前页面居中显示（但是设计各种元素的位置计算，过于繁琐，成本过高.）
     */
    function scrollTo() {
        var modalOpen = innerWindow.$('.modal.in').size() > 0;
        //有对话框被打开.
        if (modalOpen && !needRevertScroll) {
            oldTop = $(outerWindow).scrollTop();
            //$(outerWindow).scrollTop(0);
            $('html,body',outerWindow.document).stop(true).animate({scrollTop: '0px'}, 100);
            needRevertScroll = true;
        } else {
            //只在关闭对话框时还原到原来的位置.
            if (!modalOpen && needRevertScroll) {
                //$(outerWindow).scrollTop(oldTop);
                $('html,body',outerWindow.document).stop(true).animate({scrollTop: oldTop+'px'}, 100);
                oldTop = 0;
                needRevertScroll = false;
            }
        }
    }

    window.SPEED = SPEED;
    return SPEED;
});