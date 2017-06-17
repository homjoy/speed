<?php namespace Pixie\ConnectionAdapters;

use Pixie\DatabaseAdapter;

class Mysql implements DatabaseAdapter
{
    /**
     * get the database type for query builder build database related sql.
     * @return string
     */
    public function getDatabaseType()
    {
        return 'mysql';
    }

    /**
     * read data from database
     * @param $sql
     * @param array $bindParams
     * @param bool $fromMaster default false, if true ,you must read data from master db
     * @param array $fetchParameters
     * @return mixed
     */
    public function read($sql, $bindParams = array(), $fromMaster = false, $fetchParameters = array())
    {
        //TODO implement your db read using pdo or anything
        return array();
    }

    /**
     * write related execute
     * @param $sql
     * @param array $bindParams
     * @return mixed
     */
    public function write($sql, $bindParams = array())
    {
        //TODO implement your db write using pdo or anything
        return 1;
    }

    /**
     * get the last insert record id
     * @return integer
     */
    public function lastInsertId()
    {
        //TODO implement this method
        return 0;
    }

}