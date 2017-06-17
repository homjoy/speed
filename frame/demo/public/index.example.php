<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame');
define('LIB_PATH', __DIR__ . '/../../libs');
define('VENDOR_PATH', __DIR__ . '/../../demo');

require(FRAME_PATH . '/Autoloader.class.php');
//or
//require(FRAME_PATH . '/PsrAutoloader.class.php');
require(FRAME_PATH . '/Application.class.php');

$root_path_setting = array(
	'Frame' => FRAME_PATH,
	'Libs' => LIB_PATH,
    'default' => VENDOR_PATH,
);
$autoloader = Autoloader::register($root_path_setting);
//or
//$autoloader = PsrAutoloader::register($root_path_setting);

//get the app
$app = \Frame\Application::instance();

//注册module的namespace
$app->module_namespace = '\\Demo\\Modules\\';

$app->singleton('request', function($c) {
    return new \Libs\Http\BasicRequest(); 
});
//response
$app->singleton('response', function($c) {
    return new \Libs\Http\BasicResponse(); 
});
//router
$app->singleton('router', function($c) {
    return new \Libs\Router\BasicRouter($c);
});

$app->singleton('view', function($c) {
    return new \Libs\View\Json($c);
});

//声明logWriter
$app->singleton('logWriter', function($c) {
    return new \Libs\Log\BasicLogWriter();
});

//TODO config filter 
$app->config->db = function() {
    return \Demo\ConfigDemo::instance()->db();
};
$app->config->redis = function() {
    return \Demo\ConfigDemo::instance()->redis();
};

$app->run();

$spend = microtime(true) - $start;
