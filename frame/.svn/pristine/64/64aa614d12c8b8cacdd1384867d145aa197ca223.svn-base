<?php
namespace Libs\Cache;

class MemcacheIdentityObject {
    //前缀
    private $prefix = "";
    //后缀
    private $suffix = array();

    function __construct($prefix = "") {
        $this->prefix = $prefix;
    }

    function setSuffix($suffix) {
        if (!is_array($suffix)) {
            throw new \Exception("suffix must be array");
        }
        $this->suffix = $suffix;	
    }

    function getSuffix() {
        return $this->suffix;
    }

    function getPrefix() {
        return $this->prefix;
    }

    function getkeys() {
        $keys = array();
        if (!empty($this->suffix)) {
            foreach ($this->suffix as $memkey) {
                $keys[] = $this->prefix . $memkey;	
            }
        }
        elseif (!empty($prefix)){
            $keys[] = $this->prefix;
        }
        return $keys; 
    }
}

