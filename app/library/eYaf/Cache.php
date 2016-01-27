<?php
/**
 *
 */
namespace eYaf;

use eYaf\Cache\CacheException;

class Cache
{
    /**
     * Cache工厂方法
     * @param array $config
     * @return Cache\CacheAbstract
     * @throws CacheException
     */
    public static function factory($config)
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
}