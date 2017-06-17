var path = require('path')
var cPath = require ('./config/path.json')

exports.path = cPath;
    

exports.site = require ('./config/site.json'); 

exports.etc = require ('./config/etc.json') 
exports.api = require ('./config/api.json'); ;
exports.virtualHost = require('./config/virtual_host.json'); 


exports.setAbsPath = function (webRoot) {
	webRoot += '/';
	for (var p in cPath){
		if ('appPath' == p || 'views' == p || 'model' == p || 'controller' == p){
			continue
			}
		if (p != 'webRoot' && cPath[p][0] != '/') {
			cPath[p] = webRoot + cPath[p];
			}
		}
	if (cPath.appPath) cPath.appPath = path.resolve(cPath.appPath) + '/' 
	else cPath.appPath = ''
	cPath.webRoot = webRoot;
	
}
