fml.define('speed/meeting/checkin', ['jquery', 'plugin/moment','component/notify'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var moment = require('plugin/moment');
    var MessageNotify = require('component/notify');

    var template = '<div class="alert-wrap animated jellyIn alert-check-in" data-bookId="{book_id}">' +
        '<div class="alert" role="alert">' +
        '<div class="media"><div class="media-left">' +
        '<a href="javascript:void(0);" class="icon-checkin" ></a></div>' +
        '<div class="media-body">' +
        '<h4 class="alert-title">会议室签到<span class="time-remain" data-remain="{remain}">剩余<em>{time}</em>分钟</span></h4>' +
        '<p class="alert-message">' +
        '{title}<br/>地点:{place}' +
        '</p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    /**
     * 渲染模板.
     * @param data
     * @param diffTime
     * @returns {string}
     */
    function renderTemplate(data, diffTime) {
        //剩余时间
        var minuteRemain = Math.ceil(diffTime / 60);
        return template.replace('{book_id}', data.book_id)
            .replace('{remain}', Math.ceil(diffTime))
            .replace('{time}', minuteRemain)
            .replace('{title}', data.title || '')
            .replace('{place}', data.place || '');
    }

    var timer = null;
    return function (book) {
        //指定秒数后结束签到.
        var diffTime = book.timestamp || 0;
        if (diffTime <= 0) {
            return false;
        }
        var notify = $(renderTemplate(book, diffTime));
        var checkList = $('#floating-top-right .alert-check-in');
        if (checkList.size() > 0) {
            checkList.after(notify);
        } else {
            $('#floating-top-right').prepend(notify);
        }
        notify.addClass('in');

        //off掉再绑定，防止重复绑定.
        $(".alert-check-in").off('click').on('click', function () {
            var self = $(this);
            var bookId = self.attr('data-bookId');
            var remain = self.find('.time-remain').attr('data-remain');
            if (remain <= 0) {
                MessageNotify.warning('很遗憾，已经过了签到时间！');
                //过期了，关闭签到条
                self.fadeOut(function () {
                    $(this).removeClass('in').remove();
                });
                return;
            }

            $.post('/aj/meeting/check_in', {'book_id': bookId}, function (resp) {
                if (resp.code != 200) {
                    MessageNotify.error(resp.error_msg || '签到失败.');
                    return false;
                }

                MessageNotify.success("签到成功.");
                //self.find('.icon-checkin').addClass('checked');
                self.fadeOut(function () {
                    $(this).removeClass('in').remove();
                });
            }, 'json');
        });
        if (!timer) {
            //clearInterval(timer);
            timer = setInterval(function () {
                $('.alert-check-in .time-remain').each(function () {
                    var span = $(this);
                    var remain = span.attr('data-remain');
                    if (remain <= 0) {
                        //过期了，关闭签到条
                        span.closest('.alert-check-in').fadeOut(function () {
                            $(this).removeClass('in').remove();
                        });
                        return;
                    }
                    remain -= 1;
                    span.attr('data-remain', remain);
                    var minute = Math.ceil(remain / 60);
                    //var minute = Math.ceil(remain);
                    span.find('em').text(minute);
                });
            }, 1000);//一秒钟检测一次.
        }
    }
});