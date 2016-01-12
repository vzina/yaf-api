<?php

/**
 * Test.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/11
 * Time: 15:12
 */
class ApiController extends \eYaf\Controllers
{

    public function indexAction()
    {
        if ($this->_request->isPost()) {
            \Helper\Tools::logfile('test',file_get_contents('php://input'),LOG_PATH);
            (new \eYaf\RPC\Yar\Server())->handle($this);
        }
        $this->_response->setBody('<h1>404</h1>');
    }

    /**
     * the doc info will be generated automatically into service info page.
     * @params
     * @return
     */
    public function test($p)
    {
        return array(
            'test'=>time(),
            'p'=>$p
        );
    }
}