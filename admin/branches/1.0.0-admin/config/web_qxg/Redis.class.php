<?php

namespace Admin\Config\Web;

class Redis extends \Frame\Config {

    public function config(){
        return array(
            'writeHost' => 'http://10.0.18.52:8081/write',
            'xwriteHost' => 'http://10.0.18.52:8081/xwrite',
            'readHosts' => array(
                'http://10.0.18.52:8080/read',
                'http://10.0.20.18:8080/read',
                
            )
        );
    }

}

