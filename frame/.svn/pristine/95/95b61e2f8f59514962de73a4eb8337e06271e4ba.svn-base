<?php

$verbose = 0; //静默执行，不输出任何内容
foreach($argv as $arg){
    if(preg_match('/^-(?<verbose>v{1,4})$/',$arg,$matches) > 0){
        $verbose = strlen((string)$matches['verbose']);
    }else{
        $verbose = 0;
    }
}

$logger = new \Speed\Logger\ConsoleLogger($verbose);

//文件日志
//第一个参数为文件日志的路径，默认为/tmp/speed_logger
$logger = new \Speed\Logger\FileLogger();
$logger->emergency("这是一条emergency");
$logger->error("这是一条error");
$logger->alert("这是一条alert");
$logger->warning("这是一条warning");
$logger->notice("这是一条notice");
$logger->info("这是一条info");
$logger->debug("这是一条debug，可以在这里dump 详细数据等");