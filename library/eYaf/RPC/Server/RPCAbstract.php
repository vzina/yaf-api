<?php
/**
 * RPCAbstract.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 12:07
 */

namespace eYaf\RPC\Server;


abstract class RPCAbstract
{
    abstract public function handle($object);
}