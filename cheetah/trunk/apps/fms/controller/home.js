function home() {
    return this;
}
var controlFns = {
    'index': function () {
        var php = {
            // 我的审批
            'approvalList': '/contract/AllTasklistJson',
            // 我的申请
            'applyList': '/contract/AllMyBilllistJson',
            'quickNav': '/getmainpagemenu'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['fms/home/index'];
            //data.quickNav = {
            //    rcode: 200,
            //    rmessage: "",
            //    data: [
            //        {
            //            "name": "hetong",
            //            "icon": "icon-hetong",
            //            "text": "合同",
            //            "link": "/contract/apply/",
            //            "new_window": false,
            //            "tips": ''
            //        },
            //        {
            //            "name": "fukuan",
            //            "icon": "icon-fukuan",
            //            "text": "付款",
            //            "link": "/pay/apply/",
            //            "new_window": false,
            //            "tips": '',
            //            "children": [
            //                {
            //                    "text": "新版付款",
            //                    "link": "/pay/apply/",
            //                    "new_window": false
            //                },
            //                {
            //                    "text": "旧版付款",
            //                    "link": "/pay/apply/",
            //                    "new_window": false
            //                }
            //            ]
            //        },
            //        {
            //            "name": "yusuan",
            //            "icon": "icon-yusuan",
            //            "text": "预算",
            //            "link": "/budget/apply/",
            //            "new_window": false,
            //            "tips": '',
            //            "children": [
            //                {
            //                    "text": "预算申请",
            //                    "link": "/budget/apply/",
            //                    "new_window": false
            //                },
            //                {
            //                    "text": "预算调整",
            //                    "link": "/budget/adjust/apply",
            //                    "new_window": false
            //                }
            //            ]
            //        },
            //        {
            //            "name": "baoxiao",
            //            "icon": "icon-baoxiao",
            //            "text": "报销",
            //            "link": "/reimburse/general/",
            //            "new_window": false,
            //            "tips": '',
            //            "children": [
            //                {
            //                    "text": "交通费及餐费",
            //                    "link": "/reimburse/traffic/",
            //                    "new_window": false
            //                },
            //                {
            //                    "text": "差旅费",
            //                    "link": "/reimburse/travel/",
            //                    "new_window": false
            //                },
            //                {
            //                    "text": "通用费用",
            //                    "link": "/reimburse/general/",
            //                    "new_window": false
            //                }
            //            ]
            //        },
            //        {
            //            "name": "jiekuan",
            //            "icon": "icon-jiekuan",
            //            "text": "借款",
            //            "link": "/reimburse/loan/",
            //            "new_window": false,
            //            "tips": ''
            //        }
            //    ]
            //};
            this.render('home/index.html', data);
        });
    }
};
exports.__create = controller.__create(home, controlFns);