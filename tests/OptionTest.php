<?php

namespace Phpoar\tests;

use Phpoar\NoneException;
use PHPUnit\Framework\TestCase;
use function Phpoar\{None, Some};

class OptionTest extends TestCase {

    /**
     * @throws \Throwable
     */
    public function test_unwrap() {
        $some = Some(2);
        $none = None();
        $this->assertEquals($some->unwrap(), 2);
        try {
            $none->unwrap();
        } catch (\Throwable $t) {
            $this->assertTrue($t instanceof NoneException);
        }
    }
}