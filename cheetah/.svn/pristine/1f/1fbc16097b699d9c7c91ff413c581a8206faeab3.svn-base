function it() {
    return this;
}
var controlFns = {
    'index': function () {
        //var php = {
        //
        //};
        //this.bindDefault(php);
        //this.bridgeMuch(php);
        //this.listenOver(function (data) {
        //
        //    data._CSSLinks = [ 'speed/common','speed/it/index'];
        //    //data._JSLinks = ['plugin/jquery-ui.custom.min'];
        //    this.render('it/index.html', data);
        //});
        return this.redirectTo('/it/wifipassword/');

    }

    , 'wifi': function () {
        var php = {
            'VisitorWifiInfo':'/itserver/visitor_wifi_info'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = [ 'speed/common','speed/it/wifi'];
            this.render('it/wifi.html', data);
        });
    }
    , 'wifipassword': function () {
        var php = {
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {

            data._CSSLinks = [ 'speed/common','speed/it/index'];
            //data._JSLinks = ['plugin/jquery-ui.custom.min'];
            this.render('it/wifipassword.html', data);
        });
    }
    , 'vpnpassword': function () {
        var php = {
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {

            data._CSSLinks = [ 'speed/common','speed/it/index'];
            //data._JSLinks = ['plugin/jquery-ui.custom.min'];
            this.render('it/vpnpassword.html', data);
        });
    }
};
exports.__create = controller.__create(it, controlFns);