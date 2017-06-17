fml.define('speed/common/sendmessage',['jquery','component/notify'],function(require,exports){
    var $ = require('jquery');
    var notify = require('component/notify');

    var defaults = {
        "url":"/aj/user/send_sms_captcha"
    }

    function sendmessage(element,options){
        this.element = element;
        this.$element = $(element);
        this.settings = $.extend({},defaults,options);
        this.init();
    }

    sendmessage.prototype={
        init:function(){
            this.render();
            return this;
        },
        render:function(){
            console.log(2);
            var time;
            var quartz;
            var self = this;
            self.$element.click(function () {
                console.warn(self);
                var _this = $(this);
                _this.attr('disabled', 'false');
                _this.html('正在发送...');
                time = 60;
                $.getJSON(self.settings.url||defaults.url, {send_type:self.settings.send_type||''}, function (ret) {
                    if (ret.code != 200) {
                        _this.removeAttr('disabled');
                        _this.html('获取验证码');
                        notify.error(ret.message);
                        return;
                    } else {
                        quartz = setInterval(function () {
                            if (time > 0) {
                                _this.html('发送成功，' + time + '秒后可重试');
                                time--;
                            } else {
                                _this.removeAttr('disabled');
                                _this.html('获取验证码');
                                clearInterval(quartz);
                            }
                        }, 1000);
                    } //else
                });
            });
        }
    }


    $.fn.sendmessage = function (options) {
        options = $.extend(true,{}, defaults, options);
        return this.each(function () {
            //var self = $(this);
            //if (undefined == self.data('queen-select')) {
                new sendmessage(this, options);
            //self.data('queen-select', new Select(this, options));

            //}
        })
    };

    return sendmessage;
});