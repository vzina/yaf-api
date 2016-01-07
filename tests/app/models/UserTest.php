<?php

use Test\PHPUnit\ModelTestCase;

class UserTest extends ModelTestCase {

    public function testGetUserName() {
        $model = new \TestModel();
        $userId = 1;
        $result = $model->getTestName($userId);
        $this->assertEquals('iceup', $result);

        $userId = 100;
        $result = $model->getTestName($userId);
        $this->assertFalse($result);
    }

}