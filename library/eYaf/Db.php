<?php
/**
 *
 */
namespace eYaf;

use eYaf\Db\Masterslave;

class Db
{
    public static function factory($config)
    {
        $config += array('masterslave' => false);

        if ($config['masterslave']) {
            return new Masterslave($config);
        }
        $adapter = $params = null;
        extract($config);
        $class = __NAMESPACE__ . '\Db\\' . ucfirst($adapter);
        return new $class($params);
    }
}