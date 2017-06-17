<?php

/**
 * 在基础日志功能基础上进行扩展，集合日志收集功能
 * 依赖日志收集开关，日志收集开关配置：LOG_COLLECT，意义：1或TRUE代表收集；0或FALSE代表不收集
 */

namespace Libs\Log;

use \Libs\Log\BasicLogWriter;
use \Libs\Log\LogCollector;

class ProxyLogWriter extends BasicLogWriter {

    protected static $collectLogsBlack = array();
    private $logCollector = NULL;

    public function __construct($logCollector) {
        $this->logCollector = $logCollector;
    }

    public function write($mark, $str) {
        $class = get_called_class();
        if (in_array($mark, $class::$collectLogsBlack) || !defined('LOG_COLLECT') || !LOG_COLLECT) {
            parent::write($mark, $str);
            return;
        }
        try {
            $this->logCollector->sendLog($mark, $str);
        } catch (\Exception $e) {
            parent::write('log_collect_exception', date("Y-m-d H:i:s") . "\t" . $e->getMessage());
        }
    }

}
