<?php

namespace Admin\Package\Common;

use Admin\Package\Helper\WorkflowDBHelper;
use Admin\Package\Helper\SpeedImDBHelper;
use Admin\Package\Helper\AdministrationDBHelper;

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
            case 'workflow':
                return WorkflowDBHelper::getConn();
            case 'speed_im':
                return SpeedImDBHelper::getConn();
            case 'speed_administration':
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
        try{
            $hashKey = !empty($fetchParameters['hashKey']) ? $fetchParameters['hashKey'] : null;
            return $this->conn()->read($sql,$bindParams,$fromMaster,$hashKey);
        }catch (\Frame\Exception\SQLExecException $e){
            self::writeLog($e->getMessage());
            return FALSE;
        }
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
        try{
            return $this->conn()->write($sql,$bindParams);
        }catch (\Frame\Exception\SQLExecException $e){
            self::writeLog($e->getMessage());
            return FALSE;
        }
    }

    /**
     * 获取增加数据的主键id
     * @return mixed
     * @throws \Exception
     */
    public function lastInsertId()
    {
        try{
            return $this->conn()->getInsertId();
        }catch (\Frame\Exception\SQLExecException $e){
            self::writeLog($e->getMessage());
            return FALSE;
        }
    }

    /**
     * 写日志
     * @return mixed
     * @throws 
     */
    private static function writeLog($msg = ''){
        $write = new \Libs\Log\BasicLogWriter();
        $log = new \Libs\Log\Log($write);
        $log->log('db_error', $msg);
        //throw new \Frame\Exception\SQLExecException($e->getMessage());
    }

}
