<?php
/**
 * UserServer.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/27
 * Time: 10:35
 */

namespace Http\User;


use Http\AbstractModel;

class UserServerModel
{
    public static function handle()
    {
        AbstractModel::getInstance()->handle(new static);
    }

    public function test()
    {
        return time();
    }
}