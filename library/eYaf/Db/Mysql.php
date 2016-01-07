<?php
/**
 *
 */
namespace eYaf\Db;

class Mysql extends DBAbstract
{
    /**
     * Connect to MySQL
     *
     * @return resource connection
     */
    protected function _connect($params)
    {
        if (!extension_loaded('mysql')) {
            throw new DBException('Can not find mysql extension.');
        }

        $func = ($params['persistent']) ? 'mysql_pconnect' : 'mysql_connect';

        $connection = @$func(
            $params['host'] . ':' . $params['port'],
            $params['user'],
            $params['password']
        );

        if (is_resource($connection) && mysql_select_db($params['database'], $connection)) {
            $this->_connection = $connection;
            $this->query("SET NAMES '" . $this->_config['charset'] . "';");
            return $this->_connection;
        }
        

        throw new DBException($this->error());
    }

    /**
     * Select Database
     *
     * @param string $database
     * @return boolean
     */
    public function selectDb($database)
    {
        return mysql_select_db($database, $this->_connection);
    }

    /**
     * Close mysql connection
     *
     */
    public function close()
    {
        if (is_resource($this->_connection)) {
            mysql_close($this->_connection);
        }
    }

    /**
     * Free result
     *
     */
    public function free()
    {
        mysql_free_result($this->_query);
    }

    /**
     * Query sql
     *
     * @param string $sql
     * @return Cola_Com_Db_Mysql
     */
    public function query($sql)
    {
        $this->_lastSql = $sql;

        if ($this->_debug) {
            $this->log($sql . '@' . date('Y-m-d H:i:s'));
        }

        if ($this->_query = mysql_query($sql, $this->_connection)) {
            return $this;
        }

        $msg = $this->error() . '@' . $sql . '@' . date('Y-m-d H:i:s');

        $this->log($msg);

        throw new DBException($msg);
    }

    /**
     * Return the rows affected of the last sql
     *
     * @return int
     */
    public function affectedRows()
    {
        return mysql_affected_rows($this->_connection);
    }

    /**
     * Fetch one row result
     *
     * @param string $type
     * @return mixd
     */
    public function fetch($type = 'ASSOC')
    {
        $type = strtoupper($type);

        switch ($type) {
            case 'ASSOC':
                $func = 'mysql_fetch_assoc';
                break;
            case 'NUM':
                $func = 'mysql_fetch_array';
                break;
            case 'OBJECT':
                $func = 'mysql_fetch_object';
                break;
            default:
                $func = 'mysql_fetch_assoc';
        }

        return $func($this->_query);
    }

    /**
     * Fetch All result
     *
     * @param string $type
     * @return array
     */
    public function fetchAll($type = 'ASSOC')
    {
        switch ($type) {
            case 'ASSOC':
                $func = 'mysql_fetch_assoc';
                break;
            case 'NUM':
                $func = 'mysql_fetch_array';
                break;
            case 'OBJECT':
                $func = 'mysql_fetch_object';
                break;
            default:
                $func = 'mysql_fetch_assoc';
        }
        $result = array();
        while ($row = $func($this->_query)) {
            $result[] = $row;
        }
        mysql_free_result($this->_query);
        return $result;
    }

    /**
     * Get last insert id
     *
     * @return int
     */
    public function lastInsertId()
    {
        return mysql_insert_id($this->_connection);
    }

    /**
     * Beging transaction
     *
     */
    public function beginTransaction()
    {
        mysql_query('START TRANSACTION', $this->_connection);
    }

    /**
     * Commit transaction
     *
     * @return boolean
     */
    public function commit()
    {
        if ($result = mysql_query('COMMIT', $this->_connection)) {
            return true;
        }

        throw new DBException($this->error());
    }

    /**
     * Roll back transaction
     *
     * @return boolean
     */
    public function rollBack()
    {
        if ($result = mysql_query('ROLLBACK', $this->_connection)) {
            return true;
        }

        throw new DBException($this->error());
    }

    /**
     * Escape string
     *
     * @param string $str
     * @return string
     */
    public function escape($str)
    {
        return @mysql_escape_string($str);
    }

    /**
     * Get error
     *
     * @return string|array
     */
    public function error($type = 'STRING')
    {
        $type = strtoupper($type);

        if (is_resource($this->_connection)) {
            $errno = mysql_errno($this->_connection);
            $error = mysql_error($this->_connection);
        } else {
            $errno = mysql_errno();
            $error = mysql_error();
        }

        if ('ARRAY' == $type) {
            return array('code' => $errno, 'msg' => $error);
        }
        return $errno . ':' . $error;
    }
}