<?php

namespace Phpoar;

abstract class Option {
    protected $val;

    /**
     * @return mixed | $val
     * @throws \Throwable
     */
    public function unwrap() {
        if ($this instanceof Some)
            return $this->val;
        else
            return Err(new NoneException("val is \'None\'"))->unwrap();
    }

    /**
     * @return bool
     */
    public function is_some(): bool {
        return $this instanceof Some;
    }

    /**
     * @return bool
     */
    public function is_none(): bool {
        return $this instanceof None;
    }

    /**
     * @param $optb
     * @return mixed | $val | $optb
     */
    public function unwrap_or($optb) {
        if ($this->is_some())
            return $this->val;
        else
            return $optb;
    }

    /**
     * @param callable $fn($val): $T
     * @return mixed | $T
     */
    public function map(callable $fn) {
        if ($this->is_some())
            return $fn($this->val);
        else
            return None();
    }

    /**
     * @param $fn(): $T
     * @return mixed | $val | $T
     */
    public function unwrap_or_else(callable $fn) {
        if ($this->is_some())
            return $this->val;
        else
            return $fn();
    }

    public function __toString() {
        return sprintf("Option: %s(%s)", basename(get_class($this)), $this->is_some() ? gettype($this->val): "");
    }

    public function __clone() {
        if (is_object($this->val))
            $this->val = clone $this->val;
    }

    public final static function _some($val): Option {
        if (is_null($val))
            return new None;
        return new Some($val);
    }

    public final static function _none(): Option {
        return new None;
    }
}

final class Some extends Option {
    function __construct($val) {
        $this->val = $val;
    }
}

final class None extends Option {
}

class NoneException extends \Exception {}