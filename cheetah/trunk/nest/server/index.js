var server = require("./server.js"),
	router = require("./router.js"),
	extend = require("./extend.js"),
	fs = require("fs");


server.start(router.route , config.etc.onPort || 8888);

fs.createWriteStream("config/pids", {
flags: "a",
encoding: "utf-8",
mode: 0666
}).write(process.pid + "\n");

if (config.etc.watchingTpl){
	var watcher = require("./lib/watchNode.js");
	var absDir = __dirname.replace(/\\/g,'/');
	watcher.takeCare([config.path.appPath , absDir + "/base" ,absDir + "/lib"] );
}
