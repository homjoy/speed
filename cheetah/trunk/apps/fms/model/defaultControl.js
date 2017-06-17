"use strict";
var publicPages = [
    '/user/login/',
    '/page',
    '/page/404/',
    '/page/expire/',
    '/page/ueditor/'
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
    php.userInfo = '/getUserInfo';
    php.permission = '/getActionPrivilegeJson';


    //统一加了.
    Object.keys(php).forEach(function (name) {
        //没有指定情况下，默认请求fms 的接口.
        if (php[name].indexOf('::') <= 0) {
            php[name] = 'fms::' + php[name];
        }
    });

    mSelf.eventHandle.onOver = function (data) {
        //增加CSS_BASE 路径.
        data.CSS_BASE = 'css/fms/';
        try {
            var userInfo = data.userInfo;
            //如果不是公共页面，但没获取到登陆用户的信息.
            if (!isPublic(mSelf.req.url) && (!userInfo || userInfo.rcode != 200 || !userInfo.data.userId)) {
                //未登录状态下跳转到speed的登陆界面.
                //mSelf.redirectTo(data.domain.speed + "/user/login/");
                //跳转到登陆失效页面.
                mSelf.redirectTo('/page/expire/');
                return false;
            }

            //非公共页面才能使用
            var permissionMenu = [];
            if (userInfo.menu) {
                var menuInfo = {
                    "220": {
                        "icon": "icon-home",
                        'url': "/home"
                    },
                    "221": {
                        "icon": "icon-myapply",
                        "url": "/my/apply/"
                    },
                    "222": {
                        "icon": "icon-myapproval",
                        "url": "/my/approval/"
                    },
                    "223": {
                        "icon": "icon-mymanage",
                        "url": "/my/manage/"
                    },
                    "224": {
                        "icon": "icon-receipt",
                        "url": "/query/"
                    },
                    "236": {
                        "icon": "icon-chaxun",
                        "url": "/report/"
                    },
                    "237":{
                        "icon":"icon-IDCinfo",
                        "url":"/IDCinfo/apply/"
                    }
                };

                var m;
                for (var i = 0, len = userInfo.menu.length || 0; i < len; i++) {
                    m = userInfo.menu[i];
                    if (!menuInfo.hasOwnProperty(m['res_code'])) {
                        continue;
                    }
                    permissionMenu.push({
                        "id": m['res_code'],
                        "name": m['res_name'],
                        "icon": menuInfo[m['res_code']].icon || '',
                        "url": menuInfo[m['res_code']].url || ''
                    });
                }
            }
            data.permissionMenu = permissionMenu || [];

            var permission = data.permission.action;
            data.permission = permission || [];
        } catch (e) {
            return false;
        }
    };
}

exports.bind = defaultAction;
