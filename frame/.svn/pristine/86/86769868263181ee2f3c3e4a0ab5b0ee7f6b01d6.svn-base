<?php namespace Pixie;

class Connection
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Pixie\DatabaseAdapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $adapterConfig;

    /**
     * @var Connection
     */
    protected static $storedConnection;

    /**
     * @var EventHandler
     */
    protected $eventHandler;

    /**
     * @param               $adapter
     * @param null|string   $alias
     * @param Container     $container
     * @throws Exception
     */
    public function __construct($adapter, $alias = null, Container $container = null)
    {
        $container = $container ? : new Container();
        $this->container = $container;

        if(is_string($adapter)){
            // Build a database connection and set config we
            $adapterClass = '\\Pixie\\ConnectionAdapters\\' . ucfirst(strtolower($adapter));
            $adapter = $this->container->build($adapterClass, array($this->container));
        }
        if(!($adapter instanceof \Pixie\DatabaseAdapter)){
            throw new Exception("{$adapter} is not a valid DatabaseAdapter");
        }
        $this->setAdapter($adapter);

        // Preserve the first database connection with a static property
        if (!static::$storedConnection) {
            static::$storedConnection = $this;
        }

        // Create event dependency
        $this->eventHandler = $this->container->build('\\Pixie\\EventHandler');

        if ($alias) {
            $this->createAlias($alias);
        }
    }

    /**
     * Create an easily accessible query builder alias
     *
     * @param $alias
     */
    public function createAlias($alias)
    {
        class_alias('Pixie\\AliasFacade', $alias);
        $builder = $this->container->build('\\Pixie\\QueryBuilder\\QueryBuilderHandler', array($this));
        AliasFacade::setQueryBuilderInstance($builder);
    }

    /**
     * Returns an instance of Query Builder
     * @return \Pixie\QueryBuilder\QueryBuilderHandler
     */
    public function getQueryBuilder()
    {
        return $this->container->build('\\Pixie\\QueryBuilder\\QueryBuilderHandler', array($this));
    }

    /**
     * read data from slave db
     * if fromMaster is true, you should force read from master db
     * @param $sql
     * @param array $params
     * @param bool $fromMaster
     * @param array $fetchParameters  pdo fetch parameters
     * @return mixed
     */
    public function read($sql,$params = array(),$fromMaster = false,$fetchParameters = array())
    {
        $start = microtime(true);
        $result = $this->adapter->read($sql,$params,$fromMaster,$fetchParameters);
        $end = microtime(true);
        return array($result,$end - $start);
    }

    /**
     * write data to master db
     * @param $sql
     * @param array $params
     * @return array
     */
    public function write($sql,$params=array())
    {
        $start = microtime(true);
        $result = $this->adapter->write($sql,$params);
        $end = microtime(true);
        return array($result,$end - $start);
    }

    /**
     * return last insert data id
     * @return int
     */
    public function lastInsertId()
    {
        return $this->adapter->lastInsertId();
    }

    /**
     * @param $adapter
     *
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EventHandler
     */
    public function getEventHandler()
    {
        return $this->eventHandler;
    }

    /**
     * @return Connection
     */
    public static function getStoredConnection()
    {
        return static::$storedConnection;
    }
}
