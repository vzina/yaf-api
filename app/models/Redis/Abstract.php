<?php
/**
 * Abstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 13:51
 */

namespace Redis;


use eYaf\Cache;
use Helper\Tools;
use Yaf\Registry;

/**
 * KV缓存Redis
 * Class AbstractModel
 * @package Redis
 */
abstract class AbstractModel
{
    protected static $_instance = null;

    /**
     * redis对象
     * @var Cache\CacheAbstract
     */
    protected $_redis;

    /**
     * key前缀
     * @var string
     */
    protected $_keyPrefix = '';

    /**
     * 配置名
     * @var string
     */
    protected $_configName = '_redisdb';

    /**
     * 表id
     * @var int
     */
    protected $_db = 0;

    protected function __construct()
    {
        $this->_redis = $this->connect();
    }

    protected function connect($config = array())
    {
        if (empty($config)) {
            $config = $this->_configName;
        }
        $_config = array();

        $sysConfig = Registry::get('config');
        if ($sysConfig && ($tmp = $sysConfig->get($config))) {
            $_config = $tmp->toArray();
        } elseif (is_array($config)) {
            $_config = $config;
        }

        $_config['adapter'] = 'Redis';
        $_config['params'][\Redis::OPT_PREFIX] = $this->_keyPrefix ?: get_class($this);
        $_config['params'][\Redis::OPT_SERIALIZER] = \Redis::SERIALIZER_PHP;

        return Cache::instance('__redis_model__', $_config);
    }

    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static;
        }

        return static::$_instance;
    }

    public function find($key)
    {
        return $this->_redis->get($key);
    }

    public function update($key, $data, $ttl = 0)
    {
        return $this->_redis->set($key, $data, $ttl);
    }
}