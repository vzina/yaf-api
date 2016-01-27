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
        var_dump(\Dao\UserModel::httpTest());
    }

    public function handleAction()
    {
        \Http\User\UserServerModel::handle();
    }
}