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
 * 客户端调用
 * 单个调用
 * $client = static::getInstance();
 * $client->call("http://host/api/", "test", array("parameters1"), array($this, 'callback'));
 *
 * 并行调用
 * $client->start();
 * $client->call("http://host/api/", "test", array("parameters1"), array($this, 'callback'));
 * $client->call("http://host/api/", "test", array("parameters2"), array($this, 'callback'));
 * $client->call("http://yaf.central.com/api/", "test", array("parameters3"), array($this, 'callback'));
 * $client->call("http://yaf.central.com/api/", "test", array("parameters4"), array($this, 'callback'));
 * $client->loop();
 *
 * 服务端调用
 * $server = static::getInstance();
 * $server->handle($obj);
 *
 * Class AbstractModel
 * @package Http
 */
class AbstractModel
{
    protected $rpc;
    protected $_sync = false;
    /**
     * $key 可以是:  YAR_OPT_PACKAGER, (打包类型)
     *              YAR_OPT_PERSISTENT (需要服务端支持keepalive),
     *              YAR_OPT_TIMEOUT,
     *              YAR_OPT_CONNECT_TIMEOUT (连接超时)
     * @var array
     */
    protected $_opt = array();

    protected static $_instance = null;

    protected function __construct()
    {
        if (!extension_loaded("yar"))
            throw new Exception('extension yar is not exist!');
    }

    /**
     * @return null|static
     */
    public static function getInstance()
    {
        if(!is_object(static::$_instance)){
            static::$_instance  = new static;
        }

        return static::$_instance;
    }

    public function setOpt($opt)
    {
        $this->_opt += $opt;
        return $this;
    }

    /**
     * 启动并行模式
     * @return $this
     */
    public function start()
    {
        $this->_sync = true;
        return $this;
    }

    /**
     * 执行远程调用任务
     * @param $uri
     * @param $method
     * @param array $parameters
     * @param callable $callback
     * @return $this|mixed
     */
    public function call($uri, $method, $parameters = array(), callable $callback = null)
    {
        if (!$this->_sync) {
            $this->rpc = new \Yar_Client($uri);
            if (!empty($this->_opt) && is_array($this->_opt)) {
                foreach ($this->_opt as $key => $value) {
                    $this->rpc->setOpt($key, $value);
                }
            }
            $result = call_user_func_array(array($this->rpc, $method), $parameters);
            if (!empty($callback) && is_callable($callback)) {
                $result = call_user_func($callback, $result);
            }
            return $result;
        }
        \Yar_Concurrent_Client::call($uri, $method, $parameters, $callback);
        return $this;
    }

    /**
     * 发送并行请求
     * @param null $callback
     * @param callable $error_callback
     * @return $this
     */
    public function loop($callback = null, callable $error_callback = null)
    {
        \Yar_Concurrent_Client::loop($callback, $error_callback);
        $this->_sync = false;//关闭并行
        return $this;
    }

    public function handle($object)
    {
        if(!is_object($object)){
            if(class_exists($object)){
                $object = new $object;
            }
            throw new Exception("the {$object} is not a object!");
        }
        $yar = new \Yar_Server($object);
        $yar->handle();
    }
}