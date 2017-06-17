fml.define('component/notify', ['jquery', 'plugin/bootbox'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var bootbox = require('plugin/bootbox');
    var timer = null;
    var options = {
        timeout: 2000
    };

    /**
     * 显示提示信息
     * @param message
     * @param cls
     * @param callback
     */
    function showMessage(message, cls, callback) {
        cls = cls || 'success';

        var container = $('.queen-msg-container');
        if (container.size() == 0) {
            container = $('<div class="queen-msg-container"></div>').appendTo('body');
        }
        container.html('<div class="message ' + cls + '">' + message + '</div>');

        container.slideDown();
        if (timer) {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            container.slideUp(function () {
                $(this).remove();
                if (callback && typeof callback == 'function') {
                    callback();
                }
            })
        }, options.timeout);
    }

    /**
     * 成功提示
     * @param msg
     * @param cb
     * @returns {*}
     */
    exports.success = function (msg, cb) {
        return showMessage(msg, 'success', cb);
    };


    /**
     * 普通提示信息.
     * @param msg
     * @param cb
     * @returns {*}
     */
    exports.info = function (msg, cb) {
        return showMessage(msg, 'info', cb);
    };

    /**
     * 警告提示
     * @param msg
     * @param cb
     * @returns {*}
     */
    exports.warning = function (msg, cb) {
        return showMessage(msg, 'warning', cb);
    };

    /**
     * 错误提示
     * @param msg
     * @param cb
     * @returns {*}
     */
    exports.error = function (msg, cb) {
        return showMessage(msg, 'error', cb);
    };


    /**
     * 警告框
     * @param message
     * @param callback
     */
    exports.alert = function (message, callback) {
        bootbox.alert(message, callback);
    };

    /**
     * 确认框
     * @param message
     * @param callback
     */
    exports.confirm = function (message, callback) {
        bootbox.confirm(message, callback);
    };

    /**
     * 输入框
     * @param message
     * @param callback
     */
    exports.prompt = function (message, callback) {
        bootbox.prompt(message, callback);
    };

    /**
     * 表单对话框
     * @param title
     * @param message
     * @param success
     * @param cancel
     */
    exports.formDialog = function(title, message, success, cancel) {
        bootbox.dialog({
            className: "notify-box",
            title: title,
            message: message,
            buttons: {
                success: {
                    label: '确定',
                    className: 'btn-success',
                    callback: success
                },
                cancel: {
                    label: '取消',
                    className: 'btn-default btn-cancel',
                    callback: cancel
                }
            }
        });
    }


    /**
     * 审批弹框.
     */
    exports.approval = function (title, content, options) {
        var boxOptions = {
            className: "notify-box approval-dialog",
            title: title,
            message: content,
            closeButton: false,
            backdrop: true,
            onEscape: function () {
            },
            buttons: {
                cancel: {
                    label: '取消',
                    className: 'btn-default btn-cancel',
                    callback: options.cancel
                },
                success: {
                    label: '确定',
                    className: 'btn-primary',
                    callback: options.success
                }
            }
        };
        bootbox.dialog(boxOptions);
    };


    /**
     * 小型
     * @param message
     * @param options
     */
    function smallDialog(message, options) {
        var boxOptions = {
            size: 'small',
            className: "notify-box notify-sm",
            message: message,
            closeButton: false,
            backdrop: true,
            onEscape: function () {
            },
            buttons: {
                cancel: {
                    label: '取消',
                    className: 'btn-default btn-cancel',
                    callback: options.cancel
                },
                success: {
                    label: '确认',
                    className: 'btn-primary',
                    callback: options.success
                }
            }
        };
        bootbox.dialog(boxOptions);
    }

    exports.smallDialog = smallDialog;


    /**
     * 取消对话框
     * @param callback
     * @returns {*}
     */
    exports.cancel = function (callback) {
        return smallDialog('<i class="icon-cancel"></i><p>确认取消?</p>', {
            success: function () {
                if ($.isFunction(callback)) {
                    callback(true);
                }
            },
            cancel: function () {
                if ($.isFunction(callback)) {
                    callback(false);
                }
            }
        });
    };

    /**
     *
     * @param callback
     * @returns {*}
     */
    exports.decline = function (callback) {
        return smallDialog('<i class="icon-decline"></i><p>残忍拒绝?</p>', {
            success: function () {
                if ($.isFunction(callback)) {
                    callback(true);
                }
            },
            cancel: function () {
                if ($.isFunction(callback)) {
                    callback(false);
                }
            }
        });
    }
});