<?php

namespace Phpoar;

abstract class Result {
    protected $val;
    protected $err;

    /**
     * @return mixed <$val>
     * @throws \Throwable
     */
    public final function unwrap() {
        if ($this instanceof Ok)
            return $this->val;
        else
            $this->panic("called `Result::unwrap()` on an `Err` value: \r\n" . is_string($this->err) ? strval($this->err) : print_r($this->err, true));
        return null; // 跳过phpstorm的missing返回语句警告
    }

    /**
     * @param string $msg
     * @return mixed <$val>
     * @throws \Throwable
     */
    public final function expect(string $msg) {
        if ($this instanceof Ok)
            return $this->val;
        else
            $this->panic($msg);
        return null; // 跳过phpstorm的missing返回语句警告
    }

    /**
     * @param string $msg
     * @throws \Throwable
     */
    private final function panic(string $msg) {
        if ($this->err instanceof \Throwable) {
            throw $this->err;
        } else {
            throw new ResultException($msg);
        }
    }

    /**
     * @return bool
     */
    public function is_ok(): bool {
        return $this instanceof Ok;
    }

    /**
     * @return bool
     */
    public function is_err(): bool {
        return $this instanceof Err;
    }

    /**
     * @return Option<$val>
     */
    public function ok(): Option {
        return $this->is_ok() ? Some($this->val) : None();
    }

    /**
     * @return Option<$err>
     */
    public function err(): Option {
        return $this->is_err() ? Some($this->err) : None();
    }

    /**
     * @param callable $fn($val): $T
     * @return Result<$T, $err>
     */
    public function map(callable $fn): Result {
        if ($this->is_err())
            return Err($this->err);
        else
            return Ok($fn($this->val));
    }

    /**
     * @param mixed $default
     * @param callable $fn($val): $T
     * @return mixed <$T | $default>
     */
    public function map_or($default, callable $fn) {
        if ($this->is_err())
            return $default;
        else
            return $fn($this->val);
    }

    /**
     * @param callable $default(): $T
     * @param callable $fn($val): $T
     * @return mixed <$T>
     */
    public function map_or_else(callable $default, callable $fn) {
        if ($this->is_err())
            return $default();
        else
            return $fn($this->val);
    }

    /**
     * @param callable $fn($err): $E
     * @return Result<$val, $E>
     */
    public function map_err(callable $fn): Result {
        if ($this->is_ok())
            return Ok($this->val);
        else
            return Err($fn($this->err));
    }

    /**
     * @param Result $res<$U, $E>
     * @return Result<$U, $E>
     */
    public function and(Result $res): Result {
        if ($this->is_ok())
            return $res;
        else
            return Err($this->err);
    }

    /**
     * @param callable $fn($val): Result<$U, $E>
     * @return Result<$U, $E>
     */
    public function and_then(callable $fn): Result {
        if ($this->is_ok())
            return $fn($this->val);
        else
            return Err($this->err);
    }

    /**
     * @param Result $res
     * @return Result
     */
    public function or(Result $res): Result {
        if ($this->is_ok())
            return Ok($this->val);
        else
            return $res;
    }

    /**
     * @param callable $fn($err): Result
     * @return Result
     */
    public function or_else(callable $fn): Result {
        if ($this->is_ok())
            return Ok($this->val);
        else
            return $fn($this->err);
    }

    /**
     * @param $optb
     * @return mixed | $val | $optb
     */
    public function unwrap_or($optb) {
        if ($this->is_ok())
            return $this->val;
        else
            return $optb;
    }

    /**
     * @param callable $fn($err): $T
     * @return mixed | $val | $T
     */
    public function unwrap_or_else(callable $fn) {
        if ($this->is_ok())
            return $this->val;
        else
            return $fn($this->err);
    }

    public function __toString() {
        return sprintf("Result: %s(%s)", basename(get_class($this)), $this->is_ok() ? gettype($this->val): gettype($this->err));
    }

    public function __clone() {
        if (is_object($this->val))
            $this->val = clone $this->val;
        if (is_object($this->err))
            $this->err = clone $this->err;
    }

    public final static function _ok($val): Ok {
        return new Ok($val ?? None());
    }

    public final static function _err($err): Err {
        return new Err($err ?? None());
    }
}


final class Ok extends Result {
    function __construct($val) {
        $this->val = $val;
    }
}

final class Err extends Result {
    function __construct($err) {
        $this->err = $err;
    }
}

class ResultException extends \Exception {}