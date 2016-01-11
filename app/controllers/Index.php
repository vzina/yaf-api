<?php


class IndexController extends \eYaf\Controllers
{
    public function indexAction()
    {
//        \Helper\Tools::cache('_yac')->set('aaa','test');
//        $client = new \eYaf\RPC\Client\Yar('http://yaf.central.com/api/');
//
//        var_dump($client->getRequest());

        \eYaf\RPC\Client\Yar::call("http://yaf.central.com/api/", "test", array("parameters1"), array($this, 'callback'));
        \eYaf\RPC\Client\Yar::call("http://yaf.central.com/api/", "test", array("parameters2"), array($this, 'callback'));
        \eYaf\RPC\Client\Yar::call("http://yaf.central.com/api/", "test", array("parameters3"), array($this, 'callback'));
        \eYaf\RPC\Client\Yar::call("http://yaf.central.com/api/", "test", array("parameters4"), array($this, 'callback'));
        \eYaf\RPC\Client\Yar::loop();
        $this->_view->heading = 'Home Page!';
        $this->_view->display('index/index.phtml');
        $this->_response->setBody('Home Page!');
    }

    public function testAction()
    {
//        \Helper\Tools::cache('_redis')->delete('aaa');
//        var_dump(\Helper\Tools::cache('_yac')->get('aaa'));
    }

    public function callback($retval, $callinfo)
    {
        var_dump($retval);
    }
}

