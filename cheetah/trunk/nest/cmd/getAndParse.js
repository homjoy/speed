#!/usr/bin/env node
var arguments = process.argv.splice(2);
var url = arguments[0];
if (!url) return '';
var sys = require('util');
var exec = require('child_process').exec;
try {
	function puts(error, stdout, stderr) { 
		try{
			text = JSON.parse(stdout);
			console.log(sys.inspect(text,false, 10));
		}catch(e){
			console.log(stdout);
			console.log('pare error' , e);
			
			}
		
		}
	exec("curl "+ url, puts);
}catch(e){
	console.log('curl error' , e);
}

