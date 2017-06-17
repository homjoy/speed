<?php

namespace Libs\DB;

class SQLMonitor {

	private static $monitor = NULL;
	private $queries = array();

	private $time_start = NULL;
	private $time_finish = NULL;

	private $query = NULL;
	private $params = NULL;

	private $overLook = TRUE;

	public static function getMonitor() {
		is_null(self::$monitor) && self::$monitor = new self();
		return self::$monitor;
	}

	public function __construct() {
	}

	public function start($sql, $params = NULL) {
        if (empty($this->overLook)) {
            return FALSE;
        }
		$this->time_start = microtime(TRUE);
		$this->query = $sql;
		$this->params = $params;
	}

	public function shutDownMonitor() {
		$this->overLook = FALSE;
	}

	public function finish(\PDOStatement $sth = NULL, $conf) {
		if (empty($this->overLook)) {
			return FALSE;
		}
		if (is_null($this->query)) {
			throw new \Exception("Trying to monitor an empty query.");
		}

		if (is_null($this->time_start)) {
			throw new \Exception("Trying to finish a query monitoring without starting it.");
		}

		$this->time_finish = microtime(TRUE);
		$sql_state = $error_code = $error_message = '';
		is_null($sth) || list($sql_state, $error_code, $error_message) = $sth->errorInfo();

		$this->queries[] = array(
			'sql' => $this->query,
			'params' => empty($this->params) ? '' : json_encode(utf8_encode(serialize($this->params))),
			'time_spent' => ($this->time_finish - $this->time_start) * 1000,
			'sql_state' => $sql_state,
			'error_code' => $error_code,
			'error_message' => $error_message,
			'conf' => $conf['HOST'] . ":" . $conf['PORT'] . ":" . $conf['DB'],
		);
	}

	public function dump() {
		return $this->queries;
	}

	public function getQueriesStatistics() {
		$count = 0;
		$total_time = 0;
		foreach ($this->queries as $query) {
			$total_time += $query['time_spent'];
			$count++;
		}
		if (empty($count)) {
			$average = 0;
		}
		else {
			$average = number_format(($total_time / $count), 3);
		}
		
		return array($count, $average);
	}

}
