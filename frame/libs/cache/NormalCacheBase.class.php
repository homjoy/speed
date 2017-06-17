<?php
namespace Libs\Cache;

class NormalCacheBase extends \Libs\Cache\MemcacheBase{

    private $NormalCacheBaseObj = NULL;
    private $timeout = 600;
    private $notInCache = NULL;

    public function __construct($prefix = '') {
        if ('' === trim($prefix)) {
            return TRUE;
        }
        $memIdentityObject = new MemcacheIdentityObject($prefix);
        parent::__construct($memIdentityObject);
    }

    public function setTime($time = 600) {
        $time = (int)$time;
        return $this->timeout = $time;	
    }

    public function setCache(array $data) {
        $bool = $this->setCacheMulti($data);	
        return $bool;
    }

    public function getCache($keys) {
        if (empty($keys)) {
            $keys = array(0);	
        }
        else if (!is_array($keys)) {
            $keys = array($keys);	
        }
        $this->memIdentityObject->setSuffix($keys);
        list($notCacheCataids, $cacheData) = $this->get();
        $this->notInCache = $notCacheCataids;
        return $cacheData;
    }

    public function notInCache() {
        return $this->notInCache;	
    }

    private function setCacheMulti(array $data) {
        $suffixIndex = array_keys($data);
        $this->memIdentityObject->setSuffix($suffixIndex);
        $putToMemcache = array();
        foreach ($this->memIdentityObject->getSuffix() as $suffix) {
            $key = $this->memIdentityObject->getPrefix() . $suffix;
            if (!empty($data[$suffix])) {
                $putToMemcache[$key] = $data[$suffix];
            }
        }
        return $this->memcache->setMulti($putToMemcache, $this->timeout);
    }

    public function getResultCode() {
        return $this->memcache->getResultCode();
    }

    public function del(){
    }

    public function deleteCache(array $suffixes) {
        $this->memIdentityObject->setSuffix($suffixes);
        $putToMemcache = array();
        $delSuffix = array();
        foreach ($this->memIdentityObject->getSuffix() as $suffix) {
            $key = $this->memIdentityObject->getPrefix() . $suffix;
            $ok = $this->memcache->delete($key);
            if ($ok) {
                $delSuffix[] = $suffix;
            }
        }
        return $delSuffix;
    }

    static public function instance() {
        if(!isset(static::$instance)) {
            static::$instance = new self(static::PREFIX);
            static::$instance->setTime(static::TIMEOUT);
        }       
        return static::$instance;
    }

}
