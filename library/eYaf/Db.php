<?php
/**
 *
 */
namespace eYaf;

use eYaf\Db\DBException;
use eYaf\Db\Masterslave;

class Db
{
    /**
     * db工厂方法
     * @param array $config
     * @return Db\DbAbstract|Masterslave
     * @throws DBException
     */
    public static function factory(array $config)
    {
        $config += array('masterslave' => false);

        if ($config['masterslave']) {
            return new Masterslave($config);
        }
        $adapter = $params = null;
        extract($config);
        $class = __NAMESPACE__ . '\Db\\' . ucfirst($adapter);
        $db = new $class($params);

        if (!($db instanceof Db\DBAbstract)) {
            throw new DBException("[error] {$class} is not instanceof Db\\DBAbstract");
        }

        return $db;
    }
}