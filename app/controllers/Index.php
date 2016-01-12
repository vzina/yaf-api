<?php


class IndexController extends \eYaf\Controllers
{
    public function init()
    {
//        \Helper\Tools::cache('_yac');//缓存
//        \Helper\Tools::db('_db');//数据库链接
    }

    public function indexAction()
    {
        $client = new \eYaf\RPC\Yar\Client();
        $client->call("http://yaf.central.com/api/", "test", array("parameters1"), function ($result) {
            var_dump($result);
        });
        echo '<hr>';
        $client->start();
        $client->call("http://120.24.48.77:8081/api/", "test", array("parameters1"), array($this, 'callback'));
        $client->call("http://120.24.48.77:8081/api/", "test", array("parameters2"), array($this, 'callback'));
        $client->call("http://yaf.central.com/api/", "test", array("parameters3"), array($this, 'callback'));
        $client->call("http://yaf.central.com/api/", "test", array("parameters4"), array($this, 'callback'));
        $client->loop();

//        $this->_yac->aaa = 'test';
//        var_dump($this->_db->row('show tables;'));

        $this->_view->heading = 'Home Page!';
        $this->_view->display('index/index.phtml');
        $this->_response->setBody('Home Page!');
    }

    public function testAction()
    {
        $this->_response->setBody($this->_yac->aaa);
    }

    public function callback($retval, $callinfo = null)
    {
        var_dump($retval);
    }
}

