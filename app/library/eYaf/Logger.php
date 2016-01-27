<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace eYaf;

class Logger extends \SplFileObject
{
    const RED = '1;31m';
    const GREEN = '1;32m';
    const PURPLE = '1;35m';
    const CYAN = '1;36m';
    const WHITE = '1;37m';

    const RESET_SEQ = "\033[0m";
    const COLOR_SEQ = "\033[";
    const BOLD_SEQ = "\033[1m";

    private static $start_time;

    private static $memory;

    private static $logger_instance;

    public static function startLogging($env = null, $open_mode = "a")
    {
        self::$start_time = microtime(true);
        self::$memory = memory_get_usage(true);
        $buffer = self::COLOR_SEQ . self::GREEN
            . "Started at : [" . date('H:i:s d-m-Y', time()) . "]"
            . self::RESET_SEQ;
        static::getLogger($env, $open_mode)->log($buffer);
    }

    public static function stopLogging($env = null, $open_mode = "a")
    {
        $buffer = self::COLOR_SEQ . self::GREEN . "Completed in "
            . number_format((microtime(true) - self::$start_time) * 1000, 0)
            . "ms | "
            . "Mem Usage: ("
            . number_format((memory_get_usage(true) - self::$memory) / (1024), 0, ",", ".")
            . " kb)"
            . self::RESET_SEQ;
        static::getLogger($env, $open_mode)->log($buffer);
    }

    public static function getLogger($env = null, $open_mode = "a", $path = null)
    {
        $env = $env ?: \Yaf\ENVIRON;
        if (isset(static::$logger_instance[$env])) return static::$logger_instance[$env];
        $path = $path ?: LOG_PATH;
        $filename = $path . DS . $env . '.log';
        static::$logger_instance[$env] = new static($filename, $open_mode);
        return static::$logger_instance[$env];
    }

    public function __construct($filename = null, $open_mode = "a")
    {
        $filename = $filename ?: LOG_PATH . DS . \Yaf\ENVIRON . ".log";
        parent::__construct($filename, $open_mode);
    }

    public function log($string)
    {
        $this->fwrite($string . "\n");
    }

    public function errorLog($string)
    {
        $this->log(self::COLOR_SEQ . "1;37m" . "!! WARNING: " . $string . self::RESET_SEQ);
    }

    public function logQuery($query, $class_name = null, $parse_time = 0, $action = 'Load')
    {
        $class_name = $class_name ?: 'Sql';
        $buffer = self::COLOR_SEQ . self::PURPLE . "$class_name $action ("
            . number_format($parse_time * 1000, '4')
            . "ms)  " . self::RESET_SEQ . self::COLOR_SEQ . self::WHITE
            . $query . self::RESET_SEQ;

        $this->log($buffer);
    }

    public function logRequest($request)
    {
        $this->log("Processing "
            . $request->getModuleName() . '\\'
            . $request->getControllerName()
            . "Controller#"
            . $request->getActionName()
            . " (for {$request->getServer('REMOTE_ADDR')}"
            . " at " . date('Y-m-d H:i:s') . ")"
            . " [{$request->getMethod()}]"
        );
        $params = array();
        $params = array_merge($params,
            $request->getParams(),
            $request->getPost(),
            $request->getFiles(),
            $request->getQuery()
        );
        $this->log("Parameters: " . print_r($params, true));
    }

    public function logException($exception)
    {
        $this->log(
            get_class($exception) . ": "
            . $exception->getMessage()
            . " in file "
            . $exception->getFile()
            . " at line "
            . $exception->getLine()
        );
        $this->log($exception->getTraceAsString());
    }
}
