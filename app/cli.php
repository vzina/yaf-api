<?php
/**
 * 命令行执行PHP 入口
 * php ./app/cli.php "/cli/crontab/sendmailt?a=1&b=c"
 * php ./app/cli.php "/{模块名}/{控制器名}/{方法名}?{参数}"
 */
set_time_limit(0);
ini_set('memory_limit', '256M');
// define it for not auto running.
// like testcase, so that we can depand cli argv to choose which controller run it.
define('APPLICATION_NOT_RUN', true);
// Import application and bootstrap.
\Yaf\Loader::import(dirname(__FILE__) . '/../public/index.php');
$request = new \Yaf\Request\Simple();
// parse cli
global $argc, $argv;
if ($argc > 1) {
    $module = '';
    $uri = $argv [1];
    if (preg_match('/^[^?]*%/i', $uri)) {
        list ($module, $uri) = explode('%', $uri, 2);
    }
    $module = strtolower($module);
    $modules = \Yaf\Application::app()->getModules();
    if (in_array(ucfirst($module), $modules)) {
        $request->setModuleName($module);
    }
    if (false === strpos($uri, '?')) {
        $args = array();
    } else {
        list ($uri, $args) = explode('?', $uri, 2);
        parse_str($args, $args);
    }
    foreach ($args as $k => $v) {
        $request->setParam($k, $v);
    }
    $request->setRequestUri($uri);
    if ($request->isRouted() && !empty ($uri)) {
        if (false !== strpos($uri, '/')) {
            list ($controller, $action) = explode('/', $uri);
            $request->setActionName($action);
        } else {
            $controller = $uri;
        }
        $request->setControllerName(ucfirst(strtolower($controller)));
    }
}

// route uri => request
\Yaf\Dispatcher::getInstance()->getRouter()->route($request);
// dispatch this request
\Yaf\Dispatcher::getInstance()->dispatch($request);