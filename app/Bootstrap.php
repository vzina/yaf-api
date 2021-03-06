<?php

/**
 * Yaf的引导文件
 * @说明: 定义方法以_init开头的,系统将在实例化时,按顺序自动执行.
 * Class Bootstrap
 */
class Bootstrap extends Yaf\Bootstrap_Abstract
{
    /**
     * 自定义致命错误输出
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initErrorHandler(Yaf\Dispatcher $dispatcher)
    {
        if (!defined('PHPUNIT_TEST')) {
            $dispatcher->setErrorHandler(array(get_class($this), 'error_handler'));
        }
    }

    /**
     * 加载系统常量
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initConst(Yaf\Dispatcher $dispatcher)
    {
        date_default_timezone_set("Asia/Shanghai");
        defined('PUBLIC_PATH') or define('PUBLIC_PATH', ROOT_PATH . 'public/');        /* 入口目录 */
        defined('LOG_PATH') or define('LOG_PATH', ROOT_PATH . 'log/');       /* 日志目录 */
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('IS_DEBUG') or define('IS_DEBUG', false);
    }

    /**
     * 加载用户配置
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initConfig(Yaf\Dispatcher $dispatcher)
    {
        Yaf\Registry::set('config', Yaf\Application::app()->getConfig());
    }

    /**
     * 设置自定义请求对象
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initRequest(Yaf\Dispatcher $dispatcher)
    {
        $dispatcher->setRequest(new eYaf\Request());
    }

    /**
     * 加载插件
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initPlugins(Yaf\Dispatcher $dispatcher)
    {
        $config = Yaf\Application::app()->getConfig();
        if ($config->get('application.log.status')) {
            $dispatcher->registerPlugin(new LogPlugin());
        }
    }

    /**
     * 加载路由设置
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initRoute(Yaf\Dispatcher $dispatcher)
    {
        if(defined('CONFIG_PATH') && is_file(CONFIG_PATH . 'routing.ini')) {
            $config = new Yaf\Config\Ini(CONFIG_PATH . 'routing.ini');
            $dispatcher->getRouter()->addConfig($config);
        }
    }

    /**
     * Custom error handler.
     *
     * Catches all errors (not exceptions) and creates an ErrorException.
     * ErrorException then can caught by Yaf\ErrorController.
     *
     * @param integer $errno the error number.
     * @param string $errstr the error message.
     * @param string $errfile the file where error occured.
     * @param integer $errline the line of the file where error occured.
     *
     * @throws ErrorException
     */
    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) return;

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
