<?php
namespace Admin\Modules\Common;

use \Frame\Libs\Cache;

class BaseSession {

    public $user = NULL;
    private $mark = 'speed_admin_token';
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
        $this->memHandle = \Libs\Cache\Memcache::instance();
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
			
			if(!empty($value) && !empty($value['data']['id'])) {
                $this->user = $value['data'];
			}
        }/*else{
            $this->user = array('id' => 2);
        }*/
        return $this;
    }

    private function store($user) {
        $this->memHandle->set($this->markvalue, $user);
    }

    public function reflash($user) {
        $this->store($user);
    }

    public function marked($user) {
        $this->markvalue = \Libs\Util\Utilities::getUniqueId();
        $this->store($user);
        //set cookie

        setcookie($this->mark, $this->markvalue, time() + 3600 * 24 * 365, '/', $_SERVER['SERVER_NAME']);
    }

    public function destory() {
        $this->store(NULL);
        $this->user = NULL;
        setcookie($this->mark, NULL, 0, '/', $_SERVER['SERVER_NAME']);
    }

}
