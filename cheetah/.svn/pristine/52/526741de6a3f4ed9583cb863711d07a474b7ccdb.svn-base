var cluster = require('cluster'),
	http = require('http'),
	fs = require("fs"),
	url = require('url'),
	querystring = require('querystring')
	,exec = require('child_process').exec
	,spawn = require('child_process').spawn





function onRequest(req, res) {
	var reqUrl = req.url	
	if ('/' == reqUrl || 'favicon.ico' == reqUrl) {
		return res.end('Im debugger')
		}

	res.writeHead(200 ,{'Content-Type': 'text/plain','Cache-Control': 'no-cache,no-store'})
	reqUrl = reqUrl.slice(1)
	console.log(reqUrl)

	var serverLogs = {
		'rw' : 'rongwei/rw' 
		,'devlab3' : 'work/devlab3/hornbill'
		,'devlab2' : 'work/devlab2/hornbill'
		,'devlab1' : 'work/devlab1/hornbill'
		,'newlab' : 'work/newlab/hornbill'
		}
	var renderErrLogs = {

		}
	if (reqUrl in serverLogs) {
		fs.readFile('/home/'+ serverLogs[reqUrl]  +'/cmd/config/service_log_name.txt','utf-8',function(err, logFile){
			if (err) return res.end('Log File error:'+err)
			tailLog(logFile)
			})
	}else if (reqUrl in renderErrLogs){
		tailLog(renderErrLogs[reqUrl])
	}else {
		return res.end('Param Error')
	}
	req.on('close' ,function(){
		console.log('aborted')
		req.tail.kill('SIGHUP')
		})

	function tailLog(logFile) {
	//	var logFile = '/tmp/log/rw-server/2014/01/27.log'
		//console.log(logFile)	
		logFile = logFile.trim()
		if (!logFile) return res.end('Log File lost')
		var tail  = req.tail = spawn('tail' , ['-f' , logFile])
		tail.stdout.on('data' , function(data){
			var line = data.toString('utf-8')
			console.log(line)	
			res.write(line)
			})
		/*
		fs.watchFile(logFile, function (curr, prev) {
			console.log('the current mtime is: ' + curr.mtime);
			console.log('the previous mtime was: ' + prev.mtime);
			});
		*/
		}




}


var arguments = process.argv.splice(2)
http.createServer(onRequest).listen(arguments[0] || 2015)
