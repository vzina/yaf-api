<?php

/**
 * Test.php.
 * Author: yeweijian
 * E-mail: yeweijian@hoolai.com
 * Date: 2016/1/7
 * Time: 11:35
 */
class TestModel
{
    public function getTestName($userId)
    {
        if ($userId == 1) {
            return "iceup";
        }
        return false;
    }
}