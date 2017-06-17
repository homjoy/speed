<?php

namespace Admin\Config\Web;

class Memcache extends \Frame\Config {

    public static function config() {
        return array(
            'unixsock' => array(
                array('host' => '192.168.128.13', 'port' => 11211),
            )
        );
    }

}
