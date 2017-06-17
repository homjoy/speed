<?php
namespace Pixie;

interface DatabaseAdapter
{
    /**
     * get the database type for query builder build database related sql.
     * @return string
     */
    public function getDatabaseType();

    /**
     * read data from database
     * @param $sql
     * @param array $bindParams
     * @param bool  $fromMaster default false, if true ,you must read data from master db
     * @param array $fetchParameters
     * @return mixed
     */
    public function read($sql, $bindParams = array(), $fromMaster = false, $fetchParameters = array());

    /**
     * write related execute
     * @param $sql
     * @param array $bindParams
     * @return mixed
     */
    public function write($sql, $bindParams = array());


    /**
     * get the last insert record id
     * @return integer
     */
    public function lastInsertId();
}