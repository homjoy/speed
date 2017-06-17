function home() {
    return this;
}
var controlFns = {
    'index': function () {
        var php = {};
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['fex/prism','fex/home/index'];
            this.render('home/index.html', data);
        });
    }
};
exports.__create = controller.__create(home, controlFns);
