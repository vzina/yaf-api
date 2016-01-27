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

/**
 * 客户端调用
 * 单个调用
 * $this->call("http://host/api/", "test", array("parameters1"), array($this, 'callback'));
 *
 * 并行调用
 * $this->start();
 * $this->call("http://host/api/", "test", array("parameters1"), array($this, 'callback'));
 * $this->call("http://host/api/", "test", array("parameters2"), array($this, 'callback'));
 * $this->call("http://yaf.central.com/api/", "test", array("parameters3"), array($this, 'callback'));
 * $this->call("http://yaf.central.com/api/", "test", array("parameters4"), array($this, 'callback'));
 * $this->loop();
 *
 * Class UserClientModel
 * @package Http\User
 */
class UserClientModel extends AbstractModel
{
    public function test()
    {
        return $this->call("http://yaf.central.com/http/handle", "test", array("parameters3"));
    }

}