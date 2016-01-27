<?php
/**
 * UserModel.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 15:11
 */

namespace Redis\Db0;


use Redis\AbstractModel;

class UserModel extends AbstractModel
{
    protected $_keyPrefix = 'user_';

    protected $_db = 0;

}