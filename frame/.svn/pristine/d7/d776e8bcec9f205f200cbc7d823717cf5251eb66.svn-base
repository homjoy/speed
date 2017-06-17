<?php
namespace Libs\Session;

use \Libs\Cache\Memcache;

class OpenApiSession {

    private $user = NULL;
    private $mark = 'speed_open_token';
    private $markvalue = NULL;
    private $memHandle = NULL;

    private $COOKIE = NULL;
    private $GET = NULL;

    public static function Singleton() {
        static $single = NULL;
        is_null($single) && $single = new self();
        return $single;
    }

    private function __construct() {
        $this->memHandle = Memcache::instance();
    }

    public function __get($field) {
        if (!empty($this->user))
            return @$this->user->$field;
        return NULL;
    }

    public function set($user) {
        $this->user = $user;
    }

    public function load($COOKIE = array(), $GET = array()) {
        $this->COOKIE = $COOKIE;
        $this->GET = $GET;
        if (isset($this->COOKIE[$this->mark])) {
            $this->markvalue = $this->COOKIE[$this->mark];
            $value = $this->memHandle->get($this->markvalue);
            if (!is_object($value)) {
                $value = (object) $value;
            }
            $this->user = $value;
        }
        return $this;
    }

    private function store($user) {
        $this->memHandle->set($this->markvalue, $user);
    }

    public function reflash($user) {
        $this->store($user);
    }

    public function marked($user, $expire=0) {
        $this->markvalue = \Gate\Libs\Utilities::getUniqueId();//18384f78b7fc018848eccf17e36bef9d
        $this->store($user);
        empty($expire) && $expire = time() + 3600 * 24 * 30;
        //set cookie
        defined('SERVER_NAME') || define("SERVER_NAME", $_SERVER['SERVER_NAME']);
        setcookie($this->mark, $this->markvalue, $expire, '/', SERVER_NAME);
    }

    public function destory() {
        $this->store(NULL);
        $this->user = NULL;
        setcookie($this->mark, NULL, 0, '/', SERVER_NAME);
    }

}
