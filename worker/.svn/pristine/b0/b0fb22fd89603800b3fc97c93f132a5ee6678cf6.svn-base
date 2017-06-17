<?php

namespace Worker\Package\Common;

use Worker\Package\Helper\RecruitDBHelper;
use Worker\Package\Helper\WorkerDBHelper;
use Worker\Package\Helper\AdministrationDBHelper;

/**
 * Class DbAdapter
 * @package Atom\Package\Common
 */
class DbAdapter implements \Pixie\DatabaseAdapter
{
    /**
     * 数据库名
     * @var string
     */
    protected $database = '';

    public function __construct($database)
    {
        if(empty($database)){
            throw new \Exception('DbAdapter require the database name.');
        }
        $this->database = $database;
    }

    /**
     * @return \Libs\DB\DBConnManager
     * @throws \Exception
     */
    protected function conn()
    {
        switch($this->database)
        {
            case 'recruit':
                return RecruitDBHelper::getConn();
            case 'worker':
                return WorkerDBHelper::getConn();
            case 'administration':
                return AdministrationDBHelper::getConn();
            default:
                throw new \Exception("指定的数据库{$this->database}不存在！");
        }
    }

    /**
     * get the database type for query builder build database related sql.
     * @return string
     */
    public function getDatabaseType()
    {
        return 'mysql';
    }

    /**
     * 数据库读操作，可以在这里加上中间处理，比如记日志，捕获异常。
     * @param $sql
     * @param array $bindParams
     * @param bool $fromMaster
     * @param array $fetchParameters
     * @return mixed
     * @throws \Exception
     */
    public function read($sql, $bindParams = array(), $fromMaster = false,$fetchParameters = array())
    {
        $hashKey = !empty($fetchParameters['hashKey']) ? $fetchParameters['hashKey'] : null;
        return $this->conn()->read($sql,$bindParams,$fromMaster,$hashKey);
    }

    /**
     * 数据库写操作
     * @param $sql
     * @param array $bindParams
     * @return integer
     * @throws \Exception
     */
    public function write($sql, $bindParams = array())
    {
        return $this->conn()->write($sql,$bindParams);
    }

    /**
     * 获取增加数据的主键id
     * @return mixed
     * @throws \Exception
     */
    public function lastInsertId()
    {
        return $this->conn()->getInsertId();
    }
}
