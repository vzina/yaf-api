<?php


class IndexController extends \eYaf\Controllers
{
    public function indexAction() 
    {
//        \Helper\Tools::cache('_yac')->set('aaa','test');
        $this->_view->heading = 'Home Page!';
        $this->_view->display('index/index.phtml');
    }

    public function testAction()
    {
//        \Helper\Tools::cache('_redis')->delete('aaa');
//        var_dump(\Helper\Tools::cache('_yac')->get('aaa'));
    }
}
