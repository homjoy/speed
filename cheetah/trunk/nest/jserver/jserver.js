var http = require('http'),
    url = require('url'),
    fs = require('fs'),
	cprocess = require('child_process'),
    cluster = require('cluster')

var wrapJS = require('./commonJS.js').wrapJS	
var numCPUs = require('../server/config/etc.json').cpuNums || require('os').cpus().length;
var jsModDep = require('./config/jsMod.json');
var cache = {},deponCache = {}, outputed = {};

//var STATICROOT = '/usr/local/www/html/test/';
var config = require('./config/service.json');
var JSTATICROOT = config.root + config.jspath;
var STATICROOT = config.root;
var RESEXPIRE = config.max_time;
var pic_url = config.pic_url || 'http://i.meilishuo.net/css'

var less = config.less ? require(config.less) : null
var isDebug = !!config.isDebug 

function onErr (filepath ,res){
   res.writeHead( 404 ,{'Content-Type' : 'text/plain'}); 
   res.write( filepath.replace(JSTATICROOT,'').replace(STATICROOT,'') + ' is lost');
   res.end();
   console.log(filepath,' is lost')

    }

function commonJS(filepath , cbk){
	wrapJS(filepath,cbk)
	/*
	cprocess.exec('node commonJS.js '+filepath ,
		{encoding :'utf8',maxBuffer:1024*1024*10} ,
		function(error, stdout, stderr){
			if (error !== null) {
			  console.log('exec error: ' + error)
			}			
			cbk && cbk(stdout)
		}) 
	*/
	}

function replacePicurl(con){
	return con.replace(new RegExp(pic_url,'g'), '/css_https/').replace(/\/\//g,'/')
	}
/*
*load js file from cache or io

*/
function loadFile(filepath , res , onReady , fileType ,req) {
    if ( !fileType) filepath = filepath + '.js';
        
    filepath = (fileType?STATICROOT : JSTATICROOT) + filepath.replace(/\.\.\//g,'');
	var inHttps = req && req.headers.encrypted && 'css' == fileType 
	if (less && 'css' == fileType){
		filepath = filepath.replace('/css/', '/less/').replace('.css','.less')
		fileType = 'less'
	}
	var fileCacheKey = filepath
	if (inHttps) fileCacheKey += '-https'
    if (cache.hasOwnProperty(fileCacheKey) ){

        res.write(cache[fileCacheKey] ) //,'binary');
        onReady();
        return;
        }
		
		
    fs.exists ( filepath , function (exists) {
        if (!exists ) {
            onErr (filepath ,res);
            return;
        }
		function getFileCon(file , noReplace){

			if (inHttps && !noReplace) file = replacePicurl(file)
            if (!isDebug) cache[fileCacheKey] = file 
            res.write( file ) //, 'binary');
            onReady();
			}
		if (!fileType || 'js' == fileType) return  commonJS(filepath , function(con){
			getFileCon(con + ';', true)
			})

        fs.readFile (filepath , 'utf8' , function (err ,file) {
            if (err) {
                onErr (filepath ,res);
                return;
            }
			if (less &&  'less' == fileType){
				var parser = new(less.Parser)({
					paths: [filepath.substr(0 , filepath.lastIndexOf('/'))]
					})
				parser.parse(file , function(err ,tree){
					file = tree.toCSS({ compress: true })
					getFileCon(file )
					})
			}else	getFileCon(file + ';')
			
            });
        });
    }
 /*
 *load a,b in  sequence ,
 * a+b in  parallel
 */   
function loadFileSeq(fileBlocks ,res , loadedList){
    var timer = setTimeout ( function() {
        res.end();
        } , RESEXPIRE);
	
    var onAllReady = function(){
           clearTimeout(timer);
           res.end();
        }
	/*
    fileBlocks = fileBlocks.split(',');
    var fsnum = fileBlocks.length;

    var onSecFinish =function(){
         if ( fileBlocks.length ) {
             loadFileParal (fileBlocks.shift() , res , onSecFinish , loadedList);   
          }else{
              onAllReady();
              } 
        }
    loadFileParal (fileBlocks.shift() , res , onSecFinish , loadedList);
	*/
	fileBlocks = fileBlocks.split('=')
    loadFileParal (fileBlocks[0] , res , onAllReady , loadedList , fileBlocks[1]||'');
        
    
    }
function loadFileParal(fileBlocks , res , onReady,loadedList , loadedBlocks) {
    if (!fileBlocks) return;
	var deponKey = fileBlocks + '=' + loadedBlocks
	var toLoad = deponCache[deponKey] || []
	var blocks = fileBlocks.split('+')
	

	if (toLoad.length == 0 ){
		if (loadedBlocks){
			var excluedKey = loadedBlocks+'='
			if (! deponCache[excluedKey]) loadFileParal(loadedBlocks,null,null , [],'')
			loadedList = loadedList.concat(deponCache[excluedKey])
			}
	
		blocks.forEach(function(mod) {
	///		if (putInList(loadedList , mod )) toLoad.push(mod);
			var depon = jsModDep[mod] || [];
			depon.forEach(function(dep){
				if (putInList(loadedList , dep )) toLoad.unshift(dep);
				});
			});
		deponCache[deponKey] = toLoad;
	}
	if (!res) return
	
    var toLoadNum = toLoad.length
		,blockNum = blocks.length
		,blockCache = {} 
			

    function onDepFinish(){
        if (--toLoadNum <= 0 ){
			blocks.forEach(function(mod){
				 if (!putInList(loadedList , mod )) {
					 blockNum--
					 return
				 }

				 loadFile (mod ,
					 {
					 'end' : function(){
						 res.writeHead( 404 ,{'Content-Type' : 'text/plain'})
						 res.write(blockCache[mod])
						 res.end()
						 },
					 'writeHead':function(){},
					 'write': function(context){
						if (mod in blockCache)
							blockCache[mod] += context
						else
							blockCache[mod] = context
					 }}
				 , onSecFinish);
				})
			} 
        }
	function onSecFinish(){
		if ( --blockNum <= 0){
			blocks.forEach(function(mod){
				if (mod in blockCache)
					res.write(blockCache[mod]) //, 'binary')
				})
			delete blockCache
			onReady()
			}
		}
	

	if (toLoadNum ){
		toLoad.forEach(function(filePath){
			 loadFile (filePath , res , onDepFinish)
			});
	} else{
		onDepFinish()
	}

    
    }    
function putInList(list , item){
	if (list.indexOf(item) == -1) {
		list.push(item);
		return true;
	}else {
		return false;
		}
	
	}
/*
*combo require files 
*/
function onRequest(req , res){
	var lastModified = outputed[req.url] || (new Date).toUTCString();
	var now = new Date;
	var expires = new Date(now.getFullYear() , now.getMonth() , now.getDate()+30);
	if (!isDebug && lastModified == req.headers['if-modified-since']){
		//res.writeHead(304, "Not Modified");
		res.writeHead(304, {"Expires" : expires.toUTCString() });
		res.end();
	}else{
		var statfs = url.parse(req.url).pathname;
		var filetype;
		if ('~' == statfs[1] ){ statfs = statfs.substr(2 ); }
		else filetype = getFileype(statfs);
		var contentType = 'text/plain'
		if ('css' == filetype) contentType = 'text/css'
		else if ('js' == filetype || !filetype) contentType = 'application/javascript'
		
		res.writeHeader(200 ,{
							'Content-Type' :  contentType +';charset=utf-8',
							"Last-Modified" : lastModified,
							"Expires" : expires.toUTCString()
							}); 
		switch (filetype){
			case 'ico':
			case 'css' :
			case 'less' :
			case 'js':
				var timer = setTimeout ( function() {
					res.end();
				} , RESEXPIRE);
				loadFile(statfs , res , function(){
					clearTimeout(timer);
					res.end();	
					},filetype, req); 
				break;
			default:
				loadFileSeq (statfs , res ,[]);

			}
		
		outputed[req.url] = lastModified;
		}

    }
function getFileype(url){
	var pos = url.lastIndexOf('.');
	if (!pos ) return;
	return url.substr(pos +1);
	
	}
if (cluster.isMaster ){
    while ( numCPUs--){    
        cluster.fork();
        };
    cluster.on('exit' , function(worker) {
        cluster.fork();
        });
    
 }else {
	var arguments = process.argv.splice(2);
	var onPort = isNaN(arguments[0]-0) ? (config.onPort || 8080) : arguments[0];
    http.createServer(onRequest).listen(onPort );
   //http://nodemanual.org/0.6.10/nodejs_ref_guide/cluster.html
   //process.send({ args: arguments });
   // work = cluster.fork();
   //work.on('message' ,...)
  }
fs.createWriteStream("config/pids", {
flags: "a",
encoding: "utf-8",
mode: 0666
}).write(process.pid + "\n");
