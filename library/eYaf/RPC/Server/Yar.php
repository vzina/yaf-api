<?php
/**
 * Yar.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 12:08
 */

namespace eYaf\RPC\Server;


use eYaf\RPC\RPCException;

/**
 * 调用方法
 * (new \eYaf\RPC\Server\Yar())->setApi($obj)->handle();
 *
 * Class Yar
 * @package eYaf\RPC\Server
 */
class Yar extends RPCAbstract
{

    public function __construct()
    {
        if (!extension_loaded("yar"))
            throw new RPCException('extension yar is not exist!');
    }

    public function handle($object)
    {
        if(!is_object($object)){
            if(class_exists($object)){
                $object = new $object;
            }
            throw new RPCException("the {$object} is not a object!");
        }
        $yar = new \Yar_Server($object);
        $yar->handle();
    }
}