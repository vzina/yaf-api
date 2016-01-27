<?php
/**
 *
 */
namespace eYaf\Db\Pdo;


use eYaf\Db\DBAbstract;
use eYaf\Db\DBException;

abstract class PDOAbstract extends DBAbstract
{
    /**
	 * The PDO construct options
	 *
	 * @var array
	 */
	protected $_options = array(\PDO::ATTR_PERSISTENT => true);

	/**
	 * Create a PDO object and connects to the database.
	 *
	 * @param array $config
	 * @return resource
	 */
	protected function _connect($params)
	{
	    if ($params['persistent']) $params['options'][\PDO::ATTR_PERSISTENT] = true;
	    $connection = new \PDO($this->_dsn(), $params['user'], $params['password'], $params['options']);
	    $this->_connection = $connection;
	    $this->query("SET NAMES '" . $params['charset'] . "';");
	    return $this->_connection;
	}

	/**
     * Select Database
     *
     * @param string $database
     * @return boolean
     */
    public function selectDb($database)
    {
        return $this->query("use $database;");
    }

    /**
     * Close mysql connection
     *
     */
    public function close()
    {
        $this->_connection = null;
    }

    /**
     * Free result
     *
     */
    public function free()
    {
        $this->_query = null;
    }

    /**
     * Query sql
     *
     * @param string $sql
     * @return Mysql
     * @throws DBException
     */
    public function query($sql)
    {
        $this->_lastSql = $sql;

        if ($this->_debug) {
            $this->log($sql . '@' . date('Y-m-d H:i:s'));
        }

        if ($this->_query = $this->_connection->query($sql)) {
            return $this;
        }

        $msg = $this->error() . '@' . $sql . '@' . date('Y-m-d H:i:s');

        $this->log($msg);
        throw new DBException($msg);
    }

    /**
     * Return the rows affected of the last sql
     * @return int
     * @throws DBException
     */
    public function affectedRows()
    {
        if (empty($this->_query)) {
            throw new DBException('PDOStatement is empty,you may have freed the query or has never queried.');
        } else {
            return $this->_query->rowCount();
        }
    }

    /**
     * Get pdo fetch style
     *
     * @param string $style
     * @return int
     */
    protected static function _getFetchStyle($style)
    {
        switch ($style) {
            case 'ASSOC':
                $style = \PDO::FETCH_ASSOC;
                break;
            case 'BOTH':
                $style = \PDO::FETCH_BOTH;
                break;
            case 'NUM':
                $style = \PDO::FETCH_NUM;
                break;
            case 'OBJECT':
                $style = \PDO::FETCH_OBJECT;
                break;
            default:
                $style = \PDO::FETCH_ASSOC;
        }

        return $style;
    }

    /**
     * Fetch one row result
     *
     * @param string $type
     */
    public function fetch($type = 'ASSOC')
    {
        $type = strtoupper($type);
        return $this->_query->fetch(self::_getFetchStyle($type));
    }

    /**
     * Fetch All result
     *
     * @param string $type
     * @return array
     */
    public function fetchAll($type = 'ASSOC')
    {
        $type = strtoupper($type);
        $result = $this->_query->fetchAll(self::_getFetchStyle($type));
        $this->free();
        return $result;
    }

	/**
	 * Initiate a transaction
	 *
	 * @return boolean
	 */
	public function beginTransaction()
	{
		return $this->_connection->beginTransaction();
	}

	/**
	 * Commit a transaction
	 *
	 * @return boolean
	 */
	public function commit()
	{
		return $this->_connection->commit();
	}

	/**
	 * Roll back a transaction
	 *
	 * @return boolean
	 */
	public function rollBack()
	{
		return $this->_connection->rollBack();
	}

	/**
	 * Get the last inserted ID.
	 *
	 * @param string $tableName
	 * @param string $primaryKey
	 * @return integer
	 */
	public function lastInsertId($tableName = null, $primaryKey = null)
	{
		return $this->_connection->lastInsertId();
	}

	/**
     * Escape string
     *
     * @param string $str
     * @return string
     */
	public function escape($str) {
        return addslashes($str);
    }

    /**
     * Get error
     *
     * @return string|array
     */
    public function error($type = 'STRING')
    {
        $type = strtoupper($type);

        if ($this->_connection->errorCode()) {
            $errno = $this->_connection->errorCode();
            $error = $this->_connection->errorInfo();
        } else {
            $errno = $this->_query->errorCode();
            $error = $this->_query->errorInfo();
        }

        if ('ARRAY' == $type) {
            return array('code' => $errno, 'msg' => $error[2]);
        }
        return $errno . ':' . $error[2];
    }
}