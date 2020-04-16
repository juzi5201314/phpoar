<?php


namespace Phpoar\tests;

use Phpoar\ResultException;
use PHPUnit\Framework\TestCase;
use function Phpoar\{Err, None, Ok, Some};

class ResultTest extends TestCase {

    public function test_or() {
        $this->assertEquals(Ok(1)->or(Ok(2)), Ok(1));
        $this->assertEquals(Err(1)->or(Ok(2)), Ok(2));

        $this->assertEquals(Err(1)->or_else(function () {
            return Err(2);
        }), Err(2));
        $this->assertEquals(Ok(1)->or_else(function () {
            return Ok(2);
        }), Ok(1));
    }

    public function test_ok() {
        $this->assertEquals(Ok(null), Ok(None()));
        $ok1 = Ok(1);
        $this->assertTrue($ok1->is_ok());
        $this->assertFalse($ok1->is_err());
        $this->assertEquals($ok1->ok(), Some(1));
        $this->assertEquals($ok1->err(), None());
    }

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

        $this->assertEquals(Ok(1)->and_then(function ($x) {
            return Ok($x + 2);
        }), Ok(3));
    }

    /**
     * @throws \Throwable
     */
    public function test_unwrap() {
        $this->assertEquals(Ok("yes")->unwrap(), "yes");
        try {
            Err("oh no!")->unwrap();
        } catch (\Throwable $t) {
            $this->assertTrue($t instanceof ResultException);
        }

        $this->assertEquals(Ok(1)->unwrap_or(3), 1);
        $this->assertEquals(Err(1)->unwrap_or(3), 3);
    }

}