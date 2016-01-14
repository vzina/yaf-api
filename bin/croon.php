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
try {
    // Add arguments parse
    $parser = new \Pagon\ArgParser();
    $parser->add('type', array('help' => 'The path of cron list file', 'default' => 'file'));
    $parser->add(array('--log', '-l'), array('help' => 'The file to log', 'default' => 'croon.log'));
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

    $croonConfig = \Helper\Tools::getConfig('_croon', array());

    if (is_object($croonConfig)) {
        $croonConfig = $croonConfig->toArray();
    }
    $croonConfig = array_merge(array(
        'adapter' => 'file',
        'params' => array(
            'path' => $_path . '/config/croon.list'
        ),
        'log' => array(
            'file' => $args['log']
        )
    ), $croonConfig);
    // Create croon object
    $croon = new \eYaf\Crontab\Croon($croonConfig);

    unset($_path);
    !empty($boot) && Yaf\Loader::import($boot);
    $croon->run();
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}