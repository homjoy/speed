function user() {
	return this;
}
var controlFns = {
	'index' : function(){
		var php = {
			'userInfoGet':'/user/user_info_get'
		};
		this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function(data){
			data._CSSLinks = ['plugin/bootstrapValidator','speed/user/index'];
			this.render('user/index.html' , data);
		});
	}
	, 'login': function () {
		var php = {};
		this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function (data) {
			if(data.userInfo && data.userInfo.code == 200 && data.userInfo.data.is_login){
				return this.redirectTo('/');
			}
			data._CSSLinks = ['speed/user/login'];
			this.render('user/login.html', data);
		})
	},
	'expire': function () {
		var php = {};
		this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function (data) {
			if(data.userInfo && data.userInfo.code == 200 && !data.userInfo.data.is_mfa_code_expire){
				return this.redirectTo('/');
			}
			data._CSSLinks = ['speed/user/login'];
			this.render('user/expire.html', data);
		})
	},
	'logout': function () {
		var self = this;
		var host = this.req.headers.host;
		var appMod = require(config.path.appPath + 'web/model/application.js');
		var speedToken = appMod.getCookie(self.req, self.res) || '';
		var php = {
			//'logout':'/auth/logout?speed_token='+speedToken.trim()
		};
		this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function (data) {
			//删除SPEED TOKEN.
			self.res.setHeader('Set-Cookie',[
				'speed_token=; Path=/; domain=.'+host+';expires=Thu, 01 Jan 1970 00:00:00 GMT;'
			]);
			//appMod.removeCookie(self.req, self.res,'speed_token');
			this.render('user/logout.html',data);
			//return this.redirectTo('/user/login/?from=logout');
		})
	}
	,'avatar' : function(){
		var php = {
		};
        this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function(data){
			data._CSSLinks = ['speed/user/avatar'];
			this.render('user/avatar.html' , data);
		})
	},
	'password' : function(){
		var php = {
			'userInfoGet':'/user/userInfoGet'
		};
		this.bindDefault(php);
		this.bridgeMuch(php);
		this.listenOver(function(data){
			data._CSSLinks = ['plugin/bootstrapValidator','speed/user/password'];
			this.render('user/password.html' , data);
		})
	},
	'safe':{
		'password' : function(){
			var php = {
				'userInfoGet':'/user/userInfoGet'
			};
			this.bindDefault(php);
			this.bridgeMuch(php);
			this.listenOver(function(data){
				data._CSSLinks = ['plugin/bootstrapValidator','speed/user/password'];
				this.render('user/password.html' , data);
			})
		}
		,'verification' : function(){
			var php = {
				'userInfoGet':'/user/userInfoGet'
			};
			this.bindDefault(php);
			this.bridgeMuch(php);
			this.listenOver(function(data){
				data._CSSLinks = ['plugin/bootstrapValidator','speed/user/password'];
				this.render('user/verification.html' , data);
			})
		}
		,'secretkey' : function(){
			var php = {
				'userInfoGet':'/user/userInfoGet'
			};
			this.bindDefault(php);
			this.bridgeMuch(php);
			this.listenOver(function(data){
				data._CSSLinks = ['plugin/bootstrapValidator','speed/user/password'];
				this.render('user/secretkey.html' , data);
			})
		}
	}

};
exports.__create = controller.__create(user, controlFns);