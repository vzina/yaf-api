<?php

namespace eYaf\Crontab\Adapter;

use eYaf\Crontab\Adapter;

/**
 * File Adapter
 */
class File extends Adapter
{
    private $path = null;
    private $filectime;

    public function __construct($options = array())
    {
        parent::__construct($options);
        if (!$this->path = stream_resolve_include_path($this->options['path'])) {
            throw new \InvalidArgumentException("Non-exist cron list file \"{$this->options['path']}\"");
        }
        $this->filectime = $this->getCtime();
    }

    public function fetch()
    {
        clearstatcache();
        $tasks = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $tasks;
    }

    public function isModify()
    {
        $filectime = $this->getCtime();
        if($filectime != $this->filectime){
            $this->filectime = $filectime;
            return true;
        }
        return false;
    }

    private function getCtime()
    {
        clearstatcache();
        return filectime($this->path);
    }
}
