<?php

/**
 * Shell.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/12
 * Time: 18:06
 */
class ShellController extends \eYaf\Tasks
{
    public function indexAction()
    {
        $cron = null;
        \Yaf\Loader::import(SYS_CONFIG_PATH . 'cron.inc.php');
        $cron = new \eYaf\CronManager($cron);
        $cron->start();
        exit(0);
    }
}