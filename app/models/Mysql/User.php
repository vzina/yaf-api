<?php
/**
 * User.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 14:05
 */

namespace Mysql;


class UserModel extends AbstractModel
{
    protected $_table = 'documents';

    public function getUser($userId)
    {
        return $this->load($userId);
    }
}