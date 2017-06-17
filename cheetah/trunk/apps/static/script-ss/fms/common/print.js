fml.define('fms/common/print', ['jquery'], function (require, exports) {
    var $ = require('jquery');

    //@see http://fex-team.github.io/ueditor/#start-uparse
    //处理页面没有边框的BUG，
    //http://redmine.meilishuo.com/issues/40583
    var basePath = fml.getOption('modulebase');
    if (typeof uParse === 'function') {
        uParse('.ueditor-content', {
            rootPath: basePath + '/s/ueditor/'
        });
    }

    (function () {
        var beforePrint = function () {

        };
        var afterPrint = function () {
            setTimeout(function () {
                window.close();
            }, 0);
        };

        //chrome
        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function (mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }

        window.onbeforeprint = beforePrint;
        window.onafterprint = afterPrint;
    }());


    window.print();
});