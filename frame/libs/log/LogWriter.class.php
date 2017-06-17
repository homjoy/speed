<?php

namespace Libs\Log;

abstract class LogWriter
{

    public function __construct() {}

    abstract public function write($mark, $str);

}
