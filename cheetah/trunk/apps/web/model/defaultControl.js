"use strict";
var publicPages = [
    '/user/login/',
    '/page',
    '/page/404',
    '/page/download/'
];

/**
 * 检测链接是否是公共页面（免登陆）
 * @param current
 * @returns {boolean}
 */
function isPublic(current) {
    var path = current.split('?')[0] || '/';
    for (var i = 0, len = publicPages.length; i < len; i++) {
        if (path == publicPages[i]) {
            return true;
        }
    }
    return false;
}

/**
 * 公共的action处理
 * @param php
 */
function defaultAction(php) {
    if (!php) {
        console.log('php not assign ' + this.req.url);
        return;
    }
    var mSelf = this;
    var appMod = require(__dirname + '/application.js');
    var speedToken = appMod.getCookie(mSelf.req, mSelf.res) || '';
    php.userInfo = '/auth/check_login?speed_token=' + speedToken.trim();
    php.menu = '/core/get_menu_list';
    //php.notice = '/account/get_user_notice';
    mSelf.eventHandle.onOver = function (data) {
        try {
            var userInfo = data.userInfo;
            //如果不是公共页面，但没获取到登陆用户的信息.
            if (!isPublic(mSelf.req.url) && (!userInfo || userInfo.code != 200 || !userInfo.data.is_login)) {
                mSelf.redirectTo("/user/login/");
                return false;
            }

            //如果已过期.
            if (!isPublic(mSelf.req.url)
                && !/^\/user\/expire\//.test(mSelf.req.url) //非验证页
                && userInfo.data && userInfo.data.is_mfa_code_expire) {
                mSelf.redirectTo("/user/expire/");
                return false;
            }

            /*
             * 公共页面不作处理.
             */
            if (!isPublic(mSelf.req.url) && userInfo.data.user
                && userInfo.data.user.monitor
                && mSelf.req.url.indexOf(userInfo.data.user.monitor.url) == -1
            ) {
                mSelf.redirectTo(userInfo.data.user.monitor.url);
                return false;
            }


        } catch (e) {
            return false;
        }
    };
}

exports.bind = defaultAction;