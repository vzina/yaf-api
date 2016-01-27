<?php
/**
 * Tasks.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/12
 * Time: 18:26
 */

namespace eYaf;


abstract class Tasks extends Controllers
{
    final public function init()
    {
        if (!APPLICATION_IS_CLI) {
            return $this->forwardTo404();
        }
        if (method_exists($this, '_init')) $this->_init();
    }
}