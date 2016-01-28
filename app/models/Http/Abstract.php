<?php
/**
 * Abstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/27
 * Time: 10:22
 */

namespace Http;


use Yaf\Exception;

/**
 * Class AbstractModel
 * @package Http
 */
abstract class AbstractModel
{
    protected static $_instance = null;

    protected function __construct()
    {
        if (!extension_loaded("yar"))
            throw new Exception('[error] extension yar is not exist!');
    }

    /**
     * @return null|static
     */
    public static function getInstance()
    {
        if (!is_object(self::$_instance)) {
            self::$_instance = new static;
        }
        return self::$_instance;
    }

    public function __call($method, $args)
    {
        if(!in_array($method, array('call','start','loop','setOpt'))){
            throw new Exception("[error] {$method} is not exist!");
        }
        return call_user_func_array(array(BaseModel::getInstance(), $method), (array)$args);
    }
}