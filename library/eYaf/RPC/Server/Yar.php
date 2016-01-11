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

class Yar extends RPCAbstract
{
    /**
     * Yar 对象;
     * \Yar_Server
     * @var $_yar
     */
    protected $_yar;

    public function setApi($object)
    {
        if(!is_object($object)){
            if(class_exists($object)){
                $object = new $object;
            }
            throw new RPCException("the {$object} is not a object!");
        }
        $this->_yar = new \Yar_Server($object);
        return $this;
    }

    public function handle()
    {
        if(!is_object($this->_yar)){
            throw new RPCException("RPC 对象不存在！");
        }
        $this->_yar->handle();
    }
}