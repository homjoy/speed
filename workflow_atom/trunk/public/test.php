<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame/frame');
define('LIB_PATH', __DIR__ . '/../../frame/libs');
define('VENDOR_PATH', __DIR__ . '/../../frame/vendor');
define('APP_PATH', __DIR__ . '/../');

//加载第三方库
require_once(VENDOR_PATH.'/autoload.php');

require(FRAME_PATH . '/Autoloader.class.php');
$root_path_setting = array(
	'Frame' => FRAME_PATH,
	'Libs' => LIB_PATH,
    'default' => APP_PATH,
);
$autoloader = \Frame\Autoloader::register($root_path_setting);

$app = \Frame\Speed\Test\TestApplication::instance();
$app->setAppPath(APP_PATH);
$app->setNamespace('\\Atom\\Tests\\');
$app->config->remote = function() {
	return array(
		'atom' => array(
			'http://atom.dev.newspeed.meilishuo.com',
			'http://atom.dev.newspeed.meilishuo.com',
		)
	);
};
$app->runTest();