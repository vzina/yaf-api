<?php
/**
 *
 */
namespace eYaf;

use eYaf\Db\DBException;
use eYaf\Db\Masterslave;
use Yaf\Registry;

class Db
{
    /**
     * db工厂方法
     * @param array $config
     * @return Db\DbAbstract|Masterslave
     * @throws DBException
     */
    private static function factory(array $config)
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

    public static function instance($name = null, array $config = array())
    {
        if (empty($name)) $name = '_db';
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
            $config['adapter'] = 'Pdo\Mysql';
        }

        $db = Db::factory($config);
        Registry::set($name, $db);

        return $db;
    }
}