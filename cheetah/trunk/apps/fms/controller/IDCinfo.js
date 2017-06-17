function IDCinfo(){
    return this;
}

var controlFns = {
    'index':function(){
        return this.redirectTo('/IDCinfo/apply/');
    },
    'apply':function(){
        var php = {
            //机房信息
            'areaEngineRooms':'fms::/dictionary/getdictionarybytypejson?type=ROOMAREA',
            'contractNos':'fms::/serverroom/queryallcontractnojson',
            'payTypes':'fms::/dictionary/getdictionarybytypejson?type=PAYINTRVL',
            'linkTypes':'fms::/dictionary/getdictionarybytypejson?type=LINKTYPE'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function(data){
            data._CSSLinks = ['fms/IDCinfo/apply'];

            this.render('IDCinfo/apply.html', data);
        });
    },
    'edit':function(){
        //获取保存草稿内容
        var php = {
            //表单详情信息
            'detail':'fms::/serverroom/showserverroomalljson',
            //机房信息
            'areaEngineRooms':'fms::/dictionary/getdictionarybytypejson?type=ROOMAREA',
            'contractNos':'fms::/serverroom/queryallcontractnojson',
            'payTypes':'fms::/dictionary/getdictionarybytypejson?type=PAYINTRVL',
            'linkTypes':'fms::/dictionary/getdictionarybytypejson?type=LINKTYPE'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function(data){
            data._CSSLinks = ['fms/IDCinfo/apply'];

            this.render('IDCinfo/edit.html', data);
        });
    },
    'view':function() {
        //获取相关信息
        var php = {
            //表单详情信息
            'detail':'fms::/serverroom/showserverroomalljson',
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['fms/IDCinfo/apply'];

            this.render('IDCinfo/view.html',data);
        });
    },
    'print':function(){
        var php = {
            //表单详情信息
            'detail':'fms::/serverroom/showserverroomalljson',
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['fms/common/print'];
            this.render('IDCinfo/print.html',data);
        });
    }

};
exports.__create = controller.__create(IDCinfo, controlFns);