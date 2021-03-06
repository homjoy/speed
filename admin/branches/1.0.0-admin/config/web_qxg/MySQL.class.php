<?php

namespace Admin\Config\Web;

require_once '/home/work/conf/api/MySQLConfigApi.php';

class MySQL extends \Frame\Config {

    const MYSQL_KEY = 'online';

    public function config($name='vote') {

        $config =  \MySQLConfigApi::GetCfgByServDB(self::MYSQL_KEY, $name);
        return array($name => $config);
    }


}

