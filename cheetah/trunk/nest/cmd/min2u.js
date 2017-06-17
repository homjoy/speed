var settings = require('./config/settings.json');
var orginal = '../web/script-ss',
    modFileFolders = settings.modFileFolders,
    mini = '../web/script-min';
var path = require('path'),
	fs = require('fs'),
	uglify = require(settings.uglifyjs);
var nodepencies = settings.nodepencies ;
var modDepMap = {};
var mapFile = '../jserver/config/jsMod.json';
function mkdirp(p){
	p = p.split('/');
	var pathnow = '';
	p.map(function(pi){
		pathnow += pi + '/';
		if (!fs.existsSync(pathnow) ) {
			fs.mkdirSync(pathnow);
			}
		});
	
	}
function minify(filepath ){
	var minfile = mini + filepath.substr(orginal.length);
	var minpath = path.dirname(minfile);
	
	if (! fs.existsSync(minpath) )	mkdirp(minpath);
	var data = fs.readFileSync(filepath ,'utf8');
	getDepencies(filepath , data);
	data = uglify.minify(filepath).code;
	fs.writeFile(minfile , data);	

	
	}
function getDepencies(filepath , filetxt){
	var isJSmodFile = false;
	modFileFolders.map(function(mf){
		if (filepath.indexOf('/'+mf+'/') > -1) {
			//console.log(mf);
			isJSmodFile = true;
			}
		});
	if (!isJSmodFile) return;
	//console.log(filepath);
	var modname = filepath.substr(orginal.length + 1).replace('.js' , '');
	if (nodepencies.indexOf(modname) >-1) return; 
	global.moding = modname;
	if (filetxt.indexOf("\nconsole.log") > -1  || filetxt.indexOf("	console.log") > -1 ) {
		console.log('WARNING console.log founded! : %s  ' , modname );
		}
	try{
		require(filepath);
	}catch(e){
		console.log('ERROR!: %s %s ' , modname ,  e);
		
		}


	}
function genModConfig(){

	for (var mod in modDepMap){
		modDepMap[mod] = allDepOn(modDepMap[mod]) ;
		}
	//console.log(modDepMap);
	var map = JSON.stringify(modDepMap);
	fs.writeFileSync(mapFile ,  map);	
	}

function allDepOn(deps){
	if (!deps || deps.length == 0) return null;
	if ('string' == typeof deps) deps = [deps];
	deps.map(function(dep){
		if (modDepMap[dep]) {
			 var deepD = allDepOn( modDepMap[dep] ) ;	
			 if (deepD) {
				 deepD.map(function(dp){
					 if (deps.indexOf(dp) == -1) deps.push(dp);
					 });
				 }
			}
			
		});
	return deps;
	}	
function minjs(folder){
	var files = fs.readdirSync(folder);
	files.forEach(function(file){
		if ('.' == file[0]) return;
		var filepath = folder + '/' + file;
		var stat = fs.statSync(filepath);

		if (stat.isFile() ){
			minify(filepath );
		
		}else if(stat.isDirectory()){
			minjs(filepath);		
			}
	});	
}	
function pushDep(modName , depencies){
	if (!modDepMap[modName]) modDepMap[modName] = [];
	modDepMap[modName] = modDepMap[modName].concat(depencies);
	
	}
global.moding;
global.fml = {
	'define' : function(modName , depencies ,callback){
		//pushDep(modName ,  depencies);
		if (arguments.length < 3 && 'string' != modName){
			depencies = modName;
			}
		pushDep(global.moding, depencies);
		},
	'use' : function(modName){
		pushDep( global.moding, modName );
		}
	}
minjs(orginal);
genModConfig();	
