<?php
namespace eYaf;

use Helper\Tools;
use Yaf\Application;
use Yaf\Exception;
use Yaf\Loader;
use Yaf\Registry;

/**
 *
 */
class Models
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

    /**
     * Validate rules
     *
     * @var array
     */
    protected $_validate = array();

    const UNKNOWN_ERROR = -9;
    const SYSTEM_ERROR = -8;
    const VALIDATE_ERROR = -7;

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
        } catch (Exception $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Get function cache
     *
     * @param string $func
     * @param mixed $args
     * @param int $expire
     * @return mixed
     */
    public function cached($func, $args = null, $expire = 60)
    {
        $key = md5(get_class($this) . $func . serialize($args));

        if (!$data = $this->cache->get($key)) {
            $data = call_user_func_array(array($this, $func), $args);
            $this->cache->set($key, $data, $expire);
        }

        return $data;

    }

    /**
     * Init Cola_Com_Cache
     *
     * @param mixed $name
     * @return Cola_Com_Cache
     */
    public function cache($name = 'cache', $flag = false)
    {
        return Tools::Cache($name, $flag);
    }

    /**
     * Init Cola_Com_Mq
     *
     * @param mixed $name
     * @return Cola_Com_Mq
     */
// 	public function mq($name = '_rabbitmq')
//    {
//        return Cola_Com::mq($name);
//    }

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            $this->error(array('code' => self::SYSTEM_ERROR, 'msg' => $e->getMessage()));
            return false;
        }
    }

    /**
     * Delete
     *
     * @param string $where
     * @param string $table
     * @return boolean
     */
    public function delete($id, $col = null)
    {
        if (is_null($col)) $col = $this->_pk;

        $where = $col . '=' . (is_int($id) ? $id : "'$id'");

        try {
            $result = $this->db->delete($where, $this->_table);
            return $result;
        } catch (Exception $e) {
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
     * @param array $config
     * @param string $regName
     * @param bool 是否重新启用实例 add by lishuix 20140313
     * @return Cola_Com_Db
     */
    public function db($name = null, $flag = false)
    {
        if (empty($name)) $name = $this->_db;
        return Tools::db($name, $flag);
    }

    /**
     * Connect mongo from config
     *
     * @return Cola_Com_Mongo
     * add by xing  2012-06-17
     */
//    public function mongo($name = '_mongo')
//    {
//        if ($mongo = Cola::reg($name)) return $mongo;
//        $config = (array)Cola::config($name) + array('adapter' => 'Mongo', 'params' => array());
//        $mongo = new Cola_Com_Mongo($config['params']);
//        Cola::reg($name, $mongo);
//        return $mongo;
//    }

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
     * Validate
     *
     * @param array $data
     * @param boolean $ignoreNotExists
     * @return boolean
     */
    public function validate($data, $ignoreNotExists = false)
    {
        $validate = new Validate();
        $result = $validate->check($data, $this->_validate, $ignoreNotExists);

        if (!$result) {
            $this->_error = array('code' => self::VALIDATE_ERROR, 'msg' => $validate->error());
        }

        return $result;
    }

    /**
     * Instantiated model
     * @param string $name
     * @param string $dir
     * @throws Exception
     */
    protected function model($name, $dir = null)
    {
        return Tools::model($name, $dir);
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
     */
    public function __get($key)
    {
        switch ($key) {
            case 'db' :
                $this->db = $this->db();
                return $this->db;

            case 'cache' :
                $this->cache = $this->cache();
                return $this->cache;

            default:
                throw new Exception('Undefined property: ' . get_class($this) . '::' . $key);
        }
    }
}