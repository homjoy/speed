function home() {
    return this;
}
var controlFns = {
    'index': function (param) {
        if (param == 'download') {
            return this.redirectTo('/page/download/');
        }
        return this.redirectTo('/page/404/');
    },
    'go': function () {
        "use strict";
        var link = this.req.__get['link'];
        if (!link) {
            return this.redirectTo('/');
        }

        //以/开头的链接为站内链接，直接跳转
        if (/^\//.test(link)) {
            return this.redirectTo(link);
        }

        if (!/^https?:\/\//.test(link)) {
            link = 'http://' + link;
        }

        var url = require('url').parse(link);
        var domainWhiteList = /meilishuo\.com|meiliworks\.com/;

        //白名单域名.
        if (domainWhiteList.test(url.host)) {
            return this.redirectTo(link);
        }
        
        return this.redirectTo('/');
    },
    '404': function () {
        var php = {};
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['nifty/fontfamily', 'nifty/font-awesome', 'nifty/bootstrap', 'nifty/nifty', 'nifty/pace.min'];
            this.render('page/404.html', data);
        });
    }
    , 'download': function () {
        var php = {
            'app': '/page/download_app'
        };
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            this.render('page/download.html', data);
        });
    },
    'jd': function () {
        var php = {};
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = [];
            data._JSLinks = [];
            this.render('page/jd.html', data);
        });
    },
    'shuttletime':function(){
        var php = {
            'get_bus_info':'/executive_card/get_bus_info'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['speed/shuttletime'];
            data._JSLinks = ['speed/common/shuttletime'];
            this.render('page/shuttletime.html', data);
        });
    }
};
exports.__create = controller.__create(home, controlFns);