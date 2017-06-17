#!/usr/bin/env node
var settings = require('./config/settings.json')
var path = require('path'),
	fs = require('fs'),
	uglify = require(settings.uglify);
var orginal = path.resolve(require('../jserver/config/service.json').root , './script-ss')
    modFileFolders = settings.modFileFolders,
    mini =  path.resolve(require('../jserver/config/service.json').root , './script-min') 

var commonJS = require('../jserver/commonJS.js')

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
var uglifyMin = uglify.minify? function(code){
		 return uglify.minify(code , {fromString:true}).code
	    }: uglify
function minify(filepath ){
	var minfile = mini + filepath.substr(orginal.length);
	var minpath = path.dirname(minfile);
	if (! fs.existsSync(minpath) )	mkdirp(minpath);
	//var data = fs.readFileSync(filepath ,'utf8');
	var wrapJS = commonJS.getAll(filepath)
	if (wrapJS && wrapJS.modname && wrapJS.depend){
		if (wrapJS.depend.length) pushDep( wrapJS.modname , wrapJS.depend)
	}else{
		getDepencies(filepath )
	}
	try {
		var data = uglifyMin(wrapJS.content);
		fs.writeFile(minfile , data);	
	}catch(err){
		console.log(filepath , err)
		fs.writeFile(minfile ,'console.log("'+minfile+' is parse error")')
	}
	}

function getDepencies(filepath ){
	/*
	var isJSmodFile = false;
	modFileFolders.map(function(mf){
		if (filepath.indexOf('/'+mf+'/') > -1) {
			//console.log(mf);
			isJSmodFile = true;
			}
		});
	if (!isJSmodFile) return;
	*/
	//console.log(filepath);
	var modname = filepath.substr(orginal.length + 1).replace('.js' , '');
	if (nodepencies.indexOf(modname) >-1) return; 
	global.moding = modname;

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
function pushDep(modName , depencies){
	if (!modName) return 
	if (!modDepMap[modName]) modDepMap[modName] = [];
	modDepMap[modName] = modDepMap[modName].concat(depencies);
	
	}
global.moding;
global.fml = {
	'define' : function(modName , depencies ,callback){
		//pushDep(modName ,  depencies);
		if (global.moding != modName){
			console.log('!Error:' + modName+ ' should be '+global.moding)
			}
		if (arguments.length < 3 && 'string' != modName){
			depencies = modName;
			}
		pushDep(global.moding, depencies);
		},
	'use' : function(modName){
		pushDep( global.moding, modName );
		}
	}
var args = process.argv.splice(2)
if (args[0]) {
	minify(path.relative(__dirname , args[0]) )
	console.log('min finish')
}else{
	minjs(orginal);
	genModConfig();	
}
