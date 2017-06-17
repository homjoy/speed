fml.define("fex/doc/pagination",['jquery','component/pagination'],function(require,exports){
    "use strict";
    var $ = require('jquery');

    $("#pagination-basic").pagination({
        totalPage:200,
        currentPage:1,
        lastPagesCount:1
    }).on("switch",function(e,page){
        //console.log(page);
    });


    $("#pagination-minimal").pagination({
        totalPage: 30, //总页数，默认值为10
        startPage: 1, //起始页数，默认为1开始
        currentPage: 1, //初始选中的页码，默认值为1
        firstPagesCount: 0, //最前面的展现页数，默认值为2
        preposePagesCount: 2,  //当前页的紧邻前置页数，默认值为2
        postposePagesCount: 0, //当前页的紧邻后置页数，默认值为1
        lastPagesCount: 2,//最后面的展现页数，默认值为0
        href: false,    //不生成链接
        first: '', //取消首页
        prev: '<',
        next: '>',
        last: '', //取消尾页
        go: '' //取消页面跳转
    }).on("switch",function(e,page){
        console.log(page);
    });
});