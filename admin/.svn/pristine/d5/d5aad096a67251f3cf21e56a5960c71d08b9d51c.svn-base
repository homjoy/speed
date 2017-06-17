<?php
namespace Admin;

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame/trunk/frame');
define('LIB_PATH', __DIR__ . '/../../frame/trunk/libs');
define('VENDOR_PATH', __DIR__ . '/../../frame/trunk/vendor');
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
$app->module_namespace = '\\Admin\\Modules\\';

//url模式:camelCase/underline
$app->url_mode = 'underline';

$app->singleton('request', function($c) {
    return new \Libs\Http\BasicRequest(); 
});
//response
$app->singleton('response', function($c) {
    return new \Frame\Speed\Http\HttpAdminResponse(); 
});
//router
$app->singleton('router', function($c) {
    return new \Frame\Speed\Router\BasicRouter($c);
});

$app->singleton('view', function($c) {
    return new \Frame\Speed\View\View($c);
});

//声明logWriter
$app->singleton('logWriter', function($c) {
    return new \Libs\Log\BasicLogWriter();
});

//config filter 
$app->config->db = function() {
    return \Admin\Config\ConfigOffline::instance()->db();
};
$app->config->redis = function() {
    return \Admin\Config\Web\Redis::config();
};
$app->config->memcache = function() {
    return \Admin\Config\Web\Memcache::config();
};
$app->config->remote = function() {
    return \Admin\Config\Web\Remote::config();
};

$app->run();

$spend = microtime(true) - $start;
