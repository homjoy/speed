<?php

namespace Atom\Config\Web;

//require_once '/home/work/conf/api/MySQLConfigApi.php';

class MySQL extends \Frame\Config {

    const MYSQL_KEY = 'online';

    public static function config($name='speed') {

        //$config =  \MySQLConfigApi::GetCfgByServDB(self::MYSQL_KEY, $name);
        return \Atom\Config\ConfigOffline::db();
        //return array($name => $config);
    }



}

