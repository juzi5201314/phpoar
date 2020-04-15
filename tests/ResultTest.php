<?php


namespace Phpoar\tests;

use Phpoar\ResultException;
use PHPUnit\Framework\TestCase;
use function Phpoar\{Err, None, Ok, Some};

class ResultTest extends TestCase {

    /**
     * @throws \Throwable
     */
    public function test_ok() {
        $this->assertEquals(Ok(null)->unwrap(), None());
        $ok1 = Ok(1);
        $this->assertTrue($ok1->is_ok());
        $this->assertFalse($ok1->is_err());
        $this->assertEquals($ok1->ok(), Some(1));
        $this->assertEquals($ok1->err(), None());
    }

    /**
     * @throws \Throwable
     */
    public function test_map() {
        $ok = Ok(3);
        $this->assertEquals($ok->map(function ($i) {
            return $i * 3;
        }), Ok(9));

        $err = Err("err");
        $this->assertEquals($err->map(function ($i) {
            return $i * 3;
        }), Err("err"));

        // map_or
        $this->assertEquals($err->map_or("default", function ($n) {
            return $n;
        }), "default");

        // map_or_else
        $this->assertEquals($err->map_or_else(function () {
            return "default2";
        }, function ($n) {
            return $n;
        }), "default2");

        // map_err
        $this->assertEquals($err->map_err(function ($e) {
            return sprintf("map_%s", $e);
        }), Err("map_err"));
    }

    public function test_and() {
        $ok = Ok(1);
        $err = Err("err1");
        $this->assertEquals($ok->and($err), Err("err1"));

        $err = Err("err2");
        $ok = Ok(2);
        $this->assertEquals($err->and($ok), Err("err2"));

        $err = Err("err3");
        $err4 = Err("err4");
        $this->assertEquals($err->and($err4), Err("err3"));

        $ok = Ok("ok1");
        $ok233 = Ok(2333);
        $this->assertEquals($ok->and($ok233), Ok(2333));
    }

    /**
     * @throws \Throwable
     */
    public function test_unwrap() {
        $result = Ok("yes");
        $result2 = Err("oh no!");

        $this->assertEquals($result->unwrap(), "yes");

        try {
            $result2->unwrap();
        } catch (\Throwable $t) {
            $this->assertTrue($t instanceof ResultException);
        }
    }

}