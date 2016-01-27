<?php
/**
 *
 */
namespace eYaf\Db\Pdo;

class Mysql extends PDOAbstract
{
    protected function _dsn()
    {
        return "mysql:host=" . $this->_config['host'] . ";port=" . $this->_config['port'] . ";dbname=" . $this->_config['database'];
    }
}