<?php
/**
 * Yar.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 12:08
 */

namespace eYaf\RPC\Yar;

use eYaf\RPC\RPCException;

/**
 *
 * 单个调用
 * $client = new \eYaf\RPC\Yar\Client();
 * $client->call("http://120.24.48.77:8081/api/", "test", array("parameters1"), array($this, 'callback'));
 *
 * 并行调用
 * $client->start();
 * $client->call("http://120.24.48.77:8081/api/", "test", array("parameters1"), array($this, 'callback'));
 * $client->call("http://120.24.48.77:8081/api/", "test", array("parameters2"), array($this, 'callback'));
 * $client->call("http://yaf.central.com/api/", "test", array("parameters3"), array($this, 'callback'));
 * $client->call("http://yaf.central.com/api/", "test", array("parameters4"), array($this, 'callback'));
 * $client->loop();
 *
 * Class Yar
 * @package eYaf\RPC\Client
 */
class Client
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

    public function __construct()
    {
        if (!extension_loaded("yar"))
            throw new RPCException('extension yar is not exist!');
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
     * @param null $callback
     * @return $this|mixed
     */
    public function call($uri, $method, $parameters = array(), $callback = null)
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
     * @param null $error_callback
     * @return $this
     */
    public function loop($callback = null, $error_callback = null)
    {
        \Yar_Concurrent_Client::loop($callback, $error_callback);
        $this->_sync = false;//关闭并行
        return $this;
    }
}