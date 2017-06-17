fml.define('component/CountDown', ['jquery', 'plugin/store'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), store = require('plugin/store');

    var DEFAULT = {
        countDownTime: 60,
        storeKey: null,
        ajax: {
            type: "post",
            url: '/aj/user/send_login_verify'
        },
        getData: function () {
            return null;
        },
        onComplete:function(){}
    };

    /**
     *
     * @param element
     * @param options
     * @constructor
     */
    function CountDown(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, DEFAULT, options, true);

        this.countDownTime = this.lastTime();
        this.defaultContent = this.$element.html();

        //说明上一次的还没有倒计时完毕.
        if (this.countDownTime > 0) {
            this.toggleStatus('count');
        } else {
            this.countDownTime = this.options.countDownTime;
            this.status = 'reset';
        }

        this.render();
    }

    CountDown.prototype = {
        render: function () {
            var that = this;

            this.$element.on("click", function (e) {
                e.preventDefault();

                //除了reset 以外都不能进行发送.
                if (that.status != 'reset') {
                    return false;
                }

                //发送检查
                if ($.isFunction(that.options.getData) && that.options.getData.call(that) === false) {
                    return false;
                }


                var ajaxOptions = that.getAjaxOptions();
                if ($.isFunction(that.options.getData)) {
                    ajaxOptions.data = that.options.getData.call(that);

                    //不处理.
                    if (ajaxOptions.data === false) {
                        return false;
                    }
                }

                that.toggleStatus('sending');
                $.ajax(ajaxOptions);
            });
        },
        getAjaxOptions: function () {
            var that = this;
            var ajaxOptions = $.extend({}, this.options.ajax, {}, true);
            ajaxOptions.success = function (ret) {
                var result = true;
                $.isFunction(that.options.onComplete) && (result = that.options.onComplete.call(that,ret));

                if(result === false){
                    that.toggleStatus('error');
                    return;
                }

                that.toggleStatus('success');
                //记录开始发送时间.
                that.options.storeKey && store.set(that.options.storeKey, (new Date().getTime()));
                setTimeout(that.decrement.bind(that), 1000);
            };
            ajaxOptions.error = function () {
                that.toggleStatus('error');
            };

            return ajaxOptions;
        },
        lastTime: function () {
            var lastTime = store.get(this.options.storeKey) || 0;
            if (!lastTime) {
                return -1;
            }
            //距离上一次发送的秒数
            var passSecond = Math.floor((new Date().getTime() - lastTime) / 1000);

            //获取时间差
            return (this.options.countDownTime - passSecond);
        },
        decrement: function () {
            this.countDownTime--;
            if (this.countDownTime <= 0) {
                this.countDownTime = this.options.countDownTime;
                this.options.storeKey && store.set(this.options.storeKey,0);
                this.toggleStatus('reset');
            } else {
                this.toggleStatus('count');
            }
        },
        toggleStatus: function (status) {
            var that = this;
            var $element = this.$element;
            this.status = status;

            switch (status) {
                case 'sending':
                    $element.attr('disabled',true).text('正在发送...');
                    break;
                case 'success':
                    $element.text('发送成功!');
                    break;
                case 'error':
                    $element.text('发送失败!');
                    setTimeout(function () {
                        that.toggleStatus('reset');
                    }, 1500);
                    break;
                case 'count':
                    $element.attr('disabled', true);
                    $element.text(this.countDownTime + '秒后重试');
                    setTimeout(that.decrement.bind(that), 1000);
                    break;
                case 'reset':
                    //重置
                    $element.removeAttr('disabled').html(this.defaultContent);
                    break;
                default:
                    break;
            }
        }
    };


    return CountDown;
});