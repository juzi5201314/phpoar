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
            if ($this->err instanceof \Throwable) {
                throw $this->err;
            } elseif ($this->err == None()) {
                throw new ResultException("Phpoar Result Exception. Err is empty.");
            } else {
                throw new ResultException("Phpoar Result Exception: \r\n" . var_export($this->err, true));
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
     * @return mixed <$T or $default>
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