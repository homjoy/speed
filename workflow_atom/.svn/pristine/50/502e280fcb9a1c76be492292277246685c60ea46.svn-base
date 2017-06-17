<?php
namespace Atom\Package\Common;

/**
 * 基础查询
 * Class BaseQuery
 * @package Atom\Package\Common
 */
class BaseQuery {
    private static $instances = array();
    /**
     * @var \Pixie\Connection
     */
    protected $conn;

    protected function __construct() {}

    /**
     * 数据库名称
     * @return string
     */
    public static function database(){
        return '';
    }

    /**
     * 主键定义
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    /**
     * 获取数据库表名
     * @return string
     */
    public static function tableName()
    {
        return '';
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function model()
    {
        $class = get_called_class();
        if(!isset(self::$instances[$class])){
            $model = new $class;

            if(!is_subclass_of($model,__CLASS__)){
                throw new \Exception('只有继承了BaseQuery 的类才能使用此单例方法！');
            }
            $database = $class::database();
            if(empty($database)){
                //取类名分隔符之前的前一个单词小写作为数据库名称
                $namespaces = explode('\\',$class);
                array_pop($namespaces);
                $database = strtolower(array_pop($namespaces));
            }
            $model->setConn(new \Pixie\Connection(new \Atom\Package\Common\DbAdapter($database)));
            self::$instances[$class] = $model;
        }

        return self::$instances[$class];
    }

    /**
     * @return \Pixie\Connection
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param \Pixie\Connection $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * 获取Builder实例
     * @param null $tableName
     * @return \Pixie\QueryBuilder\QueryBuilderHandler
     */
    public function builder($tableName = null)
    {
        $tableName = is_null($tableName) ? static::tableName() : $tableName;
        return $this->conn->getQueryBuilder()->table($tableName);
    }

    /**
     * 通过主键获取数据
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        if(empty($id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }
        if(is_array($id)){
            return $this->builder()->whereIn(static::pk(),$id)->get();
        }

        return $this->builder()->find($id,static::pk());
    }

    /**
     * 插入或者更新
     * @param $data
     * @return array|string
     */
    public function insertOrUpdate($data)
    {
        $pk = static::pk();
        if(isset($data[$pk])  && $data[$pk] > 0){
            $id = intval($data[$pk]);
            unset($data[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($data);
        }else{
            return $this->builder()
                ->onDuplicateKeyUpdate($data)->insert($data);
        }
    }

    /**
     * 删除数据
     * @param $id
     * @return mixed
     */
    public function deleteById($id)
    {
        if(empty($id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

        if(is_array($id)){
            return $this->builder()->whereIn(static::pk(),$id)->delete();
        }else{
            return $this->builder()->where(static::pk(), $id)->delete();
        }
    }
	
	/**
     * 逻辑删除数据
     * @param $id
     * @return mixed
     */
    public function deleteLogicalById($id)
    {
        if(empty($id)){
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

		$param = array('status' => 0);

        if(is_array($id)){
            return $this->builder()->whereIn(static::pk(),$id)->update($param);
        }else{
            return $this->builder()->where(static::pk(), $id)->update($param);
        }
    }

    /**
     * 原始插入.
     * @param $data
     * @return array|string
     */
    public function insert($data)
    {
        return $this->builder()->insert($data);
    }
}
