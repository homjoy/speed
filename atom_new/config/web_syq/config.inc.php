<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

define("API_HOST", 'http://api.speed.meilishuo.com');

defined('UPLOAD_PATH') || define("UPLOAD_PATH", '/home/work/uploads/');
defined('UPLOAD_DOMAIN') || define("UPLOAD_DOMAIN", 'http://uploads.speed.meilishuo.com/');

defined('BEANSDB_DOMAIN') || define("BEANSDB_DOMAIN", 'http://172.16.0.20/');
defined('BEANSDB_HOST') || define("BEANSDB_HOST", 'http://172.16.0.199:8080/pic/commupload');
