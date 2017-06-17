<?php
namespace Monster;

$start = microtime(true);

define('BASE_PATH',__DIR__ .'/../../../../');
define('FRAME_PATH', BASE_PATH. '/frame/frame');
define('LIB_PATH',  BASE_PATH. '/frame/libs');
define('VENDOR_PATH', BASE_PATH. '/frame/vendor');
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
$app->scripts_namespace = '\\Admin\\Scripts\\';

//url模式:camelCase/underline
// $app->url_mode = 'underline';

$app->singleton('request', function($c) {
    return new \Libs\Http\BasicScriptsRequest();
});
//response
$app->singleton('response', function($c) {
    return new \Libs\Http\BasicScriptsResponse();
});
//router
$app->singleton('router', function($c) {
    return new \Libs\Router\BasicScriptsRouter($c);
});

$app->singleton('view', function($c) {
    return new \Libs\View\Json($c);
});

//声明logWriter
$app->singleton('logWriter', function($c) {
    return new \Libs\Log\BasicLogWriter();
});

//config filter 
$app->config->db = function() {
    return \Admin\Config\ConfigOffline::instance()->db();

    // return \Admin\Config\Web\MySQL::config();

};
$app->config->redis = function() {

    return \Admin\Config\ConfigOffline::instance()->redis();

    // return \Admin\Config\Web\Redis::config();

};
$app->config->memcache = function() {
    return \Admin\Config\ConfigOffline::instance()->memcache();
//    return \Admin\Config\Web\Memcache::config();
};

$app->run();

$spend = microtime(true) - $start;

