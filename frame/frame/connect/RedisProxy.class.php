<?php

namespace Frame\Connect;

class RedisProxy {

	/**
	 * Holds initialized Redis connections.
	 *
	 * @var array
	 */
	protected static $connections = array();
	protected static $writeHandle = NULL;
	protected static $readHandle = NULL;

	protected static $logConnections = array();

	/**
	 * By default the prefix of Redis key is the same as class name. But it
	 * can be specified manually.
	 *
	 * @var string
	 */
	protected static $prefix = NULL;
	protected static $xsync = NULL;

	/**
	 * Get the write connect to the nginx proxy.
	 */
	protected static function getWriteHandle() {
		if(!isset(self::$writeHandle)) {
            static $config;
            is_null($config) && $config = \Phplib\Config::load('Redis');
            $host = $config->writeHost;
            $xhost = $config->xwriteHost;
			self::$writeHandle = new RedisWrite($host, $xhost);
		}
		return self::$writeHandle;
	}	

	protected static function getReadHandle() {
		if(!isset(self::$readHandle)) {
            static $config;
            is_null($config) && $config = \Phplib\Config::load('Redis');
            $ran = array_rand($config->readHosts);
            $host = $config->readHosts[$ran];
			self::$readHandle = new RedisRead($host);
		}
		return self::$readHandle;
	}	

	protected static function getPrefix() {
		$class = get_called_class();
		if (!is_null($class::$prefix)) {
			return $class::$prefix;
		}
		return get_called_class();
	}

	protected static function getSync() { 
		$class = get_called_class();
		if (is_null($class::$xsync)) {
			return TRUE;
		}
		return FALSE;
	}

	private static function verifyMethod($method) {
        //unsupport method
        $unSupport = array('config','slaveof','info','bgrewriteaof','bgsave','flushdb','flushall','save','shutdown','rename');
        if (in_array($method, $unSupport)) {
            throw new \Exception("call unsupport method " . $method);
        }

		$writeMethods = array(
			'hdel' => TRUE, 'hincrby' => TRUE, 'hset' => TRUE, 'hsetnx' => TRUE,
			'del' => TRUE, 'expireat' => TRUE, 'expire' => TRUE, 'persist' => TRUE,
			'linsert' => TRUE, 'lpop' => TRUE, 'lpush' => TRUE, 'lpushx' => TRUE, 'lrem' => TRUE, 'lset' => TRUE, 'ltrim' => TRUE, 'rpop' => TRUE, 'rpush' => TRUE, 'rpushx' => TRUE,
			'sadd' => TRUE, 'spop' => TRUE, 'srem' => TRUE,
			'zadd' => TRUE, 'zincrby' => TRUE, 'zrem' => TRUE, 'zremrangebyrank' => TRUE,
			'append' => TRUE, 'decrby' => TRUE, 'decr' => TRUE, 'incrby' => TRUE, 'incr' => TRUE, 'setbit' => TRUE, 'setex' => TRUE, 'set' => TRUE, 'setnx' => TRUE,
		);
		if (isset($writeMethods[$method]) && !empty($writeMethods[$method])) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public static function __callStatic($method, $args) {
        $method = strtolower($method);
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$name = $args[0];
		$key = "{$prefix}:{$name}";
		$args[0] = $key;
		unset($args[0]);
		if (self::verifyMethod($method)) {
			$sync = $class::getSync();
			$result = $class::getWriteHandle()->request($method, $key, $args, $sync);
		}
		else {
			$result = $class::getReadHandle()->request($method, $key, $args);
		}
		return $result;
	}

    public static function lPush($name, $value, $sync = FALSE) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
        !$sync && $sync = $class::getSync();
        $args = array(
            1 => $value
        );
		$result = $class::getWriteHandle()->request('lpush', $key, $args, $sync);
        return $result;
    }

    public static function hMset($name, $values = array()) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
		$sync = $class::getSync();
		$params = array();
		foreach ($values as $field => $value) {
			$params[] = 'hset#' . $field . '#' . $value;
		}
		$args = array(1 => implode(",", $params));
		$result = $class::getWriteHandle()->request('multi', $key, $args, $sync);
		return $result;
    }

	public static function hMget($name, $values = array()) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
		$params = array();
		foreach ($values as $field) {
			$params[] = 'hget,' . $field;
		}
		$args = array(1 => implode(";", $params));
		$result = $class::getReadHandle()->request('multi', $key, $args);
		return $result;
	}

	/*
	 * multi only support one key because of the hash cluster
	 * a multi(PIPELINE) block is simply transmitted faster to the server
	 * this write_multi is only for write commands
	 */
	public static function multi($name, $values = array()) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
		$sync = $class::getSync();
		$params = array();
		foreach ($values as $value) {
			$params[] = implode("#", $value);
		}
		$args = array(1 => implode(",", $params));
		$result = $class::getWriteHandle()->request('multi', $key, $args, $sync);
		return $result;
	}

    //multi for read
	public static function rmulti($name, $values = array()) {
		$class = get_called_class();
		$prefix = $class::getPrefix();
		$key = "{$prefix}:{$name}";
		$params = array();
		foreach ($values as $value) {
			$params[] = implode(",", $value);
		}
		$args = array(1 => implode(";", $params));
		$result = $class::getReadHandle()->request('multi', $key, $args);
		return $result;
	}

}

class RedisWrite {

	private static $host = NULL; 
	private static $xhost = NULL; 
    static $ci = NULL;

	public function __construct($host, $xhost) {
		self::$host = $host;
		self::$xhost = $xhost;
        is_null(self::$ci) && self::$ci = curl_init();
	}

	public function request($method, $key, $args = array(), $sync = TRUE) {
		$params = array(
			'method=' . $method,
			'key=' . $key,
		);
		foreach ($args as $key => $value) {
			$pkey = "arg" . $key;
			$params[] = $pkey . "=" . $value;
		}
		$body = join($params, "&");

        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 60";
		$header[] = "Expect:";
		curl_setopt(self::$ci, CURLOPT_CONNECTTIMEOUT, 1); 
		curl_setopt(self::$ci, CURLOPT_TIMEOUT, 1); 
		curl_setopt(self::$ci, CURLOPT_HEADER, FALSE);
		if ($sync) {
			curl_setopt(self::$ci, CURLOPT_URL, self::$host);
		}
		else {
			curl_setopt(self::$ci, CURLOPT_URL, self::$xhost);
		}
        curl_setopt(self::$ci, CURLOPT_HTTPHEADER, $header);
		curl_setopt(self::$ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt(self::$ci, CURLOPT_POST, TRUE);
		curl_setopt(self::$ci, CURLOPT_POSTFIELDS, $body);

		$ret = curl_exec(self::$ci);
		if (curl_errno(self::$ci)) {
			$info = curl_getinfo(self::$ci);
			if ($info['http_code'] != 100) {
				//$redis_error_log = new \Phplib\Tools\Liblog("redis_error_log", "normal");
				//$str = $body . "\nerror with " . json_encode($info);
				//$redis_error_log->w_log($str);
			}
		}
		return json_decode(trim($ret), TRUE);
	}
}

class RedisRead {

	private static $host = NULL; 
    static $ci = NULL;

	public function __construct($host) {
        self::$host = $host;
        is_null(self::$ci) && self::$ci = curl_init();
	}

	public function request($method, $key, $args = array()) {
		$params = array(
			'method=' . $method,
			'key=' . $key,
		);
		foreach ($args as $key => $value) {
            if (FALSE === $value) break;

			$pkey = "arg" . $key;
			$params[] = $pkey . "=" . $value;
		}

        $url = self::$host . '?' . join($params, "&");

        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 60";
		$header[] = "Expect:";
		curl_setopt(self::$ci, CURLOPT_CONNECTTIMEOUT, 1); 
		curl_setopt(self::$ci, CURLOPT_TIMEOUT, 1); 
		curl_setopt(self::$ci, CURLOPT_HEADER, FALSE);
		curl_setopt(self::$ci, CURLOPT_URL, $url);
        curl_setopt(self::$ci, CURLOPT_HTTPHEADER, $header);
		curl_setopt(self::$ci, CURLOPT_RETURNTRANSFER, TRUE);

		$ret = curl_exec(self::$ci);
		if (curl_errno(self::$ci)) {
			$info = curl_getinfo(self::$ci);
            //$redis_error_log = new \Phplib\Tools\Liblog("redis_read_error_log", "normal");
            //$str = $body . "\nerror with " . json_encode($info);
            //$redis_error_log->w_log($str);
		}
        if (empty($ret)) {
            return FALSE;
        }
		return json_decode(trim($ret), TRUE);
	}
}
