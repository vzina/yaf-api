<?php
/**
 * Abstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 13:52
 */

namespace Mysql;


use eYaf\Db;
use eYaf\Db\DBException;
use Yaf\Exception;
use Yaf\Registry;

/**
 * 数据库Mysql
 * Class AbstractModel
 * @package Mysql
 */
abstract class AbstractModel
{
    /**
     * Db config name
     *
     * @var string
     */
    protected $_db = '_db';

    /**
     * Table name, with prefix and main name
     *
     * @var string
     */
    protected $_table;

    /**
     * Primary key
     *
     * @var string
     */
    protected $_pk = 'id';

    /**
     * Error
     *
     * @var mixed string | array
     */
    protected $_error;

    const UNKNOWN_ERROR = -9;
    const SYSTEM_ERROR = -8;
    const VALIDATE_ERROR = -7;

    protected static $_instance;

    protected function __construct()
    {
    }

    /**
     *
     * @return mixed|static
     */
    public static function getInstance()
    {
        if (!is_object(static::$_instance)) {
            static::$_instance = new static;
        }

        return static::$_instance;
    }

    /**
     * Load data
     *
     * @param int $id 字段值
     * @param null $col 字段名
     * @return array
     */
    public function load($id, $col = null)
    {
        if (is_null($col)) $col = $this->_pk;
        $sql = "select * from {$this->_table} where {$col} = " . (is_int($id) ? $id : "'$id'");

        try {
            $result = $this->db->row($sql);
            return $result;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Find result
     *
     * @param array $conditions
     * @return array
     */
    public function find($conditions = array())
    {
        if (is_string($conditions)) $conditions = array('where' => $conditions);

        $conditions += array('table' => $this->_table);

        try {
            $result = $this->db->find($conditions);
            return $result;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Count result
     *
     * @param string $where
     * @param string $table
     * @return int
     */
    public function count($where, $table = null)
    {
        if (null == $table) $table = $this->_table;
        try {
            $result = $this->db->count($where, $table);
            return $result;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Query SQL
     *
     * @param string $sql
     * @return mixed
     */
    public function query($sql)
    {
        try {
            $result = $this->db->query($sql);
            return $result;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Get SQL result
     *
     * @param string $sql
     * @return array
     */
    public function sql($sql)
    {
        try {
            $result = $this->db->sql($sql);
            return $result;
        } catch (DBException $e) {
            //echo $e;exit;
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Insert
     *
     * @param array $data
     * @param string $table
     * @return boolean
     */
    public function insert($data, $table = null)
    {
        if (null == $table) $table = $this->_table;

        try {
            $result = $this->db->insert($data, $table);
            return $result;
        } catch (DBException $e) {
            //echo $e;exit;
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Update
     *
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function update($id, $data)
    {
        $where = $this->_pk . '=' . (is_int($id) ? $id : "'$id'");

        try {
            $result = $this->db->update($data, $where, $this->_table);
            return true;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Delete
     *
     * @param $id
     * @param null $col
     * @return bool
     */
    public function delete($id, $col = null)
    {
        if (is_null($col)) $col = $this->_pk;

        $where = $col . '=' . (is_int($id) ? $id : "'$id'");

        try {
            $result = $this->db->delete($where, $this->_table);
            return $result;
        } catch (DBException $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Escape string
     *
     * @param string $str
     * @return string
     */
    public function escape($str)
    {
        return $this->db->escape($str);
    }

    /**
     * Connect db from config
     *
     * @param null $name
     * @param array $config
     * @return Db\DbAbstract
     * @throws DBException
     */
    public function db($name = null, array $config = array())
    {
        if (empty($name)) $name = $this->_db;
        return Db::instance($name, $config);
    }

    public function gameDb()
    {
        $this->db->result('');
    }

    /**
     * Set table Name
     *
     * @param string $table
     * @return $this|string
     */
    public function table($table = null)
    {
        if (!is_null($table)) {
            $this->_table = $table;
            return $this;
        }

        return $this->_table;
    }

    /**
     * Get or set error
     *
     * @param mixed $error string|array
     * @return mixed $error string|array
     */
    public function error($error = null)
    {
        if (!is_null($error)) {
            $this->_error = $error;
        }

        return $this->_error;
    }

    /**
     * Dynamic set vars
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value = null)
    {
        $this->$key = $value;
    }

    /**
     * Dynamic get vars
     *
     * @param string $key
     * @return \eYaf\Db\DbAbstract
     * @throws Exception
     */
    public function __get($key)
    {
        switch ($key) {
            case 'db' :
                $this->db = $this->db();
                return $this->db;
            default:
                throw new Exception('Undefined property: ' . get_class($this) . '::' . $key);
        }
    }

}