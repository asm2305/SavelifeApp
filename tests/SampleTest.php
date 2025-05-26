<?php

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    public function testAddition()
    {
        $this->assertEquals(4, 2 + 2);
    }

    public function testSomethingFails()
    {
        $this->assertTrue(false, "هذا اختبار يفشل عمداً للتجربة");
    }
}
