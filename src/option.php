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