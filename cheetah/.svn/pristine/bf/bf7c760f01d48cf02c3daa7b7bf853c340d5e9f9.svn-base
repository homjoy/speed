function user() {
	return this;
}
var controlFns = {
	'index' : function(){
		return this.redirectTo('/home');
	}
	,'logout' : function(){
		var php = {
			'logout' : '/logout'
		};
        this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function(data){
			return this.redirectTo(data.domain.speed);
		});
	}
};
exports.__create = controller.__create(user, controlFns);