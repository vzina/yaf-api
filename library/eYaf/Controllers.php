<?php
/**
 * Controller.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/01/06
 * Time: 16:43
 */

namespace eYaf;


use Helper\Tools;
use Yaf\Controller_Abstract;
use Yaf\Exception;
use Yaf\Registry;

abstract class Controllers extends Controller_Abstract
{
    protected $yafAutoRender = false;

    /**
     * Form keys
     *
     * @var array
     */
    protected $_keys = array();

    /**
     * Magic method
     *
     * @param string $methodName
     * @param array $args
     * @throws Exception
     */
    public function __call($methodName, $args)
    {
        throw new Exception("你要访问的页面不存在: $methodName()");
    }

    /**
     * Instantiated model
     *
     * @param string $name
     * @param string $dir
     * @return Models
     */
    protected function model($name = null, $dir = null)
    {
        return Tools::model($name, $dir);
    }



    /**
     * Get var
     *
     * @param sting $key
     * @param mixed $default
     */
    protected function getVar($key = null, $default = null)
    {
        if (null === $key) {
            return $this->_request->getParams();
        }

        $funcs = array('getParam','getQuery', 'getPost', 'getCookie', 'getServer', 'getEnv');
        foreach ($funcs as $func) {
            if (null !== ($return = $this->_request->$func($key))) return $return;
        }

        return $default;
    }

    /**
     * Post var
     *
     * @param string $key
     */
    protected function post($key = null)
    {
        return $this->_request->getPost($key);
    }

    /**
     * Get var
     *
     * @param string $key
     * @param mixed $default
     */
    protected function get($key = null)
    {
        return $this->_request->getQuery($key);
    }

    /**
     * Get data from form
     *
     * @param array $keys
     * @param string $method
     * @return array
     */
    protected function form($keys = null, $method = 'post')
    {
        $fKeys = $data = array();

        if (null === $keys && !$keys = $this->_keys) {
            return $this->_request->{$method}();
        }

        if (isset($keys[0])) {
            foreach ($keys as $v) {
                $fKeys[$v] = $v;
            }
        }

        foreach ($fKeys as $k => $v) {
            $tmp = $this->_request->{$method}($k);
            if (null !== $tmp) $data[$v] = trim($tmp);
        }

        return $data;
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
            case 'model':
                $class = get_class($this);
                $this->model = $this->model(substr($class, 0, -10));
                return $this->model;
            default:
                if(true == Registry::has($key)){
                    $value = Registry::get($key);
                    if($value instanceof \Closure){
                        $value = call_user_func($value);
                    }
                    return $value;
                }
                throw new Exception('Undefined property: ' . get_class($this) . '::' . $key);
        }
    }
}