fml.define("fex/doc/trees", ['jquery', 'component/trees', 'fex/department'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var departments = require('fex/department');

    $("#tree-select").QueenTrees({
        multiple:true,
        placeholder:'申请部门',
        data:departments
        //allowSearch:false,
    }).on("change",function(event,nodes){
        console.log($(this).val(),nodes);
    });




});