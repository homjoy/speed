<?php

namespace Libs\Cache;

Use \Libs\Cache\Memcache;

abstract class MemcacheBase {

    protected $memcache = NULL;
    //存储key的class
    protected $memIdentityObject = NULL;

    public function __construct(\Libs\Cache\MemcacheIdentityObject $memidobj) {
        $this->memcache = Memcache::instance();
        $this->memIdentityObject = $memidobj;
    }

    public static function create(\Libs\Cache\MemcacheIdentityObject $memidobj) {
        return new static($memidobj);
    }

    abstract function del();

    public function get() {
        $memRes = $this->memcache->getMulti($this->memIdentityObject->getKeys());
        $notInCache = array();
        $twitter = array();
        foreach ($this->memIdentityObject->getSuffix() as $suffix) {
            $key = $this->memIdentityObject->getPrefix() . $suffix;
            if (isset($memRes[$key])) {
                if (!empty($memRes[$key])) {
                    $twitter[$suffix] = $memRes[$key];
                } else {
                    $twitter[$suffix] = array();
                }
            } else {
                $notInCache[] = $suffix;
            }
        }
        return array($notInCache, $twitter);
    }

}
