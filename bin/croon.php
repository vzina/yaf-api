#!/usr/bin/env php
<?php

set_time_limit(0);
ini_set('memory_limit', '256M');
// define it for not auto running.
// like testcase, so that we can depand cli argv to choose which controller run it.
define('APPLICATION_NOT_RUN', true);
$_path = dirname(__DIR__);
require_once $_path . '/vendor/autoload.php';
// Import application and bootstrap.
\Yaf\Loader::import($_path . '/public/index.php');
unset($_path);
try {
    // Add arguments parse
    $parser = new \Pagon\ArgParser();
    $parser->add(array('--path', '-p'), array('help' => 'The path of cron list file'));
    $parser->add(array('--log', '-l'), array('help' => 'The file to log'));
    $parser->add(array('--boot', '-b'), array('help' => 'The file to bootstrap'));

    // Try to parse the arguments
    if (!$args = $parser->parse()) {
        print $parser->help();
        exit;
    }

    // Check path to bootstrap
    if ($args['boot'] && ((!$boot = realpath($args['boot'])) || !is_file($boot))) {
        exit('The given bootstrap file "' . $args['boot'] . '" is not exists');
    }

    $croonConfig = \Helper\Tools::getConfig('_croon', array(
        'adapter' => 'file',
        'params' => array(
            'path' => SYS_CONFIG_PATH . 'croon.list',
        ),
    ));

    if (is_object($croonConfig)) {
        $croonConfig = $croonConfig->toArray();
    }

    if ($args['log']) $croonConfig['log'] = array('file' => $args['log']);
    // Check path to run
    if (($path = realpath($args['path'])) && is_file($path)) {
        $croonConfig['adapter'] = 'file';
        $croonConfig['params'] = array('path' => $path,);
    }
    // Create croon object
    $croon = new \eYaf\Crontab\Croon($croonConfig);
    !empty($boot) && Yaf\Loader::import($boot);
    $croon->run();
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}