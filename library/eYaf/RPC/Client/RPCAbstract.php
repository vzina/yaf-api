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

    abstract public function setOpt($opt);
}