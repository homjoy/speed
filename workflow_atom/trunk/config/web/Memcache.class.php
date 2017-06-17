<?php

namespace WorkFlowAtom\Config\Web;

class Memcache extends \Frame\Config {

    public static function config() {
        return array(
            'unixsock' => array(
                array('host' => '127.0.0.1', 'port' => 11211),
            )
        );
    }

}

