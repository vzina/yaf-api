<?php


class IndexController extends \eYaf\Controllers
{
    public function init()
    {
        \eYaf\Cache::instance('_yac');//缓存
    }

    public function indexAction()
    {
        var_dump(\Dao\UserModel::test(1), \Dao\UserModel::httpTest());
        $this->_yac->aaa = 'test';
//
        var_dump($this->_yac->aaa);

//        $this->_view->heading = 'Home Page!';
//        $this->_view->display('index/index.phtml');
        $this->_response->setBody('Home Page!');
    }

    public function testAction()
    {
        $this->_response->setBody($this->_yac->aaa);
    }

    public function notfoundAction()
    {
        $this->_response->setBody(404);
    }
}

