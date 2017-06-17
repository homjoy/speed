var orginal = '../web/script-ss',
    modFileFolders = ['ui','app' , 'page' ,'component','core'],
    mini = '../web/script-min';
var fs = require('fs'),
	path = require('path'),
	uglify = require("uglify-js/uglify-js.js");
var depencReg = /,([\w\W]+),/ ;
var modDepMap = {};
var mapFile = '../jserver/config/jsMod.json';
function mkdirp(p){
	p = p.split('/');
	var pathnow = '';
	p.map(function(pi){
		pathnow += pi + '/';
		if (!path.existsSync(pathnow) ) {
			fs.mkdirSync(pathnow);
			}
		});
	
	}
function minify(filepath ){
	var minfile = mini + filepath.substr(orginal.length);
	var minpath = path.dirname(minfile);
	
	if (! path.existsSync(minpath) )	mkdirp(minpath);
	var data = fs.readFileSync(filepath ,'utf8');
	getDepencies(filepath , data);
	data = uglify(data);
	fs.writeFile(minfile , data);	

	
	}
function getDepencies(filepath , filetxt){
	var isJSmodFile = false;
	modFileFolders.map(function(mf){
		if (filepath.indexOf('/'+mf+'/') > -1) {
			console.log(mf);
			isJSmodFile = true;
			}
		});
	if (!isJSmodFile) return;
	console.log(filepath);
	var modname = filepath.substr(orginal.length + 1).replace('.js' , '');
	var fmlMod = 'fml'
	if (modname == fmlMod) return;
	var pos = filetxt.indexOf('define'); 
	if (pos == -1 || filetxt.indexOf(fmlMod + '.use') > -1 ){
		//only use ,no define
		var dot_reg = /\'/g,
			dep_reg = /\.use *\(([^\(\)]+)/g,
			depm_reg = /\.use *\(([^\(\)]+)(?:\)|, * function)/;
		var usedmod = filetxt.match(dep_reg);
		var depencies = [];
		usedmod && usedmod.map(function(mod){
			mod = mod.match(depm_reg);
			mod = mod[1].trim().replace(dot_reg , '"');
			mod = JSON.parse(mod);
			if (typeof mod == 'string') mod = [mod];
			mod && mod.map(function(dep){
				if (depencies.indexOf(dep) ==-1) depencies.push(dep);
				
				});
			
			});

	}else {
		var depencies = filetxt.substring(filetxt.indexOf('(' , pos ) , filetxt.indexOf('function') );
		depencies = depencies.match(depencReg); 
		if (depencies ){ 
			depencies = depencies[1].trim().replace(/'/g , '"');
			try{
				depencies = JSON.parse(depencies);
			}catch(e){
				console.log('parse error');
				depencies = null;
				}
		}
	
	}
	//console.log(modname , depencies);

	modDepMap[modname] = depencies;
	//console.log(modname + ': ' + depencies);

	}
function genModConfig(){

	for (var mod in modDepMap){
		modDepMap[mod] = allDepOn(modDepMap[mod]) ;
		}
	console.log(modDepMap);
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
	files.map(function(file){
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


minjs(orginal);
genModConfig();	
