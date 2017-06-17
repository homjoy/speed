function pay() {
    return this;
}
var controlFns = {
    'index': function () {
        return this.redirectTo('/pay/apply/');
    },
    'apply': function () {
        var php = {
            'businessLine':'fms::/dictionary/getgetBusinessLineCode'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['s/ueditor/themes/default/css/ueditor.min','plugin/dropzone', 'plugin/zTree','queen/select', 'queen/tree-select', 'fms/pay/apply'];
            data._JSLinks = ['s/ueditor/ueditor.config', 's/ueditor/ueditor.all'];
            this.render('pay/apply.html', data);
        });
    },
    'edit': function () {
        var php = {
            "draft": "/contractpayrequest/showContractPayDraftJson",
            'businessLine':'fms::/dictionary/getgetBusinessLineCode'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['s/ueditor/themes/default/css/ueditor.min', 'plugin/dropzone', 'plugin/zTree','queen/select', 'queen/tree-select','fms/pay/apply'];
            data._JSLinks = ['s/ueditor/ueditor.config', 's/ueditor/ueditor.all'];
            this.render('pay/edit.html', data);
        });
    },
    'view': function () {
        var php = {
            "detail": "fms::/contractpayrequest/showContractPayAllJson"
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = [ 'fms/pay/view'];
            data._JSLinks = ['s/ueditor/ueditor.parse.min'];
            this.render('pay/view.html', data);
        });
    },
    'print': function () {
        var php = {
            "detail": "fms::/contractpayrequest/showContractPayAllJson"
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['fms/common/print'];
            data._JSLinks = ['s/ueditor/ueditor.parse.min'];
            this.render('pay/print.html', data);
        });
    }
};
exports.__create = controller.__create(pay, controlFns);