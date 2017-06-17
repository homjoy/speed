function my() {
    return this;
}
var controlFns = {
    'index': function () {
        return this.redirectTo('/my/apply/');
    },
    //我的申请
    'apply': {
        'index': function(){
            return this.redirectTo('/my/apply/reimburse');
        },
        //IDC机房
        'IDCinfo':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/IDCinfo.html', data);
            });
        },
        //合同
        'contract':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/contract.html', data);
            });
        },
        //付款
        'pay':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/pay.html', data);
            });
        },
        'budget':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/budget.html', data);
            });
        },
        'reimburse':function(){
            var php = {
                'addresses': 'fms::/dictionary/getattributionjson'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base','fms/my/approval/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/reimburse.html', data);
            });
        },
        'loan':function(){
            var php = {
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base','fms/my/approval/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/apply/loan.html', data);
            });
        }
    },
    'approval':{
        'index': function(){
            return this.redirectTo('/my/approval/reimburse');
        },
        //IDC机房
        'IDCinfo':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/approval/IDCinfo.html', data);
            });
        },
        'contract':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                this.render('my/approval/contract.html',data)
            });
        },
        'pay':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                this.render('my/approval/pay.html',data)
            });
        },
        'budget':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                this.render('my/approval/budget.html',data)
            });
        },
        'reimburse':function(){
            var php = {
                'addresses': 'fms::/dictionary/getattributionjson'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base','fms/my/approval/base'];
                this.render('my/approval/reimburse.html',data)
            });
        },
        'loan':function(){
            var php = {
                'addresses': 'fms::/dictionary/getattributionjson'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base','fms/my/approval/base'];
                data._JSLinks = ['s/ueditor/ueditor.parse.min'];
                this.render('my/approval/loan.html', data);
            });
        }
    },
    'manage':{
        'index': function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                var userTabs = data.userInfo.label.my_manage, firstTab;

                if(userTabs && userTabs.length) {
                    firstTab = userTabs.shift();
                    return this.redirectTo(firstTab['labelLink']);
                }

                return this.redirectTo('/my/manage/reimburse');
            });

        },
        'contract':function(){
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/zTree','queen/tree-select','queen/upload','fms/my/manage/base'];
                this.render('my/manage/contract.html', data);
            });
        },
        'pay': function () {
            var php = {};
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/zTree','queen/tree-select','fms/my/manage/base'];
                this.render('my/manage/pay.html', data);
            });
        },
        'reimburse':function(){
            var php = {
                'addresses': 'fms::/dictionary/getattributionjson'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                this.render('my/manage/reimburse.html', data);
            });
        },
        'loan':function(){
            var php = {
                'addresses': 'fms::/dictionary/getattributionjson'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function (data) {
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','nifty/bootstrap-select.min','queen/approval','queen/timeline','fms/my/base'];
                this.render('my/manage/loan.html', data);
            });
        }
    }
};
exports.__create = controller.__create(my, controlFns);