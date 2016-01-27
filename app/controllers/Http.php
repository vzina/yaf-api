<?php

/**
 * Http.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/27
 * Time: 10:36
 */
class HttpController extends \eYaf\Controllers
{
    public function indexAction()
    {
        $client = new \Http\User\UserClientModel();
        var_dump($client->test());
    }

    public function handleAction()
    {
        \Http\User\UserServerModel::handle();
    }
}