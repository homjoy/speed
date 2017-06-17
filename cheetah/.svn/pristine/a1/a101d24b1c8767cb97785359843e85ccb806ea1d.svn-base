#!/bin/bash 
if [ $USER != "work" ]
then
	echo "work only"
	exit 1
fi
rf=$(pwd)'/../'


stopService() {
	echo 'stop service'	
	for proc in `ps -ef | grep node | grep '\.js' | awk '{print $2}'`; do
		kill   $proc ; done
	if [ -f $rf'server/config/pids' ]; then  	
		rm -r $rf'server/config/pids'
	fi 
	if [ -f $rf'jserver/config/pids' ]; then  	
		rm -r $rf'jserver/config/pids'
	fi
	}
startService() {
	server_logf='/tmp/log/hornbill-server/'` date +%Y/%m/` 
	server_log=$server_logf`date +%d`'.log'
	jserver_logf='/tmp/log/hornbill-jserver/'` date +%Y/%m/` 
	jserver_log=$jserver_logf`date +%d`'.log'
	echo 'SERVICE START AT '` date +%Y/%m/%d-%T` >> $server_log
	echo 'SERVICE START AT '` date +%Y/%m/%d-%T` >> $jserver_log
	mkdir -p $server_logf
	mkdir -p $jserver_logf
	echo 'web service start , logfile:'$server_log	
	cd $rf'server/' && nohup node index.js >> $server_log &
	echo 'static service start , logfile:'$jserver_log	
	cd $rf'jserver/' && nohup node jserver.js >> $jserver_log &

}
clearTmp(){
	rm -r ../server/*.est
	rm -r /tmp/*.est
	}
minJS(){
	node min2.js	
	}
minCSS(){
	node mkcss.js
	echo 'css compiled'

	}
if [ $# -eq 0 ];then
	echo "you should pass args start|restart|stop|min|normal|clear|mincss"	
else
	case $1 in
		"normal")
			echo "normal"
			node config.js normal
			stopService
			clearTmp
			startService
			;;
		"min")
			minJS 
			node config.js min $2
			stopService
			clearTmp
			startService
			;;
		"bemin")
			echo "be min mode"
			node config.js min $2
			stopService
			clearTmp
			startService
			;;
		"minjs")
			minJS
			;;
		"mincss")
			minCSS
			;;
		"clear")
			clearTmp
			;;
		"stop") 
			stopService
			;;
		"start") 
			startService
			;;
		"restart") 
			node config.js reversion $2
			stopService
			clearTmp
			startService
			;;
	esac
fi	

