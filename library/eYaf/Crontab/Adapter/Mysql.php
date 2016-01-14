<?php
/**
 * Mysql.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/14
 * Time: 9:50
 */
/**
 CREATE TABLE IF NOT EXISTS `croon` (
 `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
 `time` char(30) NOT NULL,
 `command` varchar(255) NOT NULL,
 `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`),
 KEY `status` (`status`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8
 */
namespace eYaf\Crontab\Adapter;


use eYaf\Crontab\Adapter;
use Helper\Tools;

class Mysql extends Adapter
{

    protected $options = array(
        'table' => 'croon',
        'fields' => array('time', 'command')
    );

    private $count = null;


    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->count = $this->getCount();
    }

    public function fetch()
    {
        $rows = Tools::db()->result('SELECT * FROM ' . $this->options['table'] . ' WHERE `status`=1');
        $fields = $this->options['fields'];
        $tasks = array();

        foreach ($rows as $row) {
            $columns = array();
            foreach ($fields as $field) {
                $columns[$field] = $row[$field];
            }
            $tasks[] = join(' ', $columns);
        }

        return $tasks;
    }

    public function isModify()
    {
        $count = $this->getCount();
        if($this->count != $count){
            $this->count = $count;
            return true;
        }
        return false;
    }

    private function getCount()
    {
        return Tools::db()->col('SELECT COUNT(1) as cnt FROM ' . $this->options['table'] . ' WHERE `status`=1');
    }
}