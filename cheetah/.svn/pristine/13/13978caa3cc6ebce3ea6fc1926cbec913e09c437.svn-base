var fs = require("fs"),
    url = require('url'),
    querystring = require('querystring');
var config = require('./config.js'); 

config.setAbsPath( __dirname.replace(/\\/g,'/') );
global.config = config;
require(config.path.base + 'protoExt.js');
global.base = require(config.path.base + 'base.js');
global.controller = require(config.path.base + 'controller.js');
global.mModel = require(config.path.base + 'model.js');


var formidable = config.etc.uploadTmp ? require('formidable') : null;
var lookuped = {};

function route(request ,response ) {
    //console.log('%s / %s' ,request.headers.host  , request.url );
	try{
		var reqUrl  = url.parse('http://' + request.headers.host  + request.url , true)
	}catch(err){
		console.log ('Route Parse Error:' , request.url)
		response.writeHead(500 , {'Content-Type' : 'text/plain'});        
	    response.end('url is wrong');	
		return	
		}


    var reqPath = reqUrl.pathname.substr(1)
	request.__request_time = new Date
    request.__get = {};
	for (var k in reqUrl.query){
		request.__get[k.replace(/<|>|%/g,'')] = reqUrl.query[k]
	}
    request.__post = {}

    //对相同域名不同模块的转向
    var virtualHostname = config.virtualHost[reqUrl.hostname]

	var hostPath = reqUrl.hostname ?  virtualHostname : '';
    if (!reqPath){
			reqPath = config.etc.defaultAction
		}
	if (hostPath) hostPath += '/'

	var modUriSeg = reqPath.replace(/\/+/g,'/').split('/');
	/*
	url 格式 [/ 地址/...]模块文件名/方法名/[参数] 
	3 mod/fn/param
	2 mod/../param
	1 mod
	*/
	if (modUriSeg.length < 3){
		modUriSeg.splice(1,0,'index');
	}
	var mods = modUriSeg.splice(-3);
	//console.log(mods)

	var modPath = config.path.appPath  + hostPath + 'controller/'  + (modUriSeg.length ? modUriSeg.join('/')+'/' : '');
	delete modUriSeg ;
	

	var modName = mods[0] + '.js';
	var modFilePath = modPath + modName;
	//console.log(modFilePath)
    if (!lookuped[modFilePath] && !fs.existsSync( modFilePath)){
			base.accessLog(404 , request  )
			response.writeHead(404 , {'Content-Type' : 'text/plain'});        
		    response.end('404');
    }else{
			lookuped[modFilePath] = true;
		    var mod = require ( modFilePath);
		    var fn = mods[1];
		    var param = mods.length == 3 ? mods[2] : null;
		    if (param) {
				try {
					param = decodeURIComponent(param);
				} catch(err) {
					console.log(err, param);
				}
			}
			//console.log(mod , fn);
		    if ('function' != typeof mod[fn] &&
		        'function' == typeof mod['__create']){
			    mod = mod.__create(modName , hostPath);
			    }
			param = (!param && 'object' === typeof mod[fn]) ? 'index' : param;
		    if ('function' == typeof mod[fn] || ('object' === typeof mod[fn] && 'function' === typeof mod[fn][param])){
				//base.accessLog(200 , request  )
			    exeAppScript(hostPath ,request , response , mod ,fn , param);	
		    }else if('function' == typeof mod['__call']){
			    exeAppScript(hostPath ,request , response , mod ,fn , param ,true);	
		    }else {
				base.accessLog(404 , request, modFilePath + ' not assign'  )
				response.writeHead(404 , {'Content-Type' : 'text/plain'});        
			    response.end('not assign.');	

		    }
            
     }
}


function exeAppScript(hostPath ,request , response , mod , fn , param, magicCall){
	
	 function toExe (){
		request.headers.XREF = request.url + ' ['+ new Date().getTime().toString(32) +'.'+ (Math.random()*1000000|0).toString(32) +']'
	    mod.setRnR && mod.setRnR(request , response ,{"hostPath" : hostPath})
        //console.log(mod[fn]);
		if (false === mod.checkdIllegal(hostPath) ) {
			base.accessLog(421 , request,  'illegal request aborted'  )
			return;
		}
		try {
			magicCall ?   mod.__call(fn,param) : (('object' === typeof mod[fn] && 'function' === typeof mod[fn][param]) ? mod[fn][param].call(mod) : mod[fn](param));
		}catch (err){
			base.dataErrLog(500 , request,  'Fatal error :'+ err  )
			response.writeHead(500)
			response.end( 'Fatal error :' + err)
			
			}
        //mod[fn].call(mod , param);
	}
  	if ('POST' == request.method){
        if (formidable) {
            var form = new formidable.IncomingForm(),
                files = {},
                fields = {};

            form.uploadDir = config.etc.uploadTmp;
            form.on('field', function(field, value) {
                    fields[field] = value;
                })
                .on('file', function(field, file) {
                    files[field] = file;    
                })
                .on('end' ,function(){
  			        request.__post = fields;
                    request.__files = files;
		    	    toExe();
                    
                });
            form.parse(request);
        } else {
		    var data = '';

		    request.addListener('data' , function(chunk){
                    data += chunk;
                    if (data.length > 1e6) request.connection.destroy();
                })
		        .addListener('end' ,function(){
			        data = querystring.parse(data);

  			        request.__post = data;
			        toExe();
			    });
        }
	}else{
		toExe();
	}

}

exports.route = route;
