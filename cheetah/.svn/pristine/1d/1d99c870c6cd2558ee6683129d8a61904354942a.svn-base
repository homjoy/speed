var http = require('http');
var cookie = require(config.path.base + 'cookie.js');
var spamhost = config.api.spamhost;

var WHITELIST = ['twitter', 'user', 'magazine', 'group'];	//for aj or aw

var gatherInfo = function(req, res, uid){
	return function(evt) {
		var cata = req.url.replace('http://www.meilishuo.com', '').split('?')[0];
		if (cata.indexOf('/aj/') == -1 && cata.indexOf('/aw/') == -1) {
			cata = cata.split('/')[1];
		} else {
			var tmp = cata.split('/');
			if (WHITELIST.indexOf(tmp[2]) == -1)
				cata = tmp[1] + '/' + tmp[2];
		}
		http.get('http://' + spamhost + '/gather?uid=' + uid + '&cata=' + cata, function(res){
	//		console.log('Got response:' + res.statusCode);
	//		evt(res.statusCode);
		}).on('error', function(e){
	//		evt(false);
			console.log('Got error:' + e.message);
		});
		evt({});
	}
}

exports.init = function(_this) {
	if(!spamhost) return;
	var mSelf = _this;
	var cookies = cookie.getHandler(mSelf.req, mSelf.res);
	var uid = cookies.get('santorini_mm');
	if (!uid) return;

	_this.listenOn(function(evt){
		gatherInfo(mSelf.req, mSelf.res, uid)(evt);
	},'spam')();
}
