<?php
namespace eYaf\Cache;


class File extends CacheAbstract
{
    protected $options = array(
        'cache_dir' => 'cache',
        'cache_dir_depth' => 1,
        'cache_md5_code' => ''
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->options['cache_dir'] = '/tmp/cache';
        $this->options = $options + $this->options;
        $this->options['cache_dir'] = rtrim($this->options['cache_dir'], '\\/');
    }

    /**
     * Set cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return boolean
     */
    public function set($key, $value)
    {
        if (!is_string($value)) $value = serialize($value);
        $file = $this->_file($key);
        $dir = dirname($file);
        is_dir($dir) || mkdir($dir, 0755);
        return file_put_contents($file, $value) ? true : false;
    }

    /**
     * Get Cache Value
     *
     * @param mixed $id
     * @return mixed
     */
    public function get($key)
    {
        $file = $this->_file($key);
        return is_file($file) ? file_get_contents($file) : false;
    }

    /**
     * Delete cache
     * @param string $id
     * @return boolean
     */
    public function delete($key)
    {
        $file = $this->_file($key);
        return is_file($file) ? unlink($file) : true;
    }

    /**
     * Get file by key
     *
     * @param string $key
     * @return string
     */
    protected function _file($key)
    {
        $md5 = md5($key . $this->options['cache_md5_code']);
        $dir = $this->options['cache_dir'];
        for ($i = 0; $i < $this->options['cache_dir_depth']; $i++) {
            $dir .= DIRECTORY_SEPARATOR . substr($md5, ($i - 1) * 2, 2);
        }

        $file = $dir . DIRECTORY_SEPARATOR . $md5 . '.tmp';

        return $file;
    }
}