<?php
/**
 * User.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/26
 * Time: 13:46
 */

namespace Dao;


class UserModel extends AbstractModel
{
    public static function test($userId)
    {
        $redis = static::redis();
        $user = $redis->find($userId);
        if (!$user) {
            $user = static::mysql()->getUser($userId);
            if ($user) $redis->update($userId, $user);
        }
        return $user;
    }
}
