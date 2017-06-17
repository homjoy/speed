var http = require("http");
var cluster = require('cluster');
var numCPUs = require('./config/etc.json').cpuNums || require('os').cpus().length;

function start(route , port) {
  function onRequest(request, response) {

    route(request ,response);
   
  }
  if (cluster.isMaster) {
     for (var i = 0; i < numCPUs; i++) {
          cluster.fork();
         }

      cluster.on('death', function(worker) {
          console.log('worker ' + worker.pid + ' died');
          cluster.fork();
         });
      cluster.on('exit', function(worker) {
			var st = new Date
			st = st.getFullYear()+ '-'+ (st.getMonth()+1)+ '-'+st.getDate()+ ' '+st.toLocaleTimeString()
			console.log('worker ' + worker.process.pid + ' died at:',st);
			cluster.fork();
         });
  } else {

     http.createServer(onRequest).listen(port || 80 ,80000)
     console.log("Server has started.")
  }
}

exports.start = start;
