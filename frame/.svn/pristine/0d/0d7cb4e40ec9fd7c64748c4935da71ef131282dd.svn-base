<?php

namespace Frame\Connect;

class Redis {

	/**
	 * Holds initialized Redis connections.
	 *
	 * @var array
	 */
	protected static $connections = array();
	protected static $timeout = 2;

	/**
	 * By default the prefix of Redis key is the same as class name. But it
	 * can be specified manually.
	 *
	 * @var string
	 */
	protected static $prefix = NULL;

	protected static $configfile = 'Redis';

	/**
	 * the server_id of the Redis Cluster
	 *
	 * @var int
	 */

    protected static function setTimeout($timeout) {
        self::$timeout = $timeout;
    }

	/**
	 * Initialize a Redis connection.
	 * @TODO: support master/slave
	 */
	protected static function connect ($host, $port, $timeout = 1) {
		$redis = new \Redis();
		$redis->connect($host, $port, $timeout);
		//$redis->setOption(\Redis::OPT_READ_TIMEOUT, $timeout);
		return $redis;
	}

	/**
	 * Get an initialized Redis connection according to the key.
	 */
	protected static function getRedis($key = NULL) {
		$class = get_called_class();
		$config = self::getConfig(); 

		$count = count($config->servers);
		$server_id = is_null($key) ? 0 : (hexdec(substr(md5($key), 0, 2)) % $count);

		$host = $config->servers[$server_id]['host'];
		$port = $config->servers[$server_id]['port'];
		$connect_index = $host . ":" . $port;
		if (!isset(self::$connections[$connect_index])) {
			self::$connections[$connect_index] = self::connect($host, $port);
		}
		return self::$connections[$connect_index];
	}

	protected static function getPrefix() {
		$class = get_called_class();
		if (!is_null($class::$prefix)) {
			return $class::$prefix;
		}
		return get_called_class();
	}

    //TODO
	protected static function getConfig() {
	}

	/**
	 * Get real key
	 */
	public static function getKey ($name) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
		return $key;
	}

	public static function __callStatic($method, $args) {
		$class = get_called_class();
		$name = $args[0];
		$key = self::getKey($name);
		$args[0] = $key;

		try {
			$result = call_user_func_array(array($class::getRedis($key), $method), $args);
		}
		catch (\RedisException $e) {
			$result = NULL;
		}

		return $result;
	}


}
