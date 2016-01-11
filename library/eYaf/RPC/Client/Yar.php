<?php
/**
 * Yar.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 12:08
 */

namespace eYaf\RPC\Client;


class Yar extends RPCAbstract
{

    public function __construct($url)
    {
        $this->rpc = new \Yar_Client($url);
    }

    public function setOpt($name, $value)
    {
        $this->rpc->setOpt($name, $value);
        return $this;
    }

    public static function call($uri, $method, $parameters, $callback)
    {
        \Yar_Concurrent_Client::call($uri, $method, $parameters, $callback);
    }

    public static function loop($callback = null, $error_callback = null)
    {
        \Yar_Concurrent_Client::loop($callback, $error_callback);
    }
}