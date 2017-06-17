<?php
namespace Atom;

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame/frame');
define('LIB_PATH', __DIR__ . '/../../frame/libs');
define('VENDOR_PATH', __DIR__ . '/../../frame/vendor');
define('APP_PATH', __DIR__ . '/../');

//加载配置
require(APP_PATH . '/config/web/config.inc.php');

//加载第三方库
require_once(VENDOR_PATH.'/autoload.php');

require(FRAME_PATH . '/Autoloader.class.php');
//or
//require(FRAME_PATH . '/PsrAutoloader.class.php');
require(FRAME_PATH . '/Application.class.php');

$root_path_setting = array(
	'Frame' => FRAME_PATH,
	'Libs' => LIB_PATH,
    'default' => APP_PATH,
);
$autoloader = \Frame\Autoloader::register($root_path_setting);
//or
//$autoloader = PsrAutoloader::register($root_path_setting);

//get the app
$app = \Frame\Application::instance();

//注册module的namespace
$app->scripts_namespace = '\\Atom\\Scripts\\';

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

$app->config->db = function() {
    return \Atom\Config\Web\MySQL::config();
};
$app->config->redis = function() {
    return \Atom\Config\Web\Redis::config();
};
$app->config->memcache = function() {
    return \Atom\Config\Web\Memcache::config();
};
$app->config->remote = function() {
    return \Atom\Config\Web\Remote::config();
};


$app->run();
