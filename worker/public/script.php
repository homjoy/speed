<?php
namespace Worker;

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
$start = microtime(true);

define('FRAME_PATH', realpath(__DIR__ . '/../../frame/frame'));
define('LIB_PATH', realpath(__DIR__ . '/../../frame/libs'));
define('VENDOR_PATH', __DIR__ . '/../../frame/vendor');
define('APP_PATH', realpath(__DIR__ . '/../'));
define('TEMP_PATH', realpath(__DIR__ . '/../temp'));
define('COMPOSER_VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

//加载第三方库
require_once(VENDOR_PATH.'/autoload.php');
require_once(COMPOSER_VENDOR_PATH . '/autoload.php');
require_once(FRAME_PATH . '/Autoloader.class.php');
require_once(FRAME_PATH . '/Application.class.php');

$autoLoader = \Frame\Autoloader::register(array(
    'Frame' => FRAME_PATH,
    'Libs' => LIB_PATH,
    'default' => APP_PATH,
));

//get the app
$app = \Frame\Application::instance();

//注册module的namespace
$app->scripts_namespace = '\\Worker\\Scripts\\';

$app->singleton('request', function($c) {
    return new \Libs\Http\BasicScriptsRequest(); 
});

//router
$app->singleton('router', function($c) {
    return new \Libs\Router\BasicScriptsRouter($c);
});

$app->singleton('response', function($c) {
    return new \Libs\Http\BasicScriptsResponse(); 
});

//声明logWriter
$app->singleton('logWriter', function($c) {
    return new \Libs\Log\BasicLogWriter();
});

$app->singleton('view', function($c) {
    return new \Libs\View\None($c);
});


//TODO config filter
$app->config->db = function() {
    return \Worker\Config\Web\MySQL::config();
};
$app->config->redis = function() {
    return \Worker\Config\Web\Redis::config();
};

$app->config->remote = function() {
    return \Worker\Config\Web\Remote::config();
};

$app->config->smsApi = function() {
    $config = \Worker\Config\Web\Remote::config();
    return $config['sms'];
};
$app->config->mailApi = function() {
    $config = \Worker\Config\Web\Remote::config();
    return $config['mail'];
};
$app->config->imApi = function() {
    $config = \Worker\Config\Web\Remote::config();
    return $config['im'];
};

$app->run();
