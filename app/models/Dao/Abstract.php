<?php
/**
 * Abstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 13:53
 */

namespace Dao;

/**
 * 提供统一对外的数据访问接口
 * Class AbstractModel
 * @package Dao
 */
abstract class AbstractModel
{
    protected static function mysql($name = null)
    {
        if (empty($name)) {
            $name = static::_getClassName();
        }
        $className = '\Mysql\\' . $name;
        return call_user_func(array($className, 'getInstance'));
    }

    protected static function redis($name = null, $db = 0)
    {
        if (empty($name)) {
            $name = static::_getClassName();
        }

        if (!is_int($db)) {
            $db = 0;
        }
        $className = '\Redis\Db' . $db . '\\' . $name;
        return call_user_func(array($className, 'getInstance'));
    }

    protected static function http($name = null)
    {
        if (empty($name)) {
            $name = static::_getClassName();
        }
        $name = str_replace('Model', '', $name);
        $className = '\Http\\' . $name . '\\' . $name . 'ClientModel';
        return call_user_func(array($className, 'getInstance'));
    }

    protected static function _getClassName()
    {
        return substr(strrchr(get_called_class(), '\\'), 1);
    }
}