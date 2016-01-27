<?php
/**
 *
 */
namespace eYaf;

use eYaf\Cache\CacheException;
use Yaf\Registry;

class Cache
{
    /**
     * Cache工厂方法
     * @param array $config
     * @return Cache\CacheAbstract
     * @throws CacheException
     */
    private static function factory($config)
    {
        $adapter = $params = null;
        extract($config);

        $class = __NAMESPACE__ . '\Cache\\' . ucfirst($adapter);
        $cache = new $class($params);

        if (!($cache instanceof Cache\CacheAbstract)) {
            throw new CacheException("[error] {$class} is not instanceof Cache\\CacheAbstract");
        }

        return $cache;
    }

    public static function instance($name, array $config = array())
    {
        if (empty($name)) $name = '_cache';
        /** 单例*/
        if (Registry::has($name)) return Registry::get($name);

        if (empty($config)) {
            /** @var \Yaf\Config\Ini $_config */
            $_config = Registry::get('config');
            if (!$_config || !($tmp = $_config->get($name))) {
                return false;
            }
            $config = $tmp->toArray();
        }

        if (empty($config['adapter'])) {
            $config['adapter'] = 'file';
        }

        $cache = self::factory($config);
        Registry::set($name, $cache);

        return $cache;
    }
}