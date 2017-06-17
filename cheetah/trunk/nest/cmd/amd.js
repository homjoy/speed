#!/usr/bin/env node
var orginal = '../web/script-ss'
var path = require('path')
	,fs = require('fs')

var commonJS = require('../jserver/commonJS.js')
var tag = commonJS.getTag()

function amd(filepath ){
	var js = fs.readFileSync (filepath , 'utf8')
	if (!js || js.indexOf(tag) !== 0  ) return
	if (js.indexOf('fml.define') > 0 ){
		global.moding = filepath
		try{
			require(filepath)
		}catch(err){
			console.log('ERROR!: %s %s ' , filepath ,  err)
		}	
	}else{
		var wrapJS = commonJS.wrapJS(filepath)
		if (wrapJS) {
			console.log('wrap' ,filepath)
			fs.writeFile(filepath ,wrapJS)
			}
		}
	}

function amdjs(folder){
	var stat = fs.statSync(folder)
	if (stat.isFile() ) return amd(folder)
	var files = fs.readdirSync(folder);
	files.forEach(function(file){
		if ('.' == file[0]) return;
		var filepath = folder + '/' + file
		amdjs(filepath)		
	});	
}	

global.fml = {
    'define' : function(modName , depencies ,fn){
		var js = fn.toString()
		js = js.slice( js.indexOf('{') + 1 , -1).trim()
		var wrapJS = commonJS.wrapJSC(modName ,tag + js)
		if (wrapJS){
			console.log('rewrap' ,global.moding)
			fs.writeFile(global.moding , wrapJS)
			}
        }
    }

var args = process.argv.splice(2)
amdjs(args[0] || orginal)
console.log('full finish')
