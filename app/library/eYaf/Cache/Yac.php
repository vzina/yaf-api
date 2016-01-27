<?php
namespace eYaf\Cache;


/**
 * use https://github.com/laruence/yac.git
 *
 */
class Yac extends CacheAbstract
{

    /**
     * Constructor
     *
     * @param array $options
     * @throws CacheException
     */
    public function __construct($options = array())
    {
        if (!extension_loaded("yac"))
            throw new CacheException('extension yac is not exist!');
        parent::__construct($options);
        $name = isset($this->options['name']) ? $this->options['name'] : 'yaf-cache';
        $this->conn = new \Yac($name);
    }
}