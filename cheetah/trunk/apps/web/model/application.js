var cookie = require(config.path.base + 'cookie.js')
	,urlHandler = require('url')

exports.getCookie = function(req , res){
	var cookieHandle = cookie.getHandler(req ,res);
	var cookieKey = cookieHandle.get('speed_token'); 
	return cookieKey;
};

/**
 * 删除COOKIE.
 * @param req
 * @param res
 * @param name
 * @returns {*}
 */
exports.removeCookie = function(req,res,name)
{
	var cookieHandle = cookie.getHandler(req ,res);
	return cookieHandle.clear(name);
};

exports.getGlobalKey = function(req , res){
	/*
	$currentStamp = date('ymdHis', $time) . rand(0, 9) . rand(0, 9) . rand(0, 9);
	$globalKey = substr(commFun::getUniqueId (), 0, 17); 
	$globalKey .= $currentStamp;
	 */
	var cookieHandle = cookie.getHandler(req ,res)
	var globalKey = cookieHandle.get('GLOBAL_KEY')
	if (!globalKey){
		var seashell = req.headers.seashell
		if (seashell && seashell.length > 30) {
			seashell = seashell.substr(seashell.indexOf('=') + 1)
			var str = seashell.substring(8 , 16),
				revert = ''

			for(var i = str.length ; i>0 ; i = i-2){
				revert +=  str.substr(i-2 , 2)
			}
			var timestamp = new Date(parseInt(revert,16) * 1000),
				code = base.md5(seashell)
			globalKey = code.substr(0 , 17) + base.date('ymdHis' , timestamp) + code.substr(-6,3)
		}else{
			globalKey = base.md5(base.uuid() + new Date).substr(0, 17) +  base.date('ymdHis') +  (Math.floor(Math.random()*899 )+ 100)
		}

		var expires = new Date
		expires.setFullYear(expires.getFullYear() + 600)
		cookieHandle.set('GLOBAL_KEY' , globalKey , expires)
		req.headers.cookie += ';GLOBAL_KEY='+globalKey
		}
/*	
	var santorini = cookieHandle.get('santorini_mm')
	if (!santorini){
		santorini =  base.md5(globalKey + new Date)	
		cookieHandle.set('santorini_mm' , santorini)
		req.headers.cookie += ';santorini_mm='+ santorini
		
		}
*/
	return globalKey
}

exports.getBrowser = function(req){
	var browser = {},
		ua = req.headers['user-agent']
	if (!ua) return browser;
	if (/msie/i.test(ua)) {
		browser.msie = true;
		if (/6.0/i.test(ua)) browser.version = '6.0';
		else if (/7.0/i.test(ua)) browser.version = '7.0';
		else if (/8.0/i.test(ua)) browser.version = '8.0';
		else if (/9.0/i.test(ua)) browser.version = '9.0';
	} else if (/chrome/i.test(ua)) {
		browser.chrome = true;
	} else if (/safari/i.test(ua)) {
		browser.safari = true;
	}
	return browser;
}
