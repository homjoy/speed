#!/bin/bash 
rf=$(pwd)'/../'

script_path=`dirname $(pwd)`
script_path=${script_path##*/}

stopService() {
	echo 'stop service'	
	if [ ! -f $rf'server/config/pids' ]; then
		for proc in `ps -ef | grep node | grep index.js | awk '{print $2}'`; do
			kill   $proc ; done
	else
		cat $rf'server/config/pids' | while read line; do
			#echo 'kill '$line ;
			kill $line
		done
		echo '' > $rf'server/config/pids'
	fi


	echo 'stop jserver'
	if [ ! -f $rf'jserver/config/pids' ]; then
		for proc in `ps -ef | grep node | grep jserver.js | awk '{print $2}'`; do
			kill   $proc ; done
	else
		cat $rf'jserver/config/pids' | while read line; do
			#echo 'kill '$line ;
			kill $line
		done
		echo '' > $rf'jserver/config/pids'
	fi 

	}
stopAllService() {
    echo 'stop all web service'
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


	server_logf='/tmp/log/'$script_path'-server/'` date +%Y/%m/` 
	server_log=$server_logf`date +%d`'.log'
	jserver_logf='/tmp/log/'$script_path'-jserver/'` date +%Y/%m/` 
	jserver_log=$jserver_logf`date +%d`'.log'
	echo $server_log > './config/service_log_name.txt'
	echo 'SERVICE START AT '` date +%Y/%m/%d-%T` >> $server_log
	echo 'SERVICE START AT '` date +%Y/%m/%d-%T` >> $jserver_log
	mkdir -p $server_logf
	mkdir -p $jserver_logf
	echo 'web service start , logfile:'$server_log	
	cd $rf'server/' && nohup node index.js >> $server_log 2>&1 & 
	echo 'static service start , logfile:'$jserver_log	
	cd $rf'jserver/' && nohup node jserver.js >> $jserver_log 2>&1 &

}
clearTmp(){
	rm -r ../server/*.est
	rm -r /tmp/$script_path/*.est
	node config.js clearTmp
	}
minJS(){
	node min2.js	
	}
minCSS(){
	node mkcss.js
	echo 'css compiled'

	}
if [ $# -eq 0 ];then
	echo "you should pass args start|restart|stop|stopAll|restartS|restartJ|min|normal|clear|mincss|minjs"	
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
			#minCSS
			minJS 
			minCSS
			node config.js min
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
		"stopAll") 
			stopAllService
			;;
		"start") 
			startService
			;;
		"restart") 
			stopService
			node config.js reversion
			clearTmp
			startService
			;;
	esac
fi	

