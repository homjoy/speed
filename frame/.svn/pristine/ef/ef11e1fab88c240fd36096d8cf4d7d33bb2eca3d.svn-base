<?php

namespace Frame\Connect;

class PDO extends \PDO {
	public function __construct($host, $db, $user, $pass, $port = 3306) {
		$dsn ="mysql:dbname={$db};host={$host};port={$port};";

		$options = array(
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
			PDO::ATTR_TIMEOUT => 1,
		);

		parent::__construct($dsn, $user, $pass, $options);
	}
}

class Database {

    protected $PDO;

    public function __construct($host, $db, $user, $pass, $port) {
        $this->PDO = new PDO($host, $db, $user, $pass, $port);
        if (!is_object($this->PDO)) throw new \Frame\Exception\RetryException("establish error with {$host} {$port} {$db}");
    }

    private function __clone() {}

    public function read($sql, $params, $hash_key) {
        $sth = $this->prepare($sql, $params);
        $success = $this->catchError($sth, $sql, $params);

        $result = array();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($hash_key)) {
                $result[$row[$hash_key]] = $row;
            } else {
                $result[] = $row;
            }
        }
        $sth->closeCursor();

        return $result;
    }

    public function write($sql, $params) {
        $sth = $this->prepare($sql, $params);
        $this->catchError($sth, $sql, $params);
        return $sth->rowCount();
    }

    public function getInsertId() {
        return $this->PDO->lastInsertId();
    }

    private function prepare($sql, $params) {
        $sth = $this->PDO->prepare($sql, array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false));
        foreach ($params as $key => $value) {
            if (strpos($key, '_') === 0) {
                $sth->bindValue(":{$key}", $value, PDO::PARAM_INT);
            } else {
                $sth->bindValue(":{$key}", $value, PDO::PARAM_STR);
            }
        }
        $sth->execute();

        return $sth;
    }

    private function catchError($sth, $sql, $params) {
        list($sql_state, $error_code, $error_message) = $sth->errorInfo();

        if ($sql_state == '00000') {
            return true;
        } elseif ($sql_state == 'HY000') {
            //mysql server has gone away
            throw new \Frame\Exception\RetryException("{$sql} binds " . json_encode($params) . " failded with state code HY000");
        }
        throw new \Frame\Exception\SQLExecException("{$sql} binds " . json_encode($params) . " raise error {$sql_state} {$error_code} {$error_message}");

    }

    public function __call($func, $args) {
        return call_user_func_array(array(&$this->PDO, $func), $args);
    }

}
