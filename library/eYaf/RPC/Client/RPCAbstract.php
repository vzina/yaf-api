<?php
/**
 * RPCAbstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 12:05
 */

namespace eYaf\RPC\Client;


use eYaf\RPC\RPCException;

abstract class RPCAbstract
{
    protected $rpc;

    abstract public function setOpt($name, $value);

    public function __call($method, $parameters)
    {
        if(!is_callable(array($this->rpc, $method))){
            throw new RPCException("[{$method}] not exists!");
        }
        return call_user_func_array(array($this->rpc, $method), $parameters);
    }
}