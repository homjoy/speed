var	jserver_config = '../jserver/config/service.json',
	server_api_conf = '../server/config/api.json',
	server_etc_conf = '../server/config/etc.json',
	server_site_conf = '../server/config/site.json',
	server_path_conf = '../server/config/path.json',
	settings_conf = './config/settings.json'
var fs = require('fs'),
	path = require('path');
function save( file , conf){
	conf = JSON.stringify(conf).replace(/,"/g,',\n"');
	//console.log(file, conf);
	fs.writeFileSync(file, conf );
	
	}
function upConfig(bemin, newVer){
	var jconf = require(jserver_config);
	//jconf.root = path.dirname(__dirname) + '/web/';
	jconf.jspath =  (bemin? 'script-min/' : 'script-ss/');
	save(jserver_config, jconf );

	var etc = require(server_etc_conf);
	etc.watchingTpl = !bemin ;
	etc.fussTpl = bemin;
	save(server_etc_conf, etc );

	var settings = require(settings_conf)
	var version = newVer || settings.pubVerson || 1
	settings.pubVerson = parseFloat(version) + 1

	save(settings_conf, settings );
	
	var site = require(server_site_conf);
	site.JS_Defer = bemin;
	//site.SVERSION = Math.round(new Date/60000);
	site.SVERSION = version;

	site.PUBDAY = getNowDate() 
	site.SCRIPT_BASE = bemin ? 'script-min/':'script-ss/';
	save(server_site_conf, site );
	console.log('config updated');
		

		
	}

function upVersion (v, newVer){
	var site = require(server_site_conf);
	site.SVERSION = newVer ? parseFloat(newVer) : Math.round((parseFloat(site.SVERSION) + v) * 10000 ) / 10000;
	//site.PUBDAY = getNowDate() 
	save(server_site_conf, site);
}

function getNowDate(){
	var st = new Date
	function leadZero(t){
		if (t<10) t = '0'+ t
		return t
		}
	return ("" + st.getYear()).slice(1) 
			+ leadZero(st.getMonth())
			+ leadZero(st.getDate())
			+ leadZero(st.getHours())
			+ leadZero(st.getMinutes())
	}


var arguments = process.argv.splice(2);

if ('clearTmp' == arguments[0]) {
	var pathConf = require(server_path_conf)
	if (!pathConf.compiledViews) return
	var exec = require('child_process').exec
	exec('rm '+ pathConf.compiledViews + '/*.est')
	console.log('rm '+ pathConf.compiledViews + '/*.est')
}else if ('reversion' == arguments[0]) {
	upVersion(0.0001, arguments[1])
}else{
	var bemin = arguments[0] == 'min';
	upConfig(bemin, arguments[1]);
}
