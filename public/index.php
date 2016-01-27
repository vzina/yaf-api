<?php
isset($_GET['_debug']) && define('IS_DEBUG', true);
define('ROOT_PATH', dirname(__DIR__) . '/');           /* 根目录 */
define('APP_PATH', ROOT_PATH . 'app/');          /* 应用目录 */
define('CONFIG_PATH', APP_PATH . 'config/');          /* 系统配置目录 */

$application = new \Yaf\Application(CONFIG_PATH . "application.ini", \Yaf\ENVIRON);

$application->bootstrap();

if (!defined('APPLICATION_NOT_RUN')) {
    $application->run();
}
