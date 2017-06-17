function page() {
    return this;
}
var controlFns = {
    'index': function () {
        this.redirectTo('/page/404/');
    },
    '404': function () {
        this.listenOver(function (data) {
            data._CSSLinks = [];
            data._JSLinks = [];
            this.render('page/404.html', data);
        });
    },
    'expire': function () {
        var php = {};
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            if(data && data.userInfo && data.userInfo.rcode == 200 && data.userInfo.data && data.userInfo.data.userId){
                return this.redirectTo('/');
            }
            data._CSSLinks = [];
            data._JSLinks = [];
            this.render('page/expire.html', data);
        });
    },
    'icon': function () {
        this.listenOver(function (data) {
            this.render('page/icon.html', data);
        });
    }
    ,'ueditor': function () {
        this.listenOver(function (data) {
            this.render('page/ueditor.json', data);
        });
    }
};
exports.__create = controller.__create(page, controlFns);