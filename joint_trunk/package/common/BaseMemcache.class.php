<?php
namespace Joint\Package\Common;

class BaseMemcache extends \Libs\Cache\Memcache{

	const PREFIX = 'Joint:';
	//超时时间(s)
	const TIMEOUT = 1800;
	//静态memcache
	static $instance;

}
