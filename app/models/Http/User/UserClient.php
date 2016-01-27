<?php
/**
 * UserClient.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/27
 * Time: 10:35
 */

namespace Http\User;


use Http\AbstractModel;

class UserClientModel
{
    public function test()
    {
        return AbstractModel::getInstance()->call("http://yaf.central.com/http/handle", "test", array("parameters3"));
    }

}