<?php

use Test\PHPUnit\ModelTestCase;

class UserTest extends ModelTestCase {

    public function testGetUserName() {
        $userId = 1;
        $result = \Dao\UserModel::test($userId);
        $this->assertArrayHasKey('group_id', $result);

        $userId = 100;
        $result = \Dao\UserModel::test($userId);
        $this->assertFalse($result);
    }

}