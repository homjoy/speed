#!/usr/bin/env node
var fs = require('fs')
    ,path = require('path')
	,child = require('child_process')

var less = require('./config/settings.json').less

if (!less) throw 'less is not config'
less = require(less)

function genCss(filepath){
	child.fork(__dirname+ '/less2css.js' ,[filepath] , {env:{child: true}})
		.on('exit' , function(code){
			//console.log('child process exited with code ' + code);
			})
	//console.log(filepath)
	}	

function walkLess(folder  ){
	var cls_folder = folder+'/'
	if (cls_folder.indexOf('/block/') >=0 ) return
	if (cls_folder.indexOf('/font/') >=0 ) return
	if (cls_folder.indexOf('/import/') >=0 ) return

	var files = fs.readdirSync(folder);
	files.map(function(file){
			if ('.' == file[0]) return;
			var filepath = folder + '/' + file;
			var stat = fs.statSync(filepath);
			if (stat.isFile() ){


				if ('atom.less' == file ) return
				if ('storeAtom.less' == file ) return
				if (file.indexOf('.import.less')>0 ) return
				if (file.indexOf('.atom.less')>0 ) return

				genCss(filepath )

			}else if(stat.isDirectory()){
				walkLess(filepath);
			}
	});
}


var arguments = process.argv.splice(2),
	scriptpwd = path.dirname(__filename);
	
var lessFolder ;
if ( arguments.length) {
	lessFolder =  arguments[0] ;
	if (lessFolder.indexOf('.less')){
		lessFolder = lessFolder.substr(0 , lessFolder.indexOf('/less/') +6);
		};
}else 
	lessFolder = path.resolve(require('../jserver/config/service.json').root , './less') 

if (lessFolder){
	lessFolder = path.resolve(lessFolder);
	//console.log(' compress ' + lessFolder);
	walkLess(lessFolder );	
	}
